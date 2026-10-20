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

        return view('admin.inventory.reconciliation', compact('unmappedLpoItems', 'unmappedGrnItems', 'products'));
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

        return view('admin.inventory.audit', compact(
            'negative', 'missingMovement', 'duplicates', 'transferMissing', 'unmappedCount'
        ));
    }
}
