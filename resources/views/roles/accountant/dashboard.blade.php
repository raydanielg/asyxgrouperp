@extends('layouts.admin')
@section('title', 'Accountant Dashboard')
@section('page_title', 'Accountant Dashboard')
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

    $today = now();
    $calendarStart = $today->copy()->startOfMonth()->startOfWeek();
    $calendarEnd = $today->copy()->endOfMonth()->endOfWeek();
    $calendarDays = [];
    $cursor = $calendarStart->copy();
    while ($cursor <= $calendarEnd) {
        $calendarDays[] = $cursor->copy();
        $cursor->addDay();
    }

    $recentSales = $recentItems['recentSales'] ?? collect();
    $recentExpenses = $recentItems['recentExpenses'] ?? collect();
    $recentPurchases = $recentItems['recentPurchases'] ?? collect();
@endphp

{{-- Welcome Banner --}}
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 right-0 w-24 h-24 bg-white/5 rounded-full -mr-8 -mb-8"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-emerald-100 text-sm mt-1">Accountant Dashboard - Financial Overview</p>
        </div>
        <div class="text-right">
            <p class="text-emerald-100 text-xs">{{ $today->format('l, d M Y') }}</p>
            <p class="text-emerald-200 text-[10px] mt-1">{{ $today->format('H:i') }}</p>
        </div>
    </div>
</div>

{{-- KPI Cards --}}
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
            <span class="text-[10px] font-medium text-rose-100">Outstanding Receivables</span>
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
    <div class="bg-gradient-to-br {{ $c['sky'] }} rounded-xl border p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-sky-100">Net Position</span>
            <p class="text-xl font-bold mt-1">{{ $money(($stats['totalRevenues'] ?? 0) - ($stats['totalExpenses'] ?? 0)) }}</p>
        </div>
    </div>
</div>

{{-- AI Insights --}}
@include('roles.shared.ai-insights')

{{-- Charts & Calendar --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Revenue vs Expenses (14 days)</h3>
        <canvas id="financeChart" height="120"></canvas>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Calendar</h3>
            <span class="text-[10px] text-emerald-600 font-medium">{{ $today->format('F Y') }}</span>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center mb-2">
            @foreach(['S','M','T','W','T','F','S'] as $d)
            <span class="text-[10px] font-bold text-gray-400">{{ $d }}</span>
            @endforeach
        </div>
        <div class="grid grid-cols-7 gap-1">
            @foreach($calendarDays as $day)
            <div class="aspect-square rounded-lg flex items-center justify-center text-[10px] font-medium
                {{ $day->isCurrentMonth() ? ($day->isToday() ? 'bg-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-50') : 'text-gray-300' }}">
                {{ $day->format('j') }}
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Secondary KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
    @foreach($secondaryKpis as $kpi)
    <a href="{{ route($kpi['route']) }}" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow">
        <span class="text-[10px] font-medium text-gray-500">{{ $kpi['label'] }}</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $kpi['value'] }}</p>
    </a>
    @endforeach
</div>

{{-- Quick Actions --}}
<div class="bg-white rounded-xl border p-5 mb-6">
    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        Quick Actions
    </h3>
    <div class="flex flex-wrap gap-3">
        @foreach($quickActions as $action)
        <a href="{{ route($action['route']) }}" class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors border border-emerald-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/></svg>
            {{ $action['label'] }}
        </a>
        @endforeach
    </div>
</div>

{{-- Recent Items --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Sales Invoices</h3>
            <a href="{{ route('admin.sales-invoices.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @forelse($recentSales->take(5) as $invoice)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                    <p class="text-[10px] text-gray-400">{{ $invoice->customer?->name ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-gray-900">TZS {{ number_format($invoice->total_amount) }}</p>
                    <p class="text-[10px] text-gray-400">{{ $invoice->invoice_date?->format('d M Y') ?? '' }}</p>
                </div>
            </div>
        @empty
            <div class="px-5 py-8 text-center text-gray-400 text-xs">No recent sales invoices</div>
        @endforelse
        </div>
    </div>
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Expenses</h3>
            <a href="{{ route('admin.expenses.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @forelse($recentExpenses->take(5) as $expense)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $expense->description ?? $expense->category ?? 'Expense' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $expense->expense_date?->format('d M Y') ?? '' }}</p>
                </div>
                <p class="text-xs font-semibold text-red-600">TZS {{ number_format($expense->amount) }}</p>
            </div>
        @empty
            <div class="px-5 py-8 text-center text-gray-400 text-xs">No recent expenses</div>
        @endforelse
        </div>
    </div>
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Purchases</h3>
            <a href="{{ route('admin.purchase-invoices.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @forelse($recentPurchases->take(5) as $purchase)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $purchase->invoice_number }}</p>
                    <p class="text-[10px] text-gray-400">{{ $purchase->vendor?->name ?? 'N/A' }}</p>
                </div>
                <p class="text-xs font-semibold text-gray-900">TZS {{ number_format($purchase->total_amount) }}</p>
            </div>
        @empty
            <div class="px-5 py-8 text-center text-gray-400 text-xs">No recent purchases</div>
        @endforelse
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
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 10 }, usePointStyle: true } },
                tooltip: {
                    callbacks: {
                        label: function(context) { return context.dataset.label + ': TZS ' + context.raw.toLocaleString(); }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 9 }, callback: function(v) { return 'TZS ' + v.toLocaleString(); } } },
                x: { ticks: { font: { size: 8 } } }
            }
        }
    });
}
</script>
@endsection
