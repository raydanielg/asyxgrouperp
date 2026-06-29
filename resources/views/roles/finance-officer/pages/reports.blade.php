@php
$title = 'Financial Reports';
$description = 'Summary of sales, purchases, expenses, and revenue.';
@endphp
@extends('layouts.admin')
@section('title', $title)
@section('page_title', $title)
@section('content')
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10">
        <h2 class="text-2xl font-bold">{{ $title }}</h2>
        <p class="text-emerald-100 text-sm mt-1">{{ $description }}</p>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Total Sales</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $money($totalSales ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Total Purchases</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $money($totalPurchases ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Total Expenses</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $money($totalExpenses ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Total Revenues</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $money($totalRevenues ?? 0) }}</p>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Employees</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalEmployees ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Projects</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalProjects ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Products</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalProducts ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Tickets</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalTickets ?? 0 }}</p>
    </div>
</div>

@include('roles.finance-officer.pages._actions', ['module' => 'reports'])

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border p-4">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Recent Sales</h3>
        <div class="space-y-3">
            @forelse($recentSales as $sale)
            <div class="flex items-center justify-between border-b last:border-0 pb-2">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $sale->invoice_number }}</p>
                    <p class="text-[10px] text-gray-400">{{ $sale->invoice_date?->format('M d, Y') }}</p>
                </div>
                <p class="text-xs font-semibold text-gray-900">{{ $money($sale->total_amount) }}</p>
            </div>
            @empty
            <p class="text-xs text-gray-400">No recent sales.</p>
            @endforelse
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Recent Expenses</h3>
        <div class="space-y-3">
            @forelse($recentExpenses as $expense)
            <div class="flex items-center justify-between border-b last:border-0 pb-2">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $expense->description ?? 'Expense' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $expense->expense_date?->format('M d, Y') }}</p>
                </div>
                <p class="text-xs font-semibold text-gray-900">{{ $money($expense->amount) }}</p>
            </div>
            @empty
            <p class="text-xs text-gray-400">No recent expenses.</p>
            @endforelse
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Recent Revenues</h3>
        <div class="space-y-3">
            @forelse($recentRevenues as $revenue)
            <div class="flex items-center justify-between border-b last:border-0 pb-2">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $revenue->description ?? 'Revenue' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $revenue->revenue_date?->format('M d, Y') }}</p>
                </div>
                <p class="text-xs font-semibold text-gray-900">{{ $money($revenue->amount) }}</p>
            </div>
            @empty
            <p class="text-xs text-gray-400">No recent revenues.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection