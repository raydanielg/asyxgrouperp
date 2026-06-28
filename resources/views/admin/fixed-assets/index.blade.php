@extends('layouts.admin')
@section('title', 'Fixed Assets - ' . config('app.name'))
@section('page_title', 'Fixed Assets')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage company fixed assets and depreciation</p>
    <a href="{{ route('admin.fixed-assets.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Register Asset
    </a>
</div>
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Asset #</th><th class="px-5 py-3 font-medium">Name</th><th class="px-5 py-3 font-medium">Category</th><th class="px-5 py-3 font-medium text-right">Cost</th><th class="px-5 py-3 font-medium text-right">Acc. Dep.</th><th class="px-5 py-3 font-medium text-right">NBV</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($assets as $asset)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs"><a href="{{ route('admin.fixed-assets.show', $asset) }}" class="font-medium text-gray-800 hover:text-emerald-600">{{ $asset->asset_number }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $asset->name }}</td>
            <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ str_replace('_', ' ', $asset->category ?? '—') }}</td>
            <td class="px-5 py-3 text-xs text-right">{{ number_format($asset->acquisition_cost, 0) }}</td>
            <td class="px-5 py-3 text-xs text-right text-red-600">{{ number_format($asset->accumulated_depreciation, 0) }}</td>
            <td class="px-5 py-3 text-xs text-right font-medium text-emerald-600">{{ number_format($asset->net_book_value, 0) }}</td>
            <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $asset->status === 'in_use' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : ($asset->status === 'disposed' ? 'bg-gray-50 text-gray-500 border border-gray-100' : 'bg-amber-50 text-amber-700 border border-amber-100') }}">{{ $asset->status }}</span></td>
            <td class="px-5 py-3 flex items-center gap-3">
                <a href="{{ route('admin.fixed-assets.show', $asset) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">View</a>
                <a href="{{ route('admin.fixed-assets.edit', $asset) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">Edit</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No assets registered</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $assets->links() }}</div>
</div>
@endsection
