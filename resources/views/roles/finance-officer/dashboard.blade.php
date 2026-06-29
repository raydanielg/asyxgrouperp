@extends('layouts.admin')
@section('title', 'Finance Officer Dashboard')
@section('page_title', 'Finance Officer Dashboard')
@section('content')
@php
    $money = fn($n) => 'TZS ' . number_format($n);
    $c = [
        'emerald' => 'from-emerald-600 to-emerald-700 border-emerald-500',
        'sky' => 'from-sky-500 to-sky-600 border-sky-400',
        'amber' => 'from-amber-400 to-amber-500 border-amber-300',
        'rose' => 'from-rose-500 to-rose-600 border-rose-400',
        'violet' => 'from-violet-500 to-violet-600 border-violet-400',
    ];
@endphp

<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-emerald-100 text-sm mt-1">Finance Officer Dashboard - Financial Overview</p>
        </div>
        <div class="text-right">
            <p class="text-emerald-100 text-xs">{{ now()->format('l, d M Y') }}</p>
            <p class="text-emerald-200 text-[10px] mt-1">{{ now()->format('H:i') }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="bg-gradient-to-br {{ $c['emerald'] }} rounded-xl border p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-emerald-100">Total Sales</span>
            <p class="text-xl font-bold mt-1">{{ $money($stats['totalSales'] ?? 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br {{ $c['rose'] }} rounded-xl border p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-rose-100">Outstanding</span>
            <p class="text-xl font-bold mt-1">{{ $money($stats['salesBalance'] ?? 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br {{ $c['amber'] }} rounded-xl border p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-amber-50">Month Expenses</span>
            <p class="text-xl font-bold mt-1">{{ $money($stats['monthExpenses'] ?? 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br {{ $c['violet'] }} rounded-xl border p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-violet-100">Overdue Invoices</span>
            <p class="text-xl font-bold mt-1">{{ $stats['overdueInvoices'] ?? 0 }}</p>
        </div>
    </div>
</div>

@include('roles.shared.ai-insights')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Revenue vs Expenses (14 days)</h3>
        <canvas id="financeChart" height="120"></canvas>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('admin.sales-invoices.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Sales Invoices
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                Expenses
            </a>
            <a href="{{ route('admin.revenues.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8"/></svg>
                Revenues
            </a>
            <a href="{{ route('admin.bills.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-sky-50 hover:bg-sky-100 text-sky-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                Bills
            </a>
            <a href="{{ route('admin.bank-accounts.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-violet-50 hover:bg-violet-100 text-violet-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Bank Accounts
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
        @foreach($secondaryKpis as $kpi)
        <a href="{{ route($kpi['route']) }}" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow">
        <span class="text-[10px] font-medium text-gray-500">{{ $kpi['label'] }}</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $kpi['value'] }}</p>
    </a>
        @endforeach
        </div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Sales Invoices</h3>
            <a href="{{ route('admin.sales-invoices.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @if(!empty($recentItems['recentSales']))
            @foreach($recentItems['recentSales']->take(5) as $invoice)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $invoice->invoice_number }}</p><p class="text-[10px] text-gray-400">{{ $invoice->customer?->name ?? 'N/A' }}</p></div>
                <div class="text-right"><p class="text-xs font-semibold text-gray-900">TZS {{ number_format($invoice->total_amount) }}</p><p class="text-[10px] text-gray-400">{{ $invoice->invoice_date->format('d M Y') }}</p></div>
            </div>
        @endforeach
            @else
        <div class="px-5 py-8 text-center text-gray-400 text-xs">No recent sales</div>
        @endif
        </div>
    </div>
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Expenses</h3>
            <a href="{{ route('admin.expenses.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @if(!empty($recentItems['recentExpenses']))
            @foreach($recentItems['recentExpenses']->take(5) as $expense)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $expense->description ?? $expense->category ?? 'Expense' }}</p><p class="text-[10px] text-gray-400">{{ $expense->expense_date?->format('d M Y') ?? '' }}</p></div>
                <p class="text-xs font-semibold text-red-600">TZS {{ number_format($expense->amount) }}</p>
            </div>
        @endforeach
            @else
        <div class="px-5 py-8 text-center text-gray-400 text-xs">No recent expenses</div>
        @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('financeChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                { label: 'Revenue', data: @json($chartData['values']), backgroundColor: '#024938', borderRadius: 4 },
                @if(!empty($chartData['secondaryValues']))
                { label: 'Expenses', data: @json($chartData['secondaryValues']), backgroundColor: '#f9ac00', borderRadius: 4 },
                @endif
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } }, scales: { y: { beginAtZero: true, ticks: { font: { size: 9 }, callback: function(v) { return 'TZS ' + v.toLocaleString(); } } }, x: { ticks: { font: { size: 8 } } } } }
    });
}
</script>
@endsection
