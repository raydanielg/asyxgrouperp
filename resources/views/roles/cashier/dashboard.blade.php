@extends('layouts.admin')
@section('title', 'Cashier')
@section('page_title', 'Cashier Desk')
@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n ?? 0); @endphp

<div class="bg-slate-800 rounded-md p-6 mb-5 text-white">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Cashier Desk</p>
            <h2 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-slate-400 text-xs mt-1">POS · receipts · petty cash · payments.</p>
        </div>
        <div class="text-right">
            <p class="text-slate-300 text-xs">{{ now()->format('l, d M Y') }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Cash collected</span>
        <p class="text-lg font-bold text-emerald-700 mt-1">{{ $money($stats['salesPaid']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">across invoices</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Receivables</span>
        <p class="text-lg font-bold text-amber-600 mt-1">{{ $money($stats['salesBalance']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $stats['overdueInvoices'] ?? 0 }} overdue</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Expenses</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $money($stats['monthExpenses'] ?? $stats['totalExpenses']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">this month</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Draft invoices</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['draftInvoices'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">unfinished</p>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 p-5 mb-5">
    <h3 class="text-sm font-bold text-gray-900 mb-4">Cashier Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
        @php
        $actions = [
            ['module' => 'pos', 'label' => 'POS Terminal'],
            ['module' => 'pos-reports', 'label' => 'POS Reports'],
            ['module' => 'client-receipts', 'label' => 'Client Receipts'],
            ['module' => 'vendor-payments', 'label' => 'Vendor Payments'],
            ['module' => 'petty-cash', 'label' => 'Petty Cash'],
            ['module' => 'bank-accounts', 'label' => 'Bank Accounts'],
            ['module' => 'acc-transfers', 'label' => 'Account Transfers'],
            ['module' => 'sales-invoices', 'label' => 'Sales Invoices'],
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
