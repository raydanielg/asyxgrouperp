<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FixedAsset;
use App\Models\DepreciationRecord;
use Illuminate\Http\Request;

class FixedAssetController extends Controller
{
    public function index()
    {
        $assets = FixedAsset::with(['assignedTo', 'company'])->latest()->paginate(20);
        return view('admin.fixed-assets.index', compact('assets'));
    }

    public function create()
    {
        return view('admin.fixed-assets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_number' => 'required|string|unique:fixed_assets,asset_number',
            'asset_tag' => 'nullable|string|unique:fixed_assets,asset_tag',
            'name' => 'required|string',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1',
            'depreciation_method' => 'nullable|string',
            'location' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|string',
        ]);

        $validated['salvage_value'] = $validated['salvage_value'] ?? 0;
        $validated['net_book_value'] = $validated['acquisition_cost'] - $validated['salvage_value'];
        $validated['company_id'] = auth()->user()?->company_id;
        $validated['created_by'] = auth()->id();

        FixedAsset::create($validated);

        return redirect()->route('admin.fixed-assets.index')->with('success', 'Fixed asset registered.');
    }

    public function show(FixedAsset $fixedAsset)
    {
        $fixedAsset->load(['assignedTo', 'depreciationRecords']);
        return view('admin.fixed-assets.show', compact('fixedAsset'));
    }

    public function edit(FixedAsset $fixedAsset)
    {
        return view('admin.fixed-assets.edit', compact('fixedAsset'));
    }

    public function update(Request $request, FixedAsset $fixedAsset)
    {
        $validated = $request->validate([
            'asset_number' => 'required|string|unique:fixed_assets,asset_number,' . $fixedAsset->id,
            'asset_tag' => 'nullable|string|unique:fixed_assets,asset_tag,' . $fixedAsset->id,
            'name' => 'required|string',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1',
            'depreciation_method' => 'nullable|string',
            'location' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|string',
        ]);

        $fixedAsset->update($validated);
        return redirect()->route('admin.fixed-assets.index')->with('success', 'Asset updated.');
    }

    public function destroy(FixedAsset $fixedAsset)
    {
        $fixedAsset->delete();
        return redirect()->route('admin.fixed-assets.index')->with('success', 'Asset deleted.');
    }

    public function runDepreciation(FixedAsset $fixedAsset)
    {
        $monthlyDep = $fixedAsset->calculateMonthlyDepreciation();
        if ($monthlyDep <= 0) {
            return back()->with('error', 'Depreciation amount is zero.');
        }

        $newAccumulated = $fixedAsset->accumulated_depreciation + $monthlyDep;
        $newNBV = $fixedAsset->acquisition_cost - $newAccumulated;

        if ($newNBV < $fixedAsset->salvage_value) {
            return back()->with('error', 'Asset has reached salvage value.');
        }

        DepreciationRecord::create([
            'fixed_asset_id' => $fixedAsset->id,
            'depreciation_date' => now(),
            'depreciation_amount' => $monthlyDep,
            'accumulated_depreciation' => $newAccumulated,
            'net_book_value' => $newNBV,
            'period' => date('Y-m'),
            'created_by' => auth()->id(),
        ]);

        $fixedAsset->update([
            'accumulated_depreciation' => $newAccumulated,
            'net_book_value' => $newNBV,
        ]);

        return back()->with('success', 'Depreciation recorded for ' . date('Y-m') . '.');
    }

    public function dispose(Request $request, FixedAsset $fixedAsset)
    {
        $validated = $request->validate([
            'disposal_value' => 'required|numeric|min:0',
            'disposal_notes' => 'nullable|string',
        ]);

        $fixedAsset->update([
            'status' => 'disposed',
            'disposal_date' => now(),
            'disposal_value' => $validated['disposal_value'],
            'disposal_notes' => $validated['disposal_notes'] ?? null,
        ]);

        return redirect()->route('admin.fixed-assets.index')->with('success', 'Asset disposed.');
    }
}
