<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PosReturn;
use App\Models\PosReturnItem;
use App\Models\PosSale;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized access. Admin only.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $returns = PosReturn::with(['sale:id,sale_number', 'creator:id,name', 'items'])
            ->latest()->paginate(20);
        $sales = PosSale::latest('id')->limit(50)->get(['id', 'sale_number', 'total_amount', 'status']);
        return view('admin.pos.returns', compact('returns', 'sales'));
    }

    /**
     * Create a pending return against a sale.
     * Items: items[sale_item_id] = ['quantity' => n, 'disposition' => restock|damaged]
     */
    public function store(Request $request, PosSale $posSale)
    {
        $user = auth()->user();
        abort_unless($posSale->company_id === $user->company_id, 403, 'This sale belongs to another company.');

        $data = $request->validate([
            'reason' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.pos_sale_item_id' => 'required|exists:pos_sale_items,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.disposition' => 'required|in:restock,damaged',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($posSale, $data, $user) {
            $sale = PosSale::lockForUpdate()->with('items')->findOrFail($posSale->id);

            $return = PosReturn::create([
                'return_number' => 'RTN-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'pos_sale_id' => $sale->id,
                'status' => 'pending',
                'reason' => $data['reason'],
                'refund_amount' => $data['refund_amount'] ?? 0,
                'created_by' => $user->id,
            ]);

            foreach ($data['items'] as $line) {
                $saleItem = $sale->items->firstWhere('id', $line['pos_sale_item_id']);
                abort_unless($saleItem, 422, "Sale item #{$line['pos_sale_item_id']} does not belong to this sale.");

                // Reject returns exceeding remaining sold quantity
                $alreadyReturned = PosReturnItem::whereHas('posReturn', fn($q) =>
                    $q->where('pos_sale_id', $sale->id)->whereIn('status', ['pending', 'processed']))
                    ->where('pos_sale_item_id', $saleItem->id)->sum('quantity');
                if ($alreadyReturned + $line['quantity'] > $saleItem->quantity + 0.0001) {
                    abort(422, "Return quantity for '{$saleItem->product_name}' exceeds remaining sold quantity ({$saleItem->quantity} sold, {$alreadyReturned} already returned).");
                }

                PosReturnItem::create([
                    'pos_return_id' => $return->id,
                    'pos_sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'quantity' => $line['quantity'],
                    'disposition' => $line['disposition'],
                    'unit_price' => $saleItem->unit_price,
                ]);
            }

            AuditLog::log('create', 'pos', [
                'module_action' => 'pos_return_requested',
                'new_values' => ['return' => $return->return_number, 'sale' => $sale->sale_number],
            ]);

            return back()->with('success', "Return {$return->return_number} submitted for processing.");
        });
    }

    /**
     * Process a return — restock eligible items, record refund.
     * Different user from creator (segregation of duties). Idempotent.
     */
    public function process(PosReturn $posReturn)
    {
        $user = auth()->user();
        abort_if($posReturn->status === 'processed', 403, 'This return is already processed.');
        abort_if($posReturn->created_by === $user->id, 403, 'You cannot process a return you requested.');

        try {
            DB::transaction(function () use ($posReturn, $user) {
                $locked = PosReturn::lockForUpdate()->findOrFail($posReturn->id);
                abort_if($locked->status === 'processed', 403, 'This return is already processed.');

                $sale = PosSale::lockForUpdate()->findOrFail($locked->pos_sale_id);
                $inv = app(InventoryService::class);

                foreach ($locked->items as $item) {
                    if ($item->disposition !== 'restock' || !$item->product_id) {
                        continue; // damaged — no stock restoration (no quarantine schema)
                    }
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if (!$product || !$product->warehouse_id) {
                        abort(422, "Cannot restock '{$item->saleItem?->product_name}' — no warehouse assigned.");
                    }
                    $inv->addStock(
                        $item->product_id, $product->warehouse_id, $item->quantity,
                        "POS_RETURN {$locked->return_number}", $user->id
                    );
                }

                // Refund guard — cumulative refunds cannot exceed paid amount
                if ($locked->refund_amount > 0) {
                    $priorRefunds = PosReturn::where('pos_sale_id', $sale->id)
                        ->where('status', 'processed')->sum('refund_amount');
                    if ($priorRefunds + $locked->refund_amount > (float) $sale->paid_amount + 0.0001) {
                        abort(422, "Refund of {$locked->refund_amount} exceeds remaining refundable amount (paid {$sale->paid_amount}, refunded {$priorRefunds}).");
                    }
                }

                $locked->update([
                    'status' => 'processed',
                    'refund_status' => $locked->refund_amount > 0 ? 'processed' : 'none',
                    'processed_by' => $user->id,
                    'processed_at' => now(),
                ]);

                AuditLog::log('execute', 'pos', [
                    'module_action' => 'pos_return_processed',
                    'new_values' => ['return' => $locked->return_number, 'refund' => $locked->refund_amount],
                ]);
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Return {$posReturn->return_number} processed.");
    }

    /**
     * Reject/delete a pending return — processed returns are immutable.
     */
    public function destroy(PosReturn $posReturn)
    {
        abort_if($posReturn->status === 'processed', 403, 'Processed returns are immutable — use a corrective return instead.');
        $posReturn->items()->delete();
        $posReturn->delete();
        return back()->with('success', 'Return removed.');
    }
}
