@extends('layouts.admin')
@section('title', 'Sales Manager')
@section('page_title', 'Sales Command')
@section('content')
@php
    $money = fn($n) => 'TZS ' . number_format($n ?? 0);
    $conv = ($stats['totalLeads'] ?? 0) > 0 ? round(($stats['qualifiedLeads'] ?? 0) / $stats['totalLeads'] * 100) : 0;
@endphp

<div class="bg-slate-800 rounded-md p-6 mb-5 text-white">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Sales Command</p>
            <h2 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-slate-400 text-xs mt-1">Pipeline, leads, quotations, targets.</p>
        </div>
        <div class="text-right">
            <p class="text-slate-300 text-xs">{{ now()->format('l, d M Y') }}</p>
            <p class="text-slate-500 text-[10px] mt-1">Lead conversion: {{ $conv }}%</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Pipeline</span>
        <p class="text-lg font-bold text-emerald-700 mt-1">{{ $money($stats['totalDealValue']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $stats['openDeals'] ?? 0 }} open deals</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Leads</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalLeads'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $stats['newLeads'] ?? 0 }} new</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Qualified</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['qualifiedLeads'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $conv }}% conversion</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">My leads</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['myLeads'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">assigned to me</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Quotations</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalQuotations'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">all quotes</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Open tickets</span>
        <p class="text-2xl font-bold {{ ($stats['openTickets'] ?? 0) > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-1">{{ $stats['openTickets'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">customer issues</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2 bg-white rounded-md border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Sales — Last 14 Days</h3>
            <a href="{{ route('role.page', ['module' => 'reports']) }}" class="text-[10px] text-slate-600 hover:text-slate-800">Full report →</a>
        </div>
        <canvas id="salesChart" height="110"></canvas>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Pipeline Pulse</h3>
        <div class="space-y-2.5">
            @php
            $pulse = [
                ['label' => 'New leads', 'value' => $stats['newLeads'] ?? 0, 'link' => 'leads'],
                ['label' => 'My leads', 'value' => $stats['myLeads'] ?? 0, 'link' => 'leads'],
                ['label' => 'Open deals', 'value' => $stats['openDeals'] ?? 0, 'link' => 'deals'],
                ['label' => 'Quotations', 'value' => $stats['totalQuotations'] ?? 0, 'link' => 'quotations'],
                ['label' => 'Contacts', 'value' => $stats['totalContacts'] ?? 0, 'link' => 'contacts'],
            ];
            $dot = fn($v) => $v > 0 ? '#f59e0b' : '#94a3b8';
            @endphp
            @foreach($pulse as $p)
            <a href="{{ route('role.page', ['module' => $p['link']]) }}" class="flex items-center justify-between hover:bg-gray-50 -mx-2 px-2 py-1 rounded">
                <span class="text-xs text-gray-600">{{ $p['label'] }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-900"><span style="width:6px;height:6px;border-radius:99px;background:{{ $dot($p['value']) }}"></span>{{ $p['value'] }}</span>
            </a>
            @endforeach
        </div>
        <div class="mt-4 pt-3 border-t border-gray-100">
            <span class="text-[10px] uppercase text-gray-400">Conversion</span>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1.5"><div class="bg-emerald-600 h-1.5 rounded-full" style="width:{{ $conv }}%"></div></div>
        </div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 p-5 mb-5">
    <h3 class="text-sm font-bold text-gray-900 mb-4">Sales Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
        @php
        $actions = [
            ['module' => 'leads', 'label' => 'Leads'],
            ['module' => 'deals', 'label' => 'Deals Pipeline'],
            ['module' => 'quotations', 'label' => 'Quotations'],
            ['module' => 'contacts', 'label' => 'Contacts'],
            ['module' => 'campaigns', 'label' => 'Campaigns'],
            ['module' => 'sales-invoices', 'label' => 'Sales Invoices'],
            ['module' => 'tenders', 'label' => 'Tenders'],
            ['module' => 'sales-forecast', 'label' => 'Forecast'],
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels'] ?? []),
            datasets: [{ label: '{{ $chartData['title'] ?? 'Sales' }}', data: @json($chartData['values'] ?? []), borderColor: '#059669', backgroundColor: 'rgba(5,150,105,.08)', tension: .35, fill: true, pointRadius: 2 }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { font: { size: 9 } }, grid: { color: '#f1f5f9' } }, x: { ticks: { font: { size: 8 } }, grid: { display: false } } }
        }
    });
}
</script>
@endsection
