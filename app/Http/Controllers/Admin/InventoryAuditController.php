<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\LpoItem;
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\StockMovement;
use App\Models\Transfer;
use Illuminate\Http\Request;

class InventoryAuditController extends Controller
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

    /**
     * Legacy product reconciliation — items received/ordered without product
     * mapping. Manual mapping only: admin picks the product, we audit it.
     */
    public function reconciliation()
    {
        $unmappedLpoItems = LpoItem::withoutGlobalScopes()
            ->whereNull('product_id')
            ->whereHas('lpo', fn($q) => $q->where('company_id', auth()->user()->company_id))
            ->with('lpo:id,lpo_number')
            ->latest('id')->paginate(20);

        $unmappedGrnItems = GrnItem::withoutGlobalScopes()
            ->whereNull('product_id')
            ->whereHas('grn', fn($q) => $q->where('company_id', auth()->user()->company_id))
            ->with('grn:id,grn_number,warehouse_id,status')
            ->latest('id')->limit(50)->get();

        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'product_code']);

        // Products missing warehouse assignment — POS cannot sell these
        $unmappedProducts = Product::withoutGlobalScopes()
            ->where('company_id', auth()->user()->company_id)
            ->where('is_active', true)->whereNull('warehouse_id')
            ->orderBy('name')->limit(100)->get();

        $warehouses = \App\Models\Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.inventory.reconciliation', compact(
            'unmappedLpoItems', 'unmappedGrnItems', 'products', 'unmappedProducts', 'warehouses'
        ));
    }

    /**
     * Assign a warehouse to a product — the authorized reconciliation for
     * products blocked from POS checkout. Company-validated and audited.
     */
    public function assignWarehouse(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', \Illuminate\Validation\Rule::exists('products','id')->where('company_id', auth()->user()->company_id)],
            'warehouse_id' => ['required', \Illuminate\Validation\Rule::exists('warehouses','id')->where('company_id', auth()->user()->company_id)],
            'reason' => 'required|string|max:255',
        ]);

        $product = Product::withoutGlobalScopes()->findOrFail($data['product_id']);
        abort_unless(is_null($product->warehouse_id), 403, 'This product already has a warehouse — use a controlled change process.');

        $product->update(['warehouse_id' => $data['warehouse_id']]);

        AuditLog::log('update', 'inventory', [
            'module_action' => 'product_warehouse_assigned',
            'old_values' => ['product' => $product->name, 'warehouse_id' => null],
            'new_values' => ['warehouse_id' => $data['warehouse_id'], 'reason' => $data['reason']],
        ]);

        return back()->with('success', "Warehouse assigned to {$product->name}.");
    }

    /**
     * Map a single legacy item to a product — the only sanctioned backfill.
     * Items whose GRN already posted stock cannot be remapped here.
     */
    public function mapProduct(Request $request)
    {
        $data = $request->validate([
            'item_type' => 'required|in:lpo_item,grn_item',
            'item_id' => 'required|integer',
            'product_id' => ['required', \Illuminate\Validation\Rule::exists('products','id')->where('company_id', auth()->user()->company_id)],
            'reason' => 'required|string|max:255',
        ]);

        $model = $data['item_type'] === 'lpo_item'
            ? LpoItem::withoutGlobalScopes()->with('lpo')->findOrFail($data['item_id'])
            : GrnItem::withoutGlobalScopes()->with('grn')->findOrFail($data['item_id']);

        // Company check — only items in the user's own company
        $parent = $data['item_type'] === 'lpo_item' ? $model->lpo : $model->grn;
        abort_unless($parent && $parent->company_id === auth()->user()->company_id, 403, 'Item belongs to another company.');
        abort_unless(is_null($model->product_id), 403, 'This item already has a product mapping.');

        // If the GRN already posted stock for this item, remapping would
        // rewrite history — require a corrective adjustment instead.
        if ($data['item_type'] === 'grn_item' && $parent->warehouse_id && $parent->status === 'received') {
            return back()->with('error', 'This GRN already posted stock — remapping requires a corrective stock adjustment, not a silent re-map.');
        }

        $model->update(['product_id' => $data['product_id']]);

        AuditLog::log('update', 'inventory', [
            'module_action' => 'product_mapping_reconciled',
            'old_values' => ['item' => "{$data['item_type']}#{$data['item_id']}", 'product_id' => null],
            'new_values' => ['product_id' => $data['product_id'], 'reason' => $data['reason']],
        ]);

        return back()->with('success', 'Product mapping recorded.');
    }

    /**
     * Manually release an outstanding reservation — the authorized
     * reconciliation path for stale reservations. Floored at zero and
     * idempotent: releasing an already-released row is a no-op.
     */
    public function releaseReservation(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', \Illuminate\Validation\Rule::exists('products','id')->where('company_id', auth()->user()->company_id)],
            'warehouse_id' => ['required', \Illuminate\Validation\Rule::exists('warehouses','id')->where('company_id', auth()->user()->company_id)],
            'reason' => 'required|string|max:255',
        ]);

        $released = \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $stock = ProductWarehouseStock::withoutGlobalScopes()
                ->where('product_id', $data['product_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $amount = $stock->reserved_quantity;
            if ($amount > 0) {
                $stock->update(['reserved_quantity' => 0]);
            }
            return $amount;
        });

        AuditLog::log('update', 'inventory', [
            'module_action' => 'reservation_released',
            'new_values' => [
                'product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id'],
                'released' => $released, 'reason' => $data['reason'],
            ],
        ]);

        return back()->with('success', $released > 0 ? "Released {$released} reserved units." : 'No outstanding reservation — nothing to release.');
    }

    /**
     * Inventory audit — read-only discrepancy report.
     * Verified discrepancies vs missing-history are distinguished.
     */
    public function auditReport()
    {
        $companyId = auth()->user()->company_id;

        // 1. Negative stock balances (defect) — scope via warehouse company
        $negative = ProductWarehouseStock::withoutGlobalScopes()
            ->where('quantity', '<', 0)
            ->whereHas('warehouse', fn($q) => $q->where('company_id', $companyId))
            ->with('product:id,name', 'warehouse:id,name')->get();

        // 2. Received GRNs with a warehouse but NO matching stock movement
        //    (verified discrepancy — posting was required and missing)
        $receivedGrns = Grn::withoutGlobalScopes()
            ->where('company_id', $companyId)->where('status', 'received')
            ->whereNotNull('warehouse_id')->get();
        $missingMovement = $receivedGrns->filter(function ($grn) {
            return !StockMovement::withoutGlobalScopes()
                ->where('warehouse_id', $grn->warehouse_id)
                ->where('reference', 'like', "GRN {$grn->grn_number}%")->exists();
        })->values();

        // 3. Duplicate source-linked movements (same reference counted twice+)
        $duplicates = StockMovement::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->select('reference', \DB::raw('count(*) as cnt'))
            ->whereNotNull('reference')
            ->groupBy('reference')
            ->havingRaw('count(*) > 2') // transfers legitimately create 2 rows
            ->get();

        // 4. Completed transfers without movements (missing-history flag)
        $completedTransfers = Transfer::withoutGlobalScopes()
            ->where('company_id', $companyId)->where('status', 'completed')->whereNotNull('product_id')->get();
        $transferMissing = $completedTransfers->filter(function ($t) {
            return !StockMovement::withoutGlobalScopes()
                ->where('reference', 'like', "Transfer #{$t->id}%")->exists();
        })->values();

        // 5. Unmapped items count (reconciliation queue)
        $unmappedCount = LpoItem::withoutGlobalScopes()->whereNull('product_id')
            ->whereHas('lpo', fn($q) => $q->where('company_id', $companyId))->count()
            + GrnItem::withoutGlobalScopes()->whereNull('product_id')
            ->whereHas('grn', fn($q) => $q->where('company_id', $companyId))->count();

        // 6. On-hand / reserved / available snapshot
        $stockSnapshot = ProductWarehouseStock::withoutGlobalScopes()
            ->whereHas('warehouse', fn($q) => $q->where('company_id', $companyId))
            ->with('product:id,name,product_code', 'warehouse:id,name')
            ->orderBy('product_id')->get()
            ->map(fn($s) => [
                'product' => $s->product?->name,
                'warehouse' => $s->warehouse?->name,
                'on_hand' => $s->quantity,
                'reserved' => $s->reserved_quantity,
                'available' => $s->quantity - $s->reserved_quantity,
            ]);

        // 7. Transfers with inconsistent quantity state (transferred > requested)
        $inconsistent = Transfer::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereColumn('transferred_quantity', '>', 'quantity')->get();

        // 8. Potentially stale reservations — reserved > 0 with no open
        //    transfer claiming that product/warehouse. No expiry policy
        //    exists (documented): these require manual review, not auto-release.
        $openTransfers = Transfer::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereIn('status', ['approved', 'partially_transferred'])
            ->whereNotNull('product_id')
            ->get(['product_id', 'from_warehouse', 'quantity', 'transferred_quantity', 'status', 'id']);
        $staleReservations = ProductWarehouseStock::withoutGlobalScopes()
            ->where('reserved_quantity', '>', 0)
            ->whereHas('warehouse', fn($q) => $q->where('company_id', $companyId))
            ->with('product:id,name', 'warehouse:id,name')
            ->get()
            ->filter(fn($s) => !$openTransfers->contains(
                fn($t) => (int) $t->product_id === (int) $s->product_id && (int) $t->from_warehouse === (int) $s->warehouse_id
            ))
            ->values();

        // 9. Active reservations without source attribution (legacy)
        $unattributed = \App\Models\StockReservation::withoutGlobalScopes()
            ->where('company_id', $companyId)->where('status', 'active')
            ->whereNull('transfer_id')->with('product:id,name', 'warehouse:id,name')->get();

        // 10. Active reservation records whose transfer is no longer open
        $terminalLinked = \App\Models\StockReservation::withoutGlobalScopes()
            ->where('company_id', $companyId)->where('status', 'active')
            ->whereNotNull('transfer_id')
            ->whereHas('transfer', fn($q) => $q->withoutGlobalScopes()->whereNotIn('status', ['approved','partially_transferred']))
            ->with('transfer:id,status', 'product:id,name')->get();

        // 11. POS sales missing expected stock movements
        $posMissing = \App\Models\PosSale::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereDoesntHave('items', fn($q) => $q->whereNull('product_id'))
            ->latest('id')->limit(100)->get()
            ->filter(fn($s) => $s->items->some(fn($i) => $i->product_id) &&
                !StockMovement::withoutGlobalScopes()->where('reference', 'like', "POS {$s->sale_number}%")->exists()
            )->values();

        // 12. Transfers with missing or inconsistent company
        $transfersNoCompany = Transfer::withoutGlobalScopes()
            ->whereNull('company_id')->with('fromWarehouse:id,name,company_id', 'toWarehouse:id,name,company_id')
            ->get();
        $transfersMismatch = Transfer::withoutGlobalScopes()
            ->whereNotNull('company_id')
            ->whereHas('fromWarehouse', fn($q) => $q->withoutGlobalScopes()->whereColumn('warehouses.company_id', '!=', 'transfers.company_id'))
            ->with('fromWarehouse:id,name,company_id')->get();

        // 13. Damaged returns (non-stock disposition — traceability only)
        $damagedReturns = \App\Models\PosReturnItem::withoutGlobalScopes()
            ->where('disposition', 'damaged')
            ->whereHas('posReturn', fn($q) => $q->withoutGlobalScopes()->where('status', 'processed')->where('company_id', $companyId))
            ->with('saleItem:id,product_name', 'posReturn:id,return_number')
            ->latest('id')->limit(50)->get();

        return view('admin.inventory.audit', compact(
            'negative', 'missingMovement', 'duplicates', 'transferMissing', 'unmappedCount',
            'stockSnapshot', 'inconsistent', 'staleReservations',
            'unattributed', 'terminalLinked', 'posMissing',
            'transfersNoCompany', 'transfersMismatch', 'damagedReturns'
        ));
    }

    /**
     * Reconcile a transfer's company_id — set it to the consistent
     * company proven by its warehouses. Ambiguous records (warehouses
     * in different companies) are flagged, never guessed.
     */
    public function reconcileTransferCompany(Request $request)
    {
        $data = $request->validate([
            'transfer_id' => 'required|exists:transfers,id',
            'reason' => 'required|string|max:255',
        ]);

        $transfer = Transfer::withoutGlobalScopes()->with(['fromWarehouse', 'toWarehouse'])->findOrFail($data['transfer_id']);
        abort_unless(is_null($transfer->company_id), 403, 'This transfer already has a company — use a controlled correction.');

        $fromCo = $transfer->fromWarehouse?->company_id;
        $toCo = $transfer->toWarehouse?->company_id;

        // Only reconcile when the evidence is unambiguous
        if (!$fromCo || !$toCo || $fromCo !== $toCo) {
            return back()->with('error', "Transfer #{$transfer->id} is ambiguous — warehouses belong to different or missing companies. Manual review required.");
        }
        abort_unless($fromCo === auth()->user()->company_id, 403, 'This transfer belongs to another company.');

        $transfer->update(['company_id' => $fromCo]);

        AuditLog::log('update', 'inventory', [
            'module_action' => 'transfer_company_reconciled',
            'old_values' => ['transfer' => $transfer->id, 'company_id' => null],
            'new_values' => ['company_id' => $fromCo, 'reason' => $data['reason']],
        ]);

        return back()->with('success', "Transfer #{$transfer->id} assigned to company from warehouse evidence.");
    }
    }
}
