@extends('admin.reports.layout')

@section('pageTitle', 'Balance Sheet')
@section('pageSubtitle', 'Assets, liabilities and equity overview')

@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n ?? 0); @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Assets --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b bg-emerald-50/50">
            <h3 class="text-sm font-semibold text-gray-900">Assets</h3>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                <span class="text-xs font-medium text-gray-700">Fixed Assets</span>
                <span class="text-sm font-bold text-emerald-700">{{ $money($assets) }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-sky-50 rounded-lg">
                <span class="text-xs font-medium text-gray-700">Bank Balances</span>
                <span class="text-sm font-bold text-sky-700">{{ $money($bankBalance) }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-violet-50 rounded-lg">
                <span class="text-xs font-medium text-gray-700">Cash Accounts</span>
                <span class="text-sm font-bold text-violet-700">{{ $money($cashBalance) }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg">
                <span class="text-xs font-medium text-gray-700">Receivables</span>
                <span class="text-sm font-bold text-amber-700">{{ $money($receivables) }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                <span class="text-xs font-medium text-gray-700">Inventory Value</span>
                <span class="text-sm font-bold text-emerald-700">{{ $money($inventoryValue) }}</span>
            </div>
            <div class="border-t pt-3 flex items-center justify-between p-3 bg-emerald-100 rounded-lg">
                <span class="text-sm font-bold text-gray-900">Total Assets</span>
                <span class="text-lg font-bold text-emerald-700">{{ $money($totalAssets) }}</span>
            </div>
        </div>
    </div>

    {{-- Liabilities --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b bg-red-50/50">
            <h3 class="text-sm font-semibold text-gray-900">Liabilities</h3>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                <span class="text-xs font-medium text-gray-700">Payables</span>
                <span class="text-sm font-bold text-red-600">{{ $money($payables) }}</span>
            </div>
            <div class="border-t pt-3 flex items-center justify-between p-3 bg-red-100 rounded-lg">
                <span class="text-sm font-bold text-gray-900">Total Liabilities</span>
                <span class="text-lg font-bold text-red-600">{{ $money($totalLiabilities) }}</span>
            </div>
        </div>
    </div>

    {{-- Equity --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b bg-violet-50/50">
            <h3 class="text-sm font-semibold text-gray-900">Equity</h3>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center justify-between p-3 bg-violet-50 rounded-lg">
                <span class="text-xs font-medium text-gray-700">Owner's Equity</span>
                <span class="text-sm font-bold text-violet-700">{{ $money($equity) }}</span>
            </div>
            <div class="border-t pt-3 flex items-center justify-between p-3 bg-violet-100 rounded-lg">
                <span class="text-sm font-bold text-gray-900">Total Equity</span>
                <span class="text-lg font-bold text-violet-700">{{ $money($equity) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border p-5 mt-6">
    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl text-white">
        <span class="text-sm font-bold">Balance Check: Assets = Liabilities + Equity</span>
        <span class="text-sm font-bold">{{ $money($totalAssets) }} = {{ $money($totalLiabilities) }} + {{ $money($equity) }}</span>
    </div>
</div>
@endsection
