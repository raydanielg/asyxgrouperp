@extends('layouts.admin')
@section('title', 'Finance Manager')
@section('page_title', 'Finance Command')
@section('content')
@php
    $money = fn($n) => 'TZS ' . number_format($n ?? 0);
    $net = ($stats['totalRevenues'] ?? 0) - ($stats['totalExpenses'] ?? 0);
    $arRate = ($stats['totalSales'] ?? 0) > 0 ? round(($stats['salesPaid'] ?? 0) / $stats['totalSales'] * 100) : 0;
@endphp

{{-- Header --}}
<div class="bg-slate-800 rounded-md p-6 mb-5 text-white">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Finance Command</p>
            <h2 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-slate-400 text-xs mt-1">Ledger · cash flow · receivables · payables.</p>
        </div>
        <div class="text-right">
            <p class="text-slate-300 text-xs">{{ now()->format('l, d M Y') }}</p>
            <p class="text-slate-500 text-[10px] mt-1">Collection rate: {{ $arRate }}%</p>
        </div>
    </div>
</div>

{{-- Primary KPIs --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Revenue</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $money($stats['totalRevenues']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">Month: {{ $money($stats['monthRevenues']) }}</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Expenses</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $money($stats['totalExpenses']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">Month: {{ $money($stats['monthExpenses']) }}</p>
    </div>
    <div class="bg-white rounded-md border {{ $net >= 0 ? 'border-emerald-200' : 'border-red-200' }} p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Net Position</span>
        <p class="text-lg font-bold {{ $net >= 0 ? 'text-emerald-700' : 'text-red-600' }} mt-1">{{ $money($net) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">Revenue − expenses</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Receivable (AR)</span>
        <p class="text-lg font-bold text-amber-600 mt-1">{{ $money($stats['salesBalance']) }}</p>
        <p class="text-[10px] {{ ($stats['overdueInvoices'] ?? 0) > 0 ? 'text-red-500' : 'text-gray-400' }} mt-0.5">{{ $stats['overdueInvoices'] ?? 0 }} overdue</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Payable (AP)</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $money($stats['purchaseBalance']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">of {{ $money($stats['totalPurchases']) }} purchases</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Cash Collected</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $money($stats['salesPaid']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $arRate }}% of invoiced</p>
    </div>
</div>

{{-- Chart + Pulse --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2 bg-white rounded-md border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Cash Flow — Last 14 Days</h3>
            <a href="{{ route('role.page', ['module' => 'financial-reports']) }}" class="text-[10px] text-slate-600 hover:text-slate-800">Full report →</a>
        </div>
        <canvas id="finChart" height="110"></canvas>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Ledger Pulse</h3>
        <div class="space-y-2.5">
            @php
            $pulse = [
                ['label' => 'Pending approvals', 'value' => $stats['pendingApprovals'] ?? '—', 'link' => 'approvals'],
                ['label' => 'Draft invoices', 'value' => $stats['draftInvoices'] ?? 0, 'link' => 'sales-invoices'],
                ['label' => 'Salary advances pending', 'value' => $salaryAdvancePending ?? 0, 'link' => 'payroll'],
                ['label' => 'Salary advances approved', 'value' => $salaryAdvanceApproved ?? 0, 'link' => 'payroll'],
                ['label' => 'Pending leave', 'value' => $stats['pendingLeaves'] ?? 0, 'link' => 'employees'],
            ];
            $dot = fn($v) => is_numeric($v) && $v > 0 ? '#f59e0b' : '#94a3b8';
            @endphp
            @foreach($pulse as $p)
            <a href="{{ route('role.page', ['module' => $p['link']]) }}" class="flex items-center justify-between hover:bg-gray-50 -mx-2 px-2 py-1 rounded">
                <span class="text-xs text-gray-600">{{ $p['label'] }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-900"><span style="width:6px;height:6px;border-radius:99px;background:{{ $dot($p['value']) }}"></span>{{ $p['value'] }}</span>
            </a>
            @endforeach
        </div>
        <div class="mt-4 pt-3 border-t border-gray-100">
            <span class="text-[10px] uppercase text-gray-400">AR collection</span>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1.5"><div class="bg-emerald-600 h-1.5 rounded-full" style="width:{{ $arRate }}%"></div></div>
        </div>
    </div>
</div>

{{-- Actions + recent --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Finance Actions</h3>
        <div class="space-y-2">
            @php
            $actions = [
                ['module' => 'journal-entries', 'label' => 'Journal Entries'],
                ['module' => 'bank-reconciliation', 'label' => 'Bank Reconciliation'],
                ['module' => 'acc-transfers', 'label' => 'Account Transfers'],
                ['module' => 'expenses', 'label' => 'Expenses'],
                ['module' => 'sales-invoices', 'label' => 'Sales Invoices'],
                ['module' => 'purchase-invoices', 'label' => 'Purchase Invoices'],
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
        <h3 class="text-sm font-bold text-gray-900 mb-4">Accounts Snapshot</h3>
        <div class="grid grid-cols-2 gap-3">
            <div class="border border-gray-100 rounded p-3">
                <p class="text-[9px] uppercase text-gray-400">Invoiced</p>
                <p class="text-sm font-bold text-gray-900">{{ $money($stats['totalSales']) }}</p>
            </div>
            <div class="border border-gray-100 rounded p-3">
                <p class="text-[9px] uppercase text-gray-400">Collected</p>
                <p class="text-sm font-bold text-emerald-700">{{ $money($stats['salesPaid']) }}</p>
            </div>
            <div class="border border-gray-100 rounded p-3">
                <p class="text-[9px] uppercase text-gray-400">Purchases</p>
                <p class="text-sm font-bold text-gray-900">{{ $money($stats['totalPurchases']) }}</p>
            </div>
            <div class="border border-gray-100 rounded p-3">
                <p class="text-[9px] uppercase text-gray-400">Payroll advances</p>
                <p class="text-sm font-bold text-gray-900">{{ $money($salaryAdvanceTotal ?? 0) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Invoices</h3>
            <a href="{{ route('role.page', ['module' => 'sales-invoices']) }}" class="text-[10px] text-slate-600 hover:text-slate-800">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse(($recentItems['recentSales'] ?? collect())->take(5) as $invoice)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $invoice->invoice_number }}</p><p class="text-[10px] text-gray-400">{{ $invoice->customer?->name ?? '—' }}</p></div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-gray-900">{{ $money($invoice->total_amount) }}</p>
                    <p class="text-[10px] {{ $invoice->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">{{ ucfirst($invoice->status) }}</p>
                </div>
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
const ctx = document.getElementById('finChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels'] ?? []),
            datasets: [
                { label: 'In', data: @json($chartData['values'] ?? []), borderColor: '#059669', backgroundColor: 'rgba(5,150,105,.08)', tension: .35, fill: true, pointRadius: 2 },
                @if(!empty($chartData['secondaryValues']))
                { label: '{{ $chartData['secondaryTitle'] ?? 'Out' }}', data: @json($chartData['secondaryValues']), borderColor: '#94a3b8', backgroundColor: 'transparent', tension: .35, pointRadius: 2, borderDash: [4,4] },
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
