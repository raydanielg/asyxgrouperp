<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\PosReturn;
use App\Models\PosReturnItem;
use App\Models\PosSale;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Shared POS return/refund logic — used by both the web admin
 * controller and the API. Single source of truth for validation,
 * stock restoration, and refund state transitions.
 */
class PosReturnService
{
    public function __construct(private InventoryService $inventory) {}

    /**
     * Create a pending return — validates sale-item ownership and
     * the cumulative return cap (pending + processed).
     */
    public function create(PosSale $sale, array $items, string $reason, float $refundAmount, User $actor): PosReturn
    {
        abort_unless($sale->company_id === $actor->company_id, 403, 'This sale belongs to another company.');

        return DB::transaction(function () use ($sale, $items, $reason, $refundAmount, $actor) {
            $locked = PosSale::lockForUpdate()->with('items')->findOrFail($sale->id);

            $return = PosReturn::create([
                'return_number' => 'RTN-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'pos_sale_id' => $locked->id,
                'status' => 'pending',
                'reason' => $reason,
                'refund_amount' => $refundAmount,
                'created_by' => $actor->id,
            ]);

            foreach ($items as $line) {
                $saleItem = $locked->items->firstWhere('id', $line['pos_sale_item_id']);
                abort_unless($saleItem, 422, "Sale item #{$line['pos_sale_item_id']} does not belong to this sale.");

                $alreadyReturned = PosReturnItem::whereHas('posReturn', fn($q) =>
                    $q->where('pos_sale_id', $locked->id)->whereIn('status', ['pending', 'processed']))
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
                'new_values' => ['return' => $return->return_number, 'sale' => $locked->sale_number],
            ]);

            return $return;
        });
    }

    /**
     * Process a return — restock eligible items + record refund intent.
     * Segregation of duties: creator cannot process. Idempotent.
     */
    public function process(PosReturn $return, User $actor): void
    {
        abort_if($return->status === 'processed', 403, 'This return is already processed.');
        abort_if($return->created_by === $actor->id, 403, 'You cannot process a return you requested.');

        DB::transaction(function () use ($return, $actor) {
            $locked = PosReturn::lockForUpdate()->findOrFail($return->id);
            abort_if($locked->status === 'processed', 403, 'This return is already processed.');

            $sale = PosSale::lockForUpdate()->findOrFail($locked->pos_sale_id);

            foreach ($locked->items as $item) {
                if ($item->disposition !== 'restock' || !$item->product_id) {
                    continue; // damaged — no stock restoration
                }
                $product = Product::lockForUpdate()->find($item->product_id);
                if (!$product || !$product->warehouse_id) {
                    abort(422, "Cannot restock '{$item->saleItem?->product_name}' — no warehouse assigned.");
                }
                $this->inventory->addStock(
                    $item->product_id, $product->warehouse_id, $item->quantity,
                    "POS_RETURN {$locked->return_number}", $actor->id
                );
            }

            if ($locked->refund_amount > 0) {
                $priorRefunds = PosReturn::where('pos_sale_id', $sale->id)
                    ->where('status', 'processed')
                    ->whereIn('refund_status', ['awaiting_reconciliation', 'confirmed', 'processed'])
                    ->sum('refund_amount');
                if ($priorRefunds + $locked->refund_amount > (float) $sale->paid_amount + 0.0001) {
                    abort(422, "Refund of {$locked->refund_amount} exceeds remaining refundable amount (paid {$sale->paid_amount}, refunded {$priorRefunds}).");
                }
            }

            $locked->update([
                'status' => 'processed',
                // Internal record only — settlement is not externally confirmed
                'refund_status' => $locked->refund_amount > 0 ? 'awaiting_reconciliation' : 'none',
                'processed_by' => $actor->id,
                'processed_at' => now(),
            ]);

            AuditLog::log('execute', 'pos', [
                'module_action' => 'pos_return_processed',
                'new_values' => ['return' => $locked->return_number, 'refund' => $locked->refund_amount],
            ]);
        });
    }

    /**
     * Confirm a refund — manual reconciliation step. Creator cannot
     * confirm their own return's refund. Idempotent.
     */
    public function confirmRefund(PosReturn $return, User $actor, ?string $reference, ?string $notes): void
    {
        abort_if($return->status !== 'processed', 403, 'Only processed returns can have refunds confirmed.');
        abort_if($return->refund_status === 'none', 403, 'This return has no refund to confirm.');
        abort_if($return->refund_status === 'confirmed', 403, 'This refund is already confirmed.');
        abort_if($return->created_by === $actor->id, 403, 'You cannot confirm a refund on a return you requested.');

        DB::transaction(function () use ($return, $actor, $reference, $notes) {
            $locked = PosReturn::lockForUpdate()->findOrFail($return->id);
            abort_if($locked->refund_status === 'confirmed', 403, 'This refund is already confirmed.');

            $locked->update([
                'refund_status' => 'confirmed',
                'refund_confirmed_by' => $actor->id,
                'refund_confirmed_at' => now(),
                'refund_reference' => $reference,
                'refund_notes' => $notes,
            ]);

            AuditLog::log('execute', 'pos', [
                'module_action' => 'pos_refund_confirmed',
                'new_values' => ['return' => $locked->return_number, 'amount' => $locked->refund_amount, 'reference' => $reference],
            ]);
        });
    }
}
