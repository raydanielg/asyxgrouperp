<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostAllocation;
use App\Models\CostCenter;
use App\Models\Expense;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('view-cost-centers'))) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $costCenters = CostCenter::withCount('allocations')->latest()->paginate(20);
        return view('admin.cost-centers.index', compact('costCenters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:cost_centers,code',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['company_id'] = auth()->user()?->company_id;
        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        CostCenter::create($validated);

        return back()->with('success', 'Cost center created.');
    }

    public function update(Request $request, CostCenter $costCenter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:cost_centers,code,' . $costCenter->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $costCenter->update($validated);

        return back()->with('success', 'Cost center updated.');
    }

    public function destroy(CostCenter $costCenter)
    {
        $costCenter->delete();
        return back()->with('success', 'Cost center deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  ALLOCATE COSTS TO EXPENSES
    // ═══════════════════════════════════════════════════════

    public function storeAllocations(Request $request)
    {
        $validated = $request->validate([
            'allocatable_type' => 'required|string',
            'allocatable_id' => 'required|integer',
            'allocations' => 'required|array|min:1',
            'allocations.*.cost_center_id' => 'required|exists:cost_centers,id',
            'allocations.*.amount' => 'required|numeric|min:0',
            'allocations.*.percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $modelClass = $validated['allocatable_type'];
        $model = $modelClass::findOrFail($validated['allocatable_id']);

        // Delete existing allocations
        $model->costAllocations()->delete();

        $totalAllocated = 0;
        foreach ($validated['allocations'] as $alloc) {
            $percentage = $alloc['percentage'] ?? (($alloc['amount'] / $model->amount) * 100);
            $model->costAllocations()->create([
                'company_id' => $model->company_id ?? auth()->user()?->company_id,
                'cost_center_id' => $alloc['cost_center_id'],
                'amount' => $alloc['amount'],
                'percentage' => $percentage,
            ]);
            $totalAllocated += $alloc['amount'];
        }

        return back()->with('success', 'Cost allocations saved. Allocated ' . number_format($totalAllocated, 2) . ' / ' . number_format($model->amount, 2));
    }

    // ═══════════════════════════════════════════════════════
    //  REPORTS
    // ═══════════════════════════════════════════════════════

    public function report(Request $request)
    {
        $costCenterId = $request->input('cost_center_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = CostAllocation::with(['costCenter', 'costAllocatable']);

        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }
        if ($dateFrom) {
            $query->whereHasMorph('costAllocatable', [Expense::class], function ($q) use ($dateFrom) {
                $q->whereDate('expense_date', '>=', $dateFrom);
            });
        }
        if ($dateTo) {
            $query->whereHasMorph('costAllocatable', [Expense::class], function ($q) use ($dateTo) {
                $q->whereDate('expense_date', '<=', $dateTo);
            });
        }

        $allocations = $query->latest()->paginate(50);

        $summary = CostAllocation::selectRaw('cost_center_id, SUM(amount) as total')
            ->with('costCenter')
            ->groupBy('cost_center_id')
            ->orderByDesc('total')
            ->get();

        $costCenters = CostCenter::where('is_active', true)->get();

        return view('admin.cost-centers.report', compact('allocations', 'summary', 'costCenters', 'costCenterId', 'dateFrom', 'dateTo'));
    }

    public function getCenters()
    {
        return response()->json(CostCenter::where('is_active', true)->get());
    }

    public function allocationIndex()
    {
        $allocations = CostAllocation::with(['costCenter', 'costAllocatable'])
            ->latest()
            ->paginate(25);
        $costCenters = CostCenter::where('is_active', true)->get();
        return view('admin.cost-centers.allocations', compact('allocations', 'costCenters'));
    }
}
