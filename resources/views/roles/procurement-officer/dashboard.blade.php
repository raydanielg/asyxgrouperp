@extends('layouts.admin')
@section('title', 'Procurement')
@section('page_title', 'Procurement Command')
@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n ?? 0); @endphp

<div class="bg-slate-800 rounded-md p-6 mb-5 text-white">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Procurement Command</p>
            <h2 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-slate-400 text-xs mt-1">Requisitions · LPOs · suppliers · receipts.</p>
        </div>
        <div class="text-right">
            <p class="text-slate-300 text-xs">{{ now()->format('l, d M Y') }}</p>
            <p class="text-slate-500 text-[10px] mt-1">{{ $stats['pendingLpos'] ?? 0 }} LPOs pending</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    <div class="bg-white rounded-md border {{ ($stats['pendingLpos'] ?? 0) > 0 ? 'border-amber-200' : 'border-gray-200' }} p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Pending LPOs</span>
        <p class="text-2xl font-bold {{ ($stats['pendingLpos'] ?? 0) > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-1">{{ $stats['pendingLpos'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">of {{ $stats['totalLpos'] ?? 0 }} total</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">GRNs</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalGrns'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">receipts recorded</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Suppliers</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalSuppliers'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">active vendors</p>
    </div>
    <div class="bg-white rounded-md border {{ ($stats['lowStockProducts'] ?? 0) > 0 ? 'border-red-200' : 'border-gray-200' }} p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Low stock</span>
        <p class="text-2xl font-bold {{ ($stats['lowStockProducts'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $stats['lowStockProducts'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">below reorder level</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Products</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalProducts'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">catalogue</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Tenders</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalTenders'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">tracked</p>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 p-5 mb-5">
    <h3 class="text-sm font-bold text-gray-900 mb-4">Procurement Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
        @php
        $actions = [
            ['module' => 'purchase-requisitions', 'label' => 'Requisitions'],
            ['module' => 'rfqs', 'label' => 'RFQs'],
            ['module' => 'lpos', 'label' => 'LPOs'],
            ['module' => 'grns', 'label' => 'GRNs'],
            ['module' => 'suppliers', 'label' => 'Suppliers'],
            ['module' => 'delivery-notes', 'label' => 'Delivery Notes'],
            ['module' => 'purchase-invoices', 'label' => 'Purchase Invoices'],
            ['module' => 'products', 'label' => 'Products'],
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
