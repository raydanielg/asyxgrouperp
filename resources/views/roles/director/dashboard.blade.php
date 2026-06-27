@extends('layouts.admin')
@section('title', 'Director Dashboard')
@section('page_title', 'Director Dashboard')
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
            <p class="text-emerald-100 text-sm mt-1">Director Dashboard - Executive Overview</p>
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
            <span class="text-[10px] font-medium text-emerald-100">Total Revenue</span>
            <p class="text-xl font-bold mt-1">{{ $money($stats['totalRevenues'] ?? 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br {{ $c['amber'] }} rounded-xl border p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-amber-50">Total Expenses</span>
            <p class="text-xl font-bold mt-1">{{ $money($stats['totalExpenses'] ?? 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br {{ $c['rose'] }} rounded-xl border p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-rose-100">Outstanding Balance</span>
            <p class="text-xl font-bold mt-1">{{ $money($stats['salesBalance'] ?? 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br {{ $c['sky'] }} rounded-xl border p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-sky-100">Active Projects</span>
            <p class="text-xl font-bold mt-1">{{ $stats['activeProjects'] ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Sales vs Purchases (14 days)</h3>
        <canvas id="directorChart" height="120"></canvas>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                View Reports
            </a>
            <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-sky-50 hover:bg-sky-100 text-sky-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                Projects
            </a>
            <a href="{{ route('admin.sales-dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
                Sales Dashboard
            </a>
            <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-violet-50 hover:bg-violet-100 text-violet-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857"/></svg>
                Employees
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
            <h3 class="text-sm font-bold text-gray-900">Recent Sales</h3>
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
            <h3 class="text-sm font-bold text-gray-900">Recent Tickets</h3>
            <a href="{{ route('admin.helpdesk-tickets.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
            @if(!empty($recentItems['recentTickets']))
            @foreach($recentItems['recentTickets']->take(5) as $ticket)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $ticket->subject ?? 'Ticket #' . $ticket->id }}</p><p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $ticket->status ?? '')) }}</p></div>
                <span class="text-[10px] text-gray-400">{{ $ticket->created_at->format('d M Y') }}</span>
            </div>
            @endforeach
            @else
            <div class="px-5 py-8 text-center text-gray-400 text-xs">No recent tickets</div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('directorChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                { label: 'Sales', data: @json($chartData['values']), backgroundColor: '#024938', borderRadius: 4 },
                @if(!empty($chartData['secondaryValues']))
                { label: '{{ $chartData['secondaryTitle'] }}', data: @json($chartData['secondaryValues']), backgroundColor: '#f9ac00', borderRadius: 4 },
                @endif
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } }, scales: { y: { beginAtZero: true, ticks: { font: { size: 9 } } }, x: { ticks: { font: { size: 8 } } } } }
    });
}
</script>
@endsection
