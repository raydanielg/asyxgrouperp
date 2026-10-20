@extends('layouts.admin')
@section('title', 'Managing Director')
@section('page_title', 'Executive Command')
@section('content')
@php
    $money = fn($n) => 'TZS ' . number_format($n ?? 0);
    $net = ($stats['totalRevenues'] ?? 0) - ($stats['totalExpenses'] ?? 0);
    $acceptRate = ($stats['totalProposals'] ?? 0) > 0
        ? round(($stats['acceptedProposals'] ?? 0) / $stats['totalProposals'] * 100) : 0;
@endphp

{{-- ═══ Header ═══ --}}
<div class="bg-slate-800 rounded-md p-6 mb-5 text-white">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Executive Command</p>
            <h2 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-slate-400 text-xs mt-1">Group-wide oversight — all companies, one view.</p>
        </div>
        <div class="text-right">
            <p class="text-slate-300 text-xs">{{ now()->format('l, d M Y') }}</p>
            <p class="text-slate-500 text-[10px] mt-1">{{ $stats['totalCompanies'] ?? 1 }} {{ Str::plural('company', $stats['totalCompanies'] ?? 1) }} under oversight</p>
        </div>
    </div>
</div>

{{-- ═══ Primary KPIs ═══ --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Revenue</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $money($stats['totalRevenues']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">This month: {{ $money($stats['monthRevenues']) }}</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Expenses</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $money($stats['totalExpenses']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">This month: {{ $money($stats['monthExpenses']) }}</p>
    </div>
    <div class="bg-white rounded-md border {{ $net >= 0 ? 'border-emerald-200' : 'border-red-200' }} p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Net Position</span>
        <p class="text-lg font-bold {{ $net >= 0 ? 'text-emerald-700' : 'text-red-600' }} mt-1">{{ $money($net) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">Revenue − expenses</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Receivables</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $money($stats['salesBalance']) }}</p>
        <p class="text-[10px] {{ ($stats['overdueInvoices'] ?? 0) > 0 ? 'text-red-500' : 'text-gray-400' }} mt-0.5">{{ $stats['overdueInvoices'] ?? 0 }} overdue</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Pipeline</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $money($stats['totalDealValue']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $stats['openDeals'] ?? 0 }} open deals</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Projects</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $stats['activeProjects'] ?? 0 }}<span class="text-xs text-gray-400 font-normal"> / {{ $stats['totalProjects'] ?? 0 }}</span></p>
        <p class="text-[10px] text-gray-400 mt-0.5">active / total</p>
    </div>
</div>

{{-- ═══ Chart + Pulse ═══ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2 bg-white rounded-md border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Sales vs Purchases — Last 14 Days</h3>
            <a href="{{ route('role.page', ['module' => 'financial-reports']) }}" class="text-[10px] text-slate-600 hover:text-slate-800">Full report →</a>
        </div>
        <canvas id="mdChart" height="110"></canvas>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Business Pulse</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600">Proposal win rate</span>
                <span class="text-xs font-bold text-gray-900">{{ $acceptRate }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-slate-700 h-1.5 rounded-full" style="width:{{ $acceptRate }}%"></div></div>

            <div class="pt-3 space-y-2.5 border-t border-gray-100">
                @php
                $pulse = [
                    ['label' => 'Pending approvals', 'value' => $secondaryKpis[0]['value'] ?? '—', 'link' => 'approvals'],
                    ['label' => 'Open tenders', 'value' => $stats['totalTenders'] ?? 0, 'link' => 'tenders'],
                    ['label' => 'Active contracts', 'value' => $stats['totalContracts'] ?? 0, 'link' => 'contracts'],
                    ['label' => 'Pending leave requests', 'value' => $stats['pendingLeaves'] ?? 0, 'link' => 'employees'],
                    ['label' => 'Open tickets', 'value' => $stats['openTickets'] ?? 0, 'link' => 'tickets'],
                ];
                @endphp
                @foreach($pulse as $p)
                <a href="{{ route('role.page', ['module' => $p['link']]) }}" class="flex items-center justify-between hover:bg-gray-50 -mx-2 px-2 py-1 rounded">
                    <span class="text-xs text-gray-600">{{ $p['label'] }}</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-900"><span style="width:6px;height:6px;border-radius:99px;background:#94a3b8"></span>{{ $p['value'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ═══ Quick Actions + Team ═══ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Executive Actions</h3>
        <div class="space-y-2">
            @php
            $actions = [
                ['module' => 'approvals', 'label' => 'Review Approvals'],
                ['module' => 'companies', 'label' => 'Company Overview'],
                ['module' => 'intercompany', 'label' => 'Intercompany'],
                ['module' => 'reports', 'label' => 'Reports'],
                ['module' => 'deals', 'label' => 'Deals Pipeline'],
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
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Organisation</h3>
        <div class="grid grid-cols-2 gap-3">
            <div class="border border-gray-100 rounded p-3">
                <p class="text-[9px] uppercase text-gray-400">Employees</p>
                <p class="text-base font-bold text-gray-900">{{ $stats['totalEmployees'] ?? 0 }}</p>
            </div>
            <div class="border border-gray-100 rounded p-3">
                <p class="text-[9px] uppercase text-gray-400">Companies</p>
                <p class="text-base font-bold text-gray-900">{{ $stats['totalCompanies'] ?? 1 }}</p>
            </div>
            <div class="border border-gray-100 rounded p-3">
                <p class="text-[9px] uppercase text-gray-400">Leads</p>
                <p class="text-base font-bold text-gray-900">{{ $stats['totalLeads'] ?? 0 }}</p>
            </div>
            <div class="border border-gray-100 rounded p-3">
                <p class="text-[9px] uppercase text-gray-400">Proposals</p>
                <p class="text-base font-bold text-gray-900">{{ $stats['totalProposals'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Invoices</h3>
            <a href="{{ route('role.page', ['module' => 'sales-invoices']) }}" class="text-[10px] text-slate-600 hover:text-slate-800">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse(($recentItems['recentInvoices'] ?? collect())->take(5) as $invoice)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $invoice->invoice_number }}</p><p class="text-[10px] text-gray-400">{{ $invoice->customer?->name ?? '—' }}</p></div>
                <div class="text-right"><p class="text-xs font-semibold text-gray-900">{{ $money($invoice->total_amount) }}</p><p class="text-[10px] text-gray-400">{{ $invoice->created_at?->format('d M') }}</p></div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-xs">No recent invoices</div>
            @endforelse
        </div>
    </div>
</div>

@include('roles.shared.ai-insights')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('mdChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels'] ?? []),
            datasets: [
                { label: 'Sales', data: @json($chartData['values'] ?? []), borderColor: '#1e293b', backgroundColor: 'rgba(30,41,59,.08)', tension: .35, fill: true, pointRadius: 2 },
                @if(!empty($chartData['secondaryValues']))
                { label: '{{ $chartData['secondaryTitle'] ?? 'Purchases' }}', data: @json($chartData['secondaryValues']), borderColor: '#94a3b8', backgroundColor: 'transparent', tension: .35, pointRadius: 2, borderDash: [4,4] },
                @endif
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, usePointStyle: true } } },
            scales: { y: { beginAtZero: true, ticks: { font: { size: 9 } }, grid: { color: '#f1f5f9' } }, x: { ticks: { font: { size: 8 } }, grid: { display: false } } }
        }
    });
}
</script>
@endsection
