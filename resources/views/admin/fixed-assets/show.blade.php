@extends('layouts.admin')
@section('title', $fixedAsset->asset_number . ' - ' . config('app.name'))
@section('page_title', $fixedAsset->name)
@section('content')
<div class="mb-4"><a href="{{ route('admin.fixed-assets.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Assets</a></div>
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="font-bold text-gray-800 mb-4">{{ $fixedAsset->name }}</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-400">Asset #</dt><dd class="text-gray-700">{{ $fixedAsset->asset_number }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Tag</dt><dd class="text-gray-700">{{ $fixedAsset->asset_tag ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Category</dt><dd class="text-gray-700 capitalize">{{ str_replace('_', ' ', $fixedAsset->category ?? '—') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Location</dt><dd class="text-gray-700">{{ $fixedAsset->location ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Acquired</dt><dd class="text-gray-700">{{ $fixedAsset->acquisition_date->format('d M Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Cost</dt><dd class="text-gray-700 font-medium">{{ number_format($fixedAsset->acquisition_cost, 0) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Salvage</dt><dd class="text-gray-700">{{ number_format($fixedAsset->salvage_value, 0) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Life</dt><dd class="text-gray-700">{{ $fixedAsset->useful_life_years }} years</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Monthly Dep.</dt><dd class="text-gray-700">{{ number_format($fixedAsset->calculateMonthlyDepreciation(), 0) }}</dd></div>
            <div class="flex justify-between border-t pt-2"><dt class="text-gray-400">Acc. Dep.</dt><dd class="text-red-600 font-medium">{{ number_format($fixedAsset->accumulated_depreciation, 0) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400 font-bold">Net Book Value</dt><dd class="text-emerald-600 font-bold">{{ number_format($fixedAsset->net_book_value, 0) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Status</dt><dd><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $fixedAsset->status === 'in_use' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-500 border border-gray-100') }}">{{ $fixedAsset->status }}</span></dd></div>
        </dl>
        @if($fixedAsset->status === 'in_use')
        <div class="mt-4 flex gap-2">
            <form method="POST" action="{{ route('admin.fixed-assets.depreciate', $fixedAsset) }}">@csrf<button class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700">Run Depreciation</button></form>
        </div>
        @endif
    </div>
    <div class="bg-white rounded-xl border overflow-hidden lg:col-span-2">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h4 class="text-sm font-bold text-gray-700">Depreciation History</h4></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Period</th><th class="px-4 py-2 font-medium">Date</th><th class="px-4 py-2 font-medium text-right">Amount</th><th class="px-4 py-2 font-medium text-right">Accumulated</th><th class="px-4 py-2 font-medium text-right">NBV</th></tr></thead>
            <tbody>
            @forelse($fixedAsset->depreciationRecords as $dep)
            <tr class="border-t border-gray-100"><td class="px-4 py-2 text-xs">{{ $dep->period }}</td><td class="px-4 py-2 text-xs">{{ $dep->depreciation_date->format('d M Y') }}</td><td class="px-4 py-2 text-xs text-right">{{ number_format($dep->depreciation_amount, 0) }}</td><td class="px-4 py-2 text-xs text-right text-red-600">{{ number_format($dep->accumulated_depreciation, 0) }}</td><td class="px-4 py-2 text-xs text-right font-medium text-emerald-600">{{ number_format($dep->net_book_value, 0) }}</td></tr>
            @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-xs">No depreciation records yet</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
</div>
@endsection
