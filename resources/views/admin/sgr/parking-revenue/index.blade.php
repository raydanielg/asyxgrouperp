@extends('layouts.admin')
@section('title', 'SGR Parking Revenue - ' . config('app.name'))
@section('page_title', 'SGR Parking Revenue Collection')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-amber-50 text-amber-700 text-sm border border-amber-100">{{ session('success') }}</div>
@endif

<div class="bg-gradient-to-r from-amber-700 to-amber-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">SGR Parking Revenue Collection</h2>
            <p class="text-amber-100 text-sm mt-1">Track daily car parking revenue per station and cashier</p>
        </div>
        <div class="text-right"><p class="text-amber-100 text-xs">{{ now()->format('l, d M Y') }}</p></div>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-amber-600">{{ number_format($stats['total_collections']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Records</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_collected'], 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Amount Collected</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-sky-600">{{ number_format($stats['total_deposited'], 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Amount Deposited</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold {{ $stats['total_difference'] >= 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($stats['total_difference'], 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Difference</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Top Cashiers by Collection</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50"><th class="px-4 py-2 font-medium">Cashier</th><th class="px-4 py-2 font-medium text-right">Records</th><th class="px-4 py-2 font-medium text-right">Collected</th><th class="px-4 py-2 font-medium text-right">Deposited</th><th class="px-4 py-2 font-medium text-right">Difference</th></tr></thead>
                <tbody>
                @forelse($byCashier as $item)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2 text-xs text-gray-700">{{ $item->cashier_name }}</td>
                    <td class="px-4 py-2 text-xs text-gray-600 text-right">{{ $item->total }}</td>
                    <td class="px-4 py-2 text-xs font-semibold text-emerald-700 text-right">{{ number_format($item->collected, 2) }}</td>
                    <td class="px-4 py-2 text-xs font-semibold text-sky-700 text-right">{{ number_format($item->deposited, 2) }}</td>
                    <td class="px-4 py-2 text-xs font-semibold text-rose-600 text-right">{{ number_format($item->diff, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-xs">No data yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('admin.sgr.parking-revenue.import') }}" class="flex items-center gap-3 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Revenue Excel
            </a>
            <a href="{{ route('admin.sgr.parking-revenue.reports') }}" class="flex items-center gap-3 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                View Reports
            </a>
            <a href="{{ route('admin.sgr.parking-revenue.download-template') }}" class="flex items-center gap-3 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download Template
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">By Booth / Comment</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50"><th class="px-4 py-2 font-medium">Booth</th><th class="px-4 py-2 font-medium text-right">Collected</th><th class="px-4 py-2 font-medium text-right">Deposited</th></tr></thead>
                <tbody>
                @forelse($byBooth as $item)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2 text-xs text-gray-700">{{ $item->booth }}</td>
                    <td class="px-4 py-2 text-xs font-semibold text-gray-900 text-right">{{ number_format($item->collected, 2) }}</td>
                    <td class="px-4 py-2 text-xs font-semibold text-gray-900 text-right">{{ number_format($item->deposited, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400 text-xs">No data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Recent Imports</h3>
        @forelse($batches as $batch)
        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-700 truncate">{{ $batch->source_filename }}</p>
                <p class="text-[10px] text-gray-400">{{ $batch->import_batch }} | {{ $batch->total }} rows</p>
                <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($batch->uploaded_at)->diffForHumans() }}</p>
            </div>
            <a href="{{ route('admin.sgr.parking-revenue.reports', ['batch' => $batch->import_batch]) }}" class="text-xs text-amber-600 hover:text-amber-700 whitespace-nowrap">View</a>
        </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-4">No imports yet.</p>
        @endforelse
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-700">Recent Records</h4>
        <a href="{{ route('admin.sgr.parking-revenue.reports') }}" class="text-xs text-amber-600 hover:text-amber-700">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50"><th class="px-4 py-2 font-medium">Date In</th><th class="px-4 py-2 font-medium">Cashier</th><th class="px-4 py-2 font-medium">Booth</th><th class="px-4 py-2 font-medium text-right">Collected</th><th class="px-4 py-2 font-medium text-right">Deposited</th></tr></thead>
            <tbody>
            @forelse($recent as $r)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs text-gray-700">{{ $r->date_in?->format('d M Y') ?? '—' }}</td>
                <td class="px-4 py-2 text-xs text-gray-700">{{ $r->cashier_name ?? '—' }}</td>
                <td class="px-4 py-2 text-xs text-gray-600">{{ $r->comments ?? '—' }}</td>
                <td class="px-4 py-2 text-xs text-emerald-700 text-right">{{ number_format($r->amount_collected, 2) }}</td>
                <td class="px-4 py-2 text-xs text-sky-700 text-right">{{ number_format($r->amount_deposited, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-xs">No records yet. <a href="{{ route('admin.sgr.parking-revenue.import') }}" class="text-amber-600 hover:text-amber-700">Import now</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
