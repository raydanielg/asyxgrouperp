@extends('layouts.admin')
@section('title', 'Storekeeper')
@section('page_title', 'Inventory Desk')
@section('content')

<div class="bg-slate-800 rounded-md p-6 mb-5 text-white">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Inventory Desk</p>
            <h2 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-slate-400 text-xs mt-1">Stock · receipts · transfers · counts.</p>
        </div>
        <div class="text-right">
            <p class="text-slate-300 text-xs">{{ now()->format('l, d M Y') }}</p>
            <p class="text-slate-500 text-[10px] mt-1">{{ $stats['pendingTransfers'] ?? 0 }} transfers pending</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Products</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalProducts'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">in catalogue</p>
    </div>
    <div class="bg-white rounded-md border {{ ($stats['lowStockProducts'] ?? 0) > 0 ? 'border-red-200' : 'border-gray-200' }} p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Low stock</span>
        <p class="text-2xl font-bold {{ ($stats['lowStockProducts'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $stats['lowStockProducts'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">below reorder</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Warehouses</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['activeWarehouses'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">of {{ $stats['totalWarehouses'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-md border {{ ($stats['pendingTransfers'] ?? 0) > 0 ? 'border-amber-200' : 'border-gray-200' }} p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Transfers</span>
        <p class="text-2xl font-bold {{ ($stats['pendingTransfers'] ?? 0) > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-1">{{ $stats['pendingTransfers'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">pending</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Movements</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalMovements'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">all time</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">GRNs</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalGrns'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">receipts</p>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 p-5 mb-5">
    <h3 class="text-sm font-bold text-gray-900 mb-4">Inventory Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
        @php
        $actions = [
            ['module' => 'products', 'label' => 'Products'],
            ['module' => 'warehouses', 'label' => 'Warehouses'],
            ['module' => 'stock-movements', 'label' => 'Stock Movements'],
            ['module' => 'grns', 'label' => 'GRNs'],
            ['module' => 'stock-count', 'label' => 'Stock Count'],
            ['module' => 'deliveries', 'label' => 'Deliveries'],
        ];
        @endphp
        @foreach($actions as $a)
        <a href="{{ route('role.page', ['module' => $a['module']]) }}" class="flex items-center justify-between px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded text-xs font-medium text-gray-700">
            {{ $a['label'] }}
            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endforeach
    </div>
</div>

@include('roles.shared.ai-insights')
@endsection
