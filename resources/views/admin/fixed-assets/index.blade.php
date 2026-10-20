@extends('layouts.admin')
@section('title', 'Fixed Assets - ' . config('app.name'))
@section('page_title', 'Fixed Assets')
@section('content')
@php
    $totalCost = $assets->sum('acquisition_cost');
    $totalDep = $assets->sum('accumulated_depreciation');
    $totalNBV = $assets->sum('net_book_value');
    $inUse = $assets->where('status', 'in_use')->count();
    $sc = ['in_use' => '#10b981', 'disposed' => '#9ca3af', 'under_maintenance' => '#f59e0b'];
@endphp

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $assets->total() }} assets · {{ $inUse }} in use</span>
        <a href="{{ route('admin.fixed-assets.create') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Register Asset</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Cost</p><p class="text-lg font-bold text-gray-900 mt-0.5">TZS {{ number_format($totalCost) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Acc. Depreciation</p><p class="text-lg font-bold text-red-500 mt-0.5">TZS {{ number_format($totalDep) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Net Book Value</p><p class="text-lg font-bold text-emerald-600 mt-0.5">TZS {{ number_format($totalNBV) }}</p></div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Asset #</th><th class="px-5 py-3 font-semibold">Name</th><th class="px-5 py-3 font-semibold">Category</th><th class="px-5 py-3 font-semibold text-right">Cost</th><th class="px-5 py-3 font-semibold text-right">Acc. Dep.</th><th class="px-5 py-3 font-semibold text-right">NBV</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($assets as $asset)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.fixed-assets.show', $asset) }}" class="text-xs font-mono font-medium text-gray-900 hover:text-gray-600">{{ $asset->asset_number }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-800">{{ $asset->name }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $asset->category ?? '—') }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-700">{{ number_format($asset->acquisition_cost, 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono {{ $asset->accumulated_depreciation > 0 ? 'text-red-500' : 'text-gray-300' }}">{{ $asset->accumulated_depreciation > 0 ? number_format($asset->accumulated_depreciation, 0) : '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold text-emerald-600">{{ number_format($asset->net_book_value, 0) }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc[$asset->status] ?? '#f59e0b' }}"></span>{{ ucfirst(str_replace('_', ' ', $asset->status)) }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.fixed-assets.show', $asset) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <a href="{{ route('admin.fixed-assets.edit', $asset) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</a>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No assets registered</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($assets->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $assets->links() }}</div>
    @endif
</div>
@endsection
