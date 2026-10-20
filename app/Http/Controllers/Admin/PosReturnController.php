<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosReturn;
use App\Models\PosSale;
use App\Services\PosReturnService;
use Illuminate\Http\Request;

class PosReturnController extends Controller
{
    public function __construct(private PosReturnService $returns)
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
        $data = $request->validate([
            'reason' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.pos_sale_item_id' => 'required|exists:pos_sale_items,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.disposition' => 'required|in:restock,damaged',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $return = $this->returns->create($posSale, $data['items'], $data['reason'], $data['refund_amount'] ?? 0, auth()->user());

        return back()->with('success', "Return {$return->return_number} submitted for processing.");
    }

    /**
     * Process a return — restock eligible items, record refund.
     * Different user from creator (segregation of duties). Idempotent.
     */
    public function process(PosReturn $posReturn)
    {
        $this->returns->process($posReturn, auth()->user());
        return back()->with('success', "Return {$posReturn->return_number} processed.");
    }

    /**
     * Confirm a refund — manual reconciliation. No external refund is
     * claimed without evidence (no payment provider is configured).
     */
    public function confirmRefund(Request $request, PosReturn $posReturn)
    {
        $data = $request->validate([
            'refund_reference' => 'nullable|string|max:255',
            'refund_notes' => 'nullable|string|max:500',
        ]);

        $this->returns->confirmRefund($posReturn, auth()->user(), $data['refund_reference'] ?? null, $data['refund_notes'] ?? null);

        return back()->with('success', "Refund for {$posReturn->return_number} confirmed.");
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
