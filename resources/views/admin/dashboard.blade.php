@extends('layouts.admin')

@section('title', 'Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'ERP Dashboard')

@section('content')
@php
$fmt = fn($n) => $n >= 1000000000 ? number_format($n/1000000000,2).'B' : ($n >= 1000000 ? number_format($n/1000000,2).'M' : ($n >= 1000 ? number_format($n/1000,1).'K' : number_format($n)));
$money = fn($n) => '$' . number_format($n, 2);
@endphp

{{-- ═══ ERP KPI Cards ═══ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @php
    $kpis = [
        ['label' => 'Total Sales', 'value' => $money($stats['totalSalesAmount']), 'sub' => $stats['totalSalesInvoices'] . ' invoices', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'from' => 'emerald-600', 'to' => 'emerald-700', 'border' => 'emerald-500', 'text' => 'emerald-100', 'sub_color' => 'emerald-200'],
        ['label' => 'Total Purchases', 'value' => $money($stats['totalPurchaseAmount']), 'sub' => $stats['totalPurchaseInvoices'] . ' invoices', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'from' => 'sky-500', 'to' => 'sky-600', 'border' => 'sky-400', 'text' => 'sky-100', 'sub_color' => 'sky-200'],
        ['label' => 'Outstanding Balance', 'value' => $money($stats['totalSalesBalance']), 'sub' => 'Receivables', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'from' => 'amber-400', 'to' => 'amber-500', 'border' => 'amber-300', 'text' => 'amber-50', 'sub_color' => 'amber-100'],
        ['label' => 'Open Tickets', 'value' => number_format($stats['openTickets'] + $stats['inProgressTickets']), 'sub' => $stats['totalTickets'] . ' total tickets', 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'rose-500', 'to' => 'rose-600', 'border' => 'rose-400', 'text' => 'rose-100', 'sub_color' => 'rose-200'],
    ];
    @endphp
    @foreach($kpis as $card)
    <div class="bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="absolute bottom-0 right-0 w-12 h-12 bg-white/5 rounded-full -mr-4 -mb-4"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium {{ $card['text'] }}">{{ $card['label'] }}</span>
                <svg class="w-4 h-4 {{ $card['sub_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
            <p class="text-[10px] {{ $card['sub_color'] }} font-medium mt-1">{{ $card['sub'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══ Secondary KPI Row ═══ --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    @php
    $subKpis = [
        ['label' => 'Warehouses', 'value' => $stats['totalWarehouses'], 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5', 'color' => 'emerald', 'route' => route('admin.warehouses.index')],
        ['label' => 'Proposals', 'value' => $stats['totalProposals'], 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'violet', 'route' => route('admin.sales-proposals.index')],
        ['label' => 'Sales Returns', 'value' => $stats['totalSalesReturns'], 'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6', 'color' => 'rose', 'route' => route('admin.sales-returns.index')],
        ['label' => 'Purchase Returns', 'value' => $stats['totalPurchaseReturns'], 'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6', 'color' => 'amber', 'route' => route('admin.purchase-returns.index')],
        ['label' => 'Transfers', 'value' => $stats['totalTransfers'], 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'color' => 'sky', 'route' => route('admin.transfers.index')],
        ['label' => 'Orders', 'value' => $stats['totalOrders'], 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'color' => 'emerald', 'route' => route('admin.orders.index')],
    ];
    @endphp
    @foreach($subKpis as $kpi)
    <a href="{{ $kpi['route'] }}" class="bg-white rounded-xl border p-3 hover:shadow-md transition-all hover:border-{{ $kpi['color'] }}-300 group">
        <div class="flex items-center gap-2 mb-1">
            <div class="w-7 h-7 rounded-lg bg-{{ $kpi['color'] }}-50 flex items-center justify-center group-hover:bg-{{ $kpi['color'] }}-100 transition-colors">
                <svg class="w-3.5 h-3.5 text-{{ $kpi['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/></svg>
            </div>
            <span class="text-[10px] font-medium text-gray-400">{{ $kpi['label'] }}</span>
        </div>
        <p class="text-lg font-bold text-gray-900">{{ $kpi['value'] }}</p>
    </a>
    @endforeach
</div>

{{-- ═══ Departmental Insights ═══ --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    {{-- HRM Insight --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900">HRM Overview</h3>
            <a href="{{ route('admin.employees.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-emerald-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Employees</p><p class="text-lg font-bold text-emerald-700">{{ $stats['totalEmployees'] }}</p><p class="text-[10px] text-gray-400">{{ $stats['activeEmployees'] }} active</p></div>
            <div class="bg-amber-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Pending Leaves</p><p class="text-lg font-bold text-amber-600">{{ $stats['pendingLeaves'] }}</p><p class="text-[10px] text-gray-400">Awaiting approval</p></div>
        </div>
    </div>
    {{-- CRM Insight --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900">CRM Summary</h3>
            <a href="{{ route('admin.crm-leads.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-sky-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Leads</p><p class="text-lg font-bold text-sky-700">{{ $stats['totalCrmLeads'] }}</p><p class="text-[10px] text-gray-400">{{ $stats['newLeads'] }} new, {{ $stats['qualifiedLeads'] }} qualified</p></div>
            <div class="bg-violet-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Open Deals</p><p class="text-lg font-bold text-violet-700">{{ $stats['openDeals'] }}</p><p class="text-[10px] text-gray-400">{{ $money($stats['totalDealValue']) }} value</p></div>
        </div>
    </div>
    {{-- Accounting Insight --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900">Accounting</h3>
            <a href="{{ route('admin.revenues.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-emerald-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Revenue (Month)</p><p class="text-lg font-bold text-emerald-700">{{ $money($stats['monthRevenues']) }}</p><p class="text-[10px] text-gray-400">{{ $money($stats['totalRevenues']) }} total</p></div>
            <div class="bg-red-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Expenses (Month)</p><p class="text-lg font-bold text-red-600">{{ $money($stats['monthExpenses']) }}</p><p class="text-[10px] text-gray-400">{{ $money($stats['totalExpenses']) }} total</p></div>
        </div>
    </div>
    {{-- Projects Insight --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900">Projects</h3>
            <a href="{{ route('admin.projects.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-amber-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Active</p><p class="text-lg font-bold text-amber-600">{{ $stats['activeProjects'] }}</p><p class="text-[10px] text-gray-400">in progress</p></div>
            <div class="bg-emerald-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Completed</p><p class="text-lg font-bold text-emerald-700">{{ $stats['completedProjects'] }}</p><p class="text-[10px] text-gray-400">{{ $stats['totalProjects'] }} total</p></div>
        </div>
    </div>
    {{-- POS Insight --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900">POS Sales</h3>
            <a href="{{ route('admin.pos.reports') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-emerald-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Today</p><p class="text-lg font-bold text-emerald-700">{{ $money($stats['posTodaySales']) }}</p><p class="text-[10px] text-gray-400">{{ $stats['posTodayCount'] }} transactions</p></div>
            <div class="bg-sky-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">This Month</p><p class="text-lg font-bold text-sky-700">{{ $money($stats['posMonthSales']) }}</p><p class="text-[10px] text-gray-400">monthly total</p></div>
        </div>
    </div>
    {{-- Products Insight --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900">Products</h3>
            <a href="{{ route('admin.products.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-emerald-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Total Products</p><p class="text-lg font-bold text-emerald-700">{{ $stats['totalProducts'] }}</p><p class="text-[10px] text-gray-400">in catalog</p></div>
            <div class="bg-red-50/50 rounded-lg p-3"><p class="text-[10px] text-gray-400 uppercase">Low Stock</p><p class="text-lg font-bold text-red-600">{{ $stats['lowStockProducts'] }}</p><p class="text-[10px] text-gray-400">need reorder</p></div>
        </div>
    </div>
</div>

{{-- ═══ Sales vs Purchase Chart + Invoice Status ═══ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Monthly Sales vs Purchases Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Sales vs Purchases</h3>
                <p class="text-xs text-gray-400">Last 6 months</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-500"></span>Sales</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-sky-500"></span>Purchases</span>
            </div>
        </div>
        @php
        $maxMonthly = max(array_merge($monthlySales, $monthlyPurchases)) ?: 1;
        $barWidth = 100 / count($monthlyLabels);
        @endphp
        <div class="flex items-end gap-3 h-48">
            @foreach($monthlyLabels as $i => $label)
            @php
            $sPct = min(100, ($monthlySales[$i] / $maxMonthly) * 100);
            $pPct = min(100, ($monthlyPurchases[$i] / $maxMonthly) * 100);
            @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="w-full flex items-end justify-center gap-1 h-40">
                    <div class="w-1/2 bg-gray-50 rounded-t-md relative h-full overflow-hidden flex items-end">
                        <div class="w-full bg-emerald-500 rounded-t-md transition-all duration-500 hover:bg-emerald-600" style="height: {{ max($sPct, 2) }}%" title="Sales: {{ $money($monthlySales[$i]) }}"></div>
                    </div>
                    <div class="w-1/2 bg-gray-50 rounded-t-md relative h-full overflow-hidden flex items-end">
                        <div class="w-full bg-sky-500 rounded-t-md transition-all duration-500 hover:bg-sky-600" style="height: {{ max($pPct, 2) }}%" title="Purchases: {{ $money($monthlyPurchases[$i]) }}"></div>
                    </div>
                </div>
                <span class="text-[9px] text-gray-400 font-medium">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Sales Invoice Status Breakdown --}}
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Invoice Status</h3>
        <div class="space-y-3">
            @php
            $statusColors = ['draft' => 'gray', 'posted' => 'sky', 'paid' => 'emerald', 'overdue' => 'red', 'partial' => 'amber'];
            $totalInvoices = max(array_sum($salesStatusBreakdown), 1);
            @endphp
            @foreach($salesStatusBreakdown as $status => $count)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-{{ $statusColors[$status] }}-100 flex items-center justify-center shrink-0">
                    <div class="w-2.5 h-2.5 rounded-full bg-{{ $statusColors[$status] }}-500"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-0.5">
                        <p class="text-xs font-medium text-gray-900 capitalize">{{ $status }}</p>
                        <p class="text-xs font-semibold text-gray-900">{{ $count }}</p>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-{{ $statusColors[$status] }}-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ ($count / $totalInvoices) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ═══ Daily Activity Chart ═══ --}}
<div class="bg-white rounded-xl border p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Daily Transaction Activity</h3>
            <p class="text-xs text-gray-400">Last 14 days</p>
        </div>
    </div>
    @php $dailyMax = max(array_merge($dailySales, $dailyPurchases)) ?: 1; @endphp
    <div class="flex items-end gap-1.5 h-32">
        @foreach($dailyLabels as $i => $label)
        @php
        $sPct = min(100, ($dailySales[$i] / $dailyMax) * 100);
        $pPct = min(100, ($dailyPurchases[$i] / $dailyMax) * 100);
        $isToday = $i === count($dailyLabels) - 1;
        @endphp
        <div class="flex-1 flex flex-col items-center gap-1 group cursor-pointer">
            <div class="w-full flex items-end justify-center gap-0.5 h-24">
                <div class="w-1/2 {{ $isToday ? 'bg-gold-500' : 'bg-emerald-300 hover:bg-emerald-400' }} rounded-t transition-all duration-300" style="height: {{ max($sPct, 2) }}%" title="Sales: {{ $money($dailySales[$i]) }}"></div>
                <div class="w-1/2 {{ $isToday ? 'bg-gold-400' : 'bg-sky-300 hover:bg-sky-400' }} rounded-t transition-all duration-300" style="height: {{ max($pPct, 2) }}%" title="Purchases: {{ $money($dailyPurchases[$i]) }}"></div>
            </div>
            <span class="text-[8px] text-gray-400 font-medium">{{ explode(' ', $label)[0] }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- ═══ Recent Activity Tables ═══ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Recent Sales Invoices --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Sales Invoices</h3>
            <a href="{{ route('admin.sales-invoices.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">Invoice #</th>
                    <th class="px-5 py-2.5 font-medium">Customer</th>
                    <th class="px-5 py-2.5 font-medium">Amount</th>
                    <th class="px-5 py-2.5 font-medium">Status</th>
                </tr></thead>
                <tbody>
                    @forelse($recentSales as $invoice)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-2.5 text-xs font-mono text-emerald-700"><a href="{{ route('admin.sales-invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                        <td class="px-5 py-2.5 text-xs text-gray-700">{{ $invoice->customer?->name ?? 'N/A' }}</td>
                        <td class="px-5 py-2.5 text-xs font-semibold text-gray-900">{{ $money($invoice->total_amount) }}</td>
                        <td class="px-5 py-2.5"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-100 capitalize">{{ $invoice->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No sales invoices yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Purchase Invoices --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Purchase Invoices</h3>
            <a href="{{ route('admin.purchase-invoices.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">Invoice #</th>
                    <th class="px-5 py-2.5 font-medium">Vendor</th>
                    <th class="px-5 py-2.5 font-medium">Amount</th>
                    <th class="px-5 py-2.5 font-medium">Status</th>
                </tr></thead>
                <tbody>
                    @forelse($recentPurchases as $invoice)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-2.5 text-xs font-mono text-sky-700"><a href="{{ route('admin.purchase-invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                        <td class="px-5 py-2.5 text-xs text-gray-700">{{ $invoice->vendor?->name ?? 'N/A' }}</td>
                        <td class="px-5 py-2.5 text-xs font-semibold text-gray-900">{{ $money($invoice->total_amount) }}</td>
                        <td class="px-5 py-2.5"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-100 capitalize">{{ $invoice->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No purchase invoices yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══ Recent Tickets + Proposals ═══ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Tickets --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Support Tickets</h3>
            <a href="{{ route('admin.helpdesk-tickets.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="p-5 space-y-3">
            @forelse($recentTickets as $ticket)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg @if($ticket->priority === 'urgent') bg-red-50 @elseif($ticket->priority === 'high') bg-orange-50 @elseif($ticket->priority === 'medium') bg-amber-50 @else bg-blue-50 @endif flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 @if($ticket->priority === 'urgent') text-red-600 @elseif($ticket->priority === 'high') text-orange-600 @elseif($ticket->priority === 'medium') text-amber-600 @else text-blue-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.helpdesk-tickets.show', $ticket) }}" class="text-sm font-medium text-gray-900 truncate hover:text-emerald-600">{{ $ticket->title }}</a>
                    <p class="text-xs text-gray-400">{{ $ticket->ticket_id }} · {{ $ticket->created_at->diffForHumans() }}</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium @if($ticket->status === 'open') bg-emerald-50 text-emerald-700 border border-emerald-100 @elseif($ticket->status === 'in_progress') bg-amber-50 text-amber-700 border border-amber-100 @else bg-gray-50 text-gray-600 border border-gray-100 @endif capitalize">{{ str_replace('_', ' ', $ticket->status) }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No tickets yet</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Proposals --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Proposals</h3>
            <a href="{{ route('admin.sales-proposals.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="p-5 space-y-3">
            @forelse($recentProposals as $proposal)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.sales-proposals.show', $proposal) }}" class="text-sm font-medium text-gray-900 truncate hover:text-emerald-600">{{ $proposal->proposal_number }}</a>
                    <p class="text-xs text-gray-400">{{ $proposal->customer?->name ?? 'N/A' }} · {{ $money($proposal->total_amount) }}</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium @if($proposal->status === 'accepted') bg-emerald-50 text-emerald-700 border border-emerald-100 @elseif($proposal->status === 'sent') bg-sky-50 text-sky-700 border border-sky-100 @elseif($proposal->status === 'rejected') bg-red-50 text-red-700 border border-red-100 @else bg-gray-50 text-gray-600 border border-gray-100 @endif capitalize">{{ $proposal->status }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No proposals yet</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ═══ Quick Stats Footer ═══ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6">
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-emerald-600">{{ $stats['paidSalesInvoices'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Paid Invoices</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-red-500">{{ $stats['overdueSalesInvoices'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Overdue Invoices</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-amber-500">{{ $stats['pendingProposals'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Pending Proposals</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-sky-500">{{ $stats['activeWarehouses'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Active Warehouses</p>
    </div>
</div>
@endsection
