@extends('layouts.admin')
@section('title', 'Sales Dashboard - ' . config('app.name'))
@section('page_title', 'Sales Dashboard')
@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between mb-2"><div class="p-2 bg-white/20 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div></div>
        <p class="text-3xl font-bold">{{ $stats['totalProposals'] }}</p>
        <p class="text-emerald-100 text-xs mt-1">Total Proposals</p>
    </div>
    <div class="bg-gradient-to-br from-sky-500 to-sky-700 rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between mb-2"><div class="p-2 bg-white/20 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div></div>
        <p class="text-3xl font-bold">{{ $stats['totalInvoices'] }}</p>
        <p class="text-sky-100 text-xs mt-1">Total Invoices</p>
    </div>
    <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between mb-2"><div class="p-2 bg-white/20 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div>
        <p class="text-2xl font-bold">TZS {{ number_format($stats['totalRevenue']) }}</p>
        <p class="text-amber-100 text-xs mt-1">Total Revenue</p>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-700 rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between mb-2"><div class="p-2 bg-white/20 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div></div>
        <p class="text-2xl font-bold">TZS {{ number_format($stats['totalOutstanding']) }}</p>
        <p class="text-red-100 text-xs mt-1">Outstanding Balance</p>
    </div>
</div>

{{-- Quick Actions --}}
<div class="bg-white rounded-xl border p-5 mb-6">
    <h3 class="text-sm font-bold text-gray-900 mb-3">Quick Actions</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.sales-proposals.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Quotation
        </a>
        <a href="{{ route('admin.sales-invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Invoice
        </a>
        <a href="{{ route('admin.sales-proposals.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            View Quotations
        </a>
        <a href="{{ route('admin.sales-invoices.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            View Invoices
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Proposals --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Quotations</h3>
            <a href="{{ route('admin.sales-proposals.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700">View All →</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentProposals as $p)
            <div class="px-5 py-3 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div><p class="text-xs font-mono text-gray-700">{{ $p->proposal_number }}</p><p class="text-[10px] text-gray-400">{{ $p->customer?->name ?? 'N/A' }} • {{ $p->proposal_date->format('d M Y') }}</p></div>
                    <div class="text-right"><p class="text-xs font-semibold text-gray-900">TZS {{ number_format($p->total_amount) }}</p>@php $c=['draft'=>'gray','sent'=>'sky','accepted'=>'emerald','rejected'=>'red']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[9px] bg-{{ $c[$p->status] ?? 'gray' }}-50 text-{{ $c[$p->status] ?? 'gray' }}-700">{{ ucfirst($p->status) }}</span></div>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-xs">No quotations yet</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Invoices --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Invoices</h3>
            <a href="{{ route('admin.sales-invoices.index') }}" class="text-xs text-sky-600 hover:text-sky-700">View All →</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentInvoices as $inv)
            <div class="px-5 py-3 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center justify-between">
                    <div><p class="text-xs font-mono text-gray-700">{{ $inv->invoice_number }}</p><p class="text-[10px] text-gray-400">{{ $inv->customer?->name ?? 'N/A' }} • {{ $inv->invoice_date->format('d M Y') }}</p></div>
                    <div class="text-right"><p class="text-xs font-semibold text-gray-900">TZS {{ number_format($inv->total_amount) }}</p>@php $c=['draft'=>'gray','posted'=>'sky','partial'=>'amber','paid'=>'emerald','overdue'=>'red']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[9px] bg-{{ $c[$inv->status] ?? 'gray' }}-50 text-{{ $c[$inv->status] ?? 'gray' }}-700">{{ ucfirst($inv->status) }}</span></div>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-xs">No invoices yet</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
