@extends('layouts.admin')
@section('title', 'Cashier Dashboard')
@section('page_title', 'Cashier Dashboard')
@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n); @endphp
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div><h2 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}</h2><p class="text-emerald-100 text-sm mt-1">Cashier Dashboard - POS Terminal</p></div>
        <div class="text-right"><p class="text-emerald-100 text-xs">{{ now()->format('l, d M Y') }}</p><p class="text-emerald-200 text-[10px] mt-1">{{ now()->format('H:i') }}</p></div>
    </div>
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 border border-emerald-500 rounded-xl p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10"><span class="text-[10px] font-medium text-emerald-100">Today's Sales</span><p class="text-xl font-bold mt-1">{{ $money($stats['todaySales'] ?? 0) }}</p></div>
    </div>
    <div class="bg-gradient-to-br from-sky-500 to-sky-600 border border-sky-400 rounded-xl p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10"><span class="text-[10px] font-medium text-sky-100">Today's Count</span><p class="text-xl font-bold mt-1">{{ $stats['todayCount'] ?? 0 }}</p></div>
    </div>
    <div class="bg-gradient-to-br from-amber-400 to-amber-500 border border-amber-300 rounded-xl p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10"><span class="text-[10px] font-medium text-amber-50">Month Sales</span><p class="text-xl font-bold mt-1">{{ $money($stats['monthSales'] ?? 0) }}</p></div>
    </div>
    <div class="bg-gradient-to-br from-violet-500 to-violet-600 border border-violet-400 rounded-xl p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10"><span class="text-[10px] font-medium text-violet-100">Products</span><p class="text-xl font-bold mt-1">{{ $stats['totalProducts'] ?? 0 }}</p></div>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">POS Sales (14 days)</h3>
        <canvas id="cashierChart" height="120"></canvas>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('admin.pos.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
                POS Terminal
            </a>
            <a href="{{ route('admin.sales-invoices.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-sky-50 hover:bg-sky-100 text-sky-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Sales Invoices
            </a>
            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-violet-50 hover:bg-violet-100 text-violet-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Products
            </a>
        </div>
    </div>
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach($secondaryKpis as $kpi)
    <a href="{{ route($kpi['route']) }}" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow">
        <span class="text-[10px] font-medium text-gray-500">{{ $kpi['label'] }}</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $kpi['value'] }}</p>
    </a>
    @endforeach
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-900">Recent POS Sales</h3>
        <a href="{{ route('admin.pos.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
    </div>
    <div class="divide-y divide-gray-100">
        @if(!empty($recentItems['recentSales']))
        @foreach($recentItems['recentSales']->take(8) as $sale)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
            <div><p class="text-xs font-medium text-gray-900">Sale #{{ $sale->id }}</p><p class="text-[10px] text-gray-400">{{ $sale->created_at->format('d M Y H:i') }}</p></div>
            <p class="text-xs font-semibold text-emerald-600">TZS {{ number_format($sale->total_amount) }}</p>
        </div>
        @endforeach
        @else
        <div class="px-5 py-8 text-center text-gray-400 text-xs">No recent sales</div>
        @endif
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('cashierChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: { labels: @json($chartData['labels']), datasets: [{ label: 'POS Sales', data: @json($chartData['values']), backgroundColor: '#024938', borderRadius: 4 }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } }, scales: { y: { beginAtZero: true, ticks: { font: { size: 9 }, callback: function(v) { return 'TZS ' + v.toLocaleString(); } } }, x: { ticks: { font: { size: 8 } } } } }
    });
}
</script>
@endsection
