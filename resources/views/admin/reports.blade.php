@extends('layouts.admin')

@section('title', 'Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reports & Analytics')

@section('content')
@php
    $verifiedPct = $stats['totalAllUsers'] > 0 ? round($stats['activeUsers'] / $stats['totalAllUsers'] * 100) : 0;
@endphp

{{-- Financial summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">System-wide overview</span>
        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">All Reports →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Sales</p><p class="text-lg font-bold text-emerald-600 mt-0.5">TZS {{ number_format($stats['totalSales']) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Purchases</p><p class="text-lg font-bold text-gray-900 mt-0.5">TZS {{ number_format($stats['totalPurchases']) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Sales Returns</p><p class="text-lg font-bold text-red-500 mt-0.5">TZS {{ number_format($stats['totalSalesReturns']) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Purchase Returns</p><p class="text-lg font-bold text-amber-600 mt-0.5">TZS {{ number_format($stats['totalPurchaseReturns']) }}</p></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Users --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Users</h3>
            <span class="text-[10px] text-gray-400">{{ $stats['totalAllUsers'] }} total</span>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-gray-600">Verified</span>
                    <span class="text-xs font-bold text-gray-900">{{ number_format($stats['activeUsers']) }} <span class="text-gray-400 font-normal">· {{ $verifiedPct }}%</span></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-gray-900 h-1.5 rounded-full transition-all" style="width: {{ $verifiedPct }}%"></div>
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="flex items-center justify-between py-2"><span class="text-xs text-gray-500">Unverified</span><span class="text-xs font-medium text-gray-700">{{ number_format($stats['inactiveUsers']) }}</span></div>
                <div class="flex items-center justify-between py-2"><span class="text-xs text-gray-500">Admins</span><span class="text-xs font-medium text-gray-700">{{ number_format($stats['totalAdmins']) }}</span></div>
                <div class="flex items-center justify-between py-2"><span class="text-xs text-gray-500">Regular Users</span><span class="text-xs font-medium text-gray-700">{{ number_format($stats['totalUsers']) }}</span></div>
            </div>
        </div>
    </div>

    {{-- Invoices --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Invoices</h3></div>
        <div class="p-5 space-y-4">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Sales</p>
                <div class="grid grid-cols-2 gap-y-2">
                    @foreach(['draft' => '#9ca3af', 'posted' => '#3b82f6', 'paid' => '#10b981', 'overdue' => '#ef4444'] as $s => $c)
                    <div class="flex items-center justify-between pr-2">
                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $c }}"></span>{{ $s }}</span>
                        <span class="text-xs font-mono font-medium text-gray-800">{{ $salesByStatus[$s] ?? 0 }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="pt-3 border-t border-gray-100">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Purchases</p>
                <div class="grid grid-cols-2 gap-y-2">
                    @foreach(['draft' => '#9ca3af', 'posted' => '#3b82f6', 'paid' => '#10b981', 'overdue' => '#ef4444'] as $s => $c)
                    <div class="flex items-center justify-between pr-2">
                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $c }}"></span>{{ $s }}</span>
                        <span class="text-xs font-mono font-medium text-gray-800">{{ $purchaseByStatus[$s] ?? 0 }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- System --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">System</h3></div>
        <div class="divide-y divide-gray-50">
            <div class="flex items-center justify-between px-5 py-3">
                <span class="text-xs text-gray-500">Warehouses</span>
                <span class="text-xs font-mono font-bold text-gray-900">{{ $stats['totalWarehouses'] }}</span>
            </div>
            <div class="flex items-center justify-between px-5 py-3">
                <span class="text-xs text-gray-500">Support Tickets</span>
                <span class="text-xs font-mono font-bold text-gray-900">{{ $stats['totalTickets'] }} <span class="text-amber-600 font-normal">({{ $stats['openTickets'] }} open)</span></span>
            </div>
            <div class="flex items-center justify-between px-5 py-3">
                <span class="text-xs text-gray-500">Orders</span>
                <span class="text-xs font-mono font-bold text-gray-900">{{ $stats['totalOrders'] }}</span>
            </div>
            <div class="flex items-center justify-between px-5 py-3">
                <span class="text-xs text-gray-500">Plans</span>
                <span class="text-xs font-mono font-bold text-gray-900">{{ $stats['totalPlans'] }}</span>
            </div>
            <div class="flex items-center justify-between px-5 py-3">
                <span class="text-xs text-gray-500">Coupons</span>
                <span class="text-xs font-mono font-bold text-gray-900">{{ $stats['totalCoupons'] }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
