@extends('layouts.admin')
@section('title', 'Sales Dashboard - ' . config('app.name'))
@section('page_title', 'Sales Dashboard')
@section('content')

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
        <span class="text-xs text-gray-400">Sales performance overview</span>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sales-proposals.index') }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Quotations</a>
            <a href="{{ route('admin.sales-invoices.index') }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Invoices</a>
            <a href="{{ route('admin.sales-proposals.create') }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">+ Quotation</a>
            <a href="{{ route('admin.sales-invoices.create') }}" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">+ Invoice</a>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100">
        <div class="px-5 py-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Proposals</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalProposals'] }}</p>
        </div>
        <div class="px-5 py-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Invoices</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalInvoices'] }}</p>
        </div>
        <div class="px-5 py-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Revenue</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">TZS {{ number_format($stats['totalRevenue']) }}</p>
        </div>
        <div class="px-5 py-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Outstanding</p>
            <p class="text-2xl font-bold text-red-500 mt-1">TZS {{ number_format($stats['totalOutstanding']) }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- Recent Proposals --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Recent Quotations</h3>
            <a href="{{ route('admin.sales-proposals.index') }}" class="text-[11px] text-gray-400 hover:text-gray-700">View all →</a>
        </div>
        <div class="divide-y divide-gray-100">
        @forelse($recentProposals as $p)
        @php $pc = ['draft'=>'#9ca3af','sent'=>'#3b82f6','accepted'=>'#10b981','rejected'=>'#ef4444'][$p->status] ?? '#9ca3af'; @endphp
        <a href="{{ route('admin.sales-proposals.index') }}" class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/60 transition-colors">
            <div class="min-w-0">
                <p class="text-xs font-mono font-medium text-gray-900">{{ $p->proposal_number }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $p->customer?->name ?? '—' }} · {{ $p->proposal_date ? \Illuminate\Support\Carbon::parse($p->proposal_date)->format('d M Y') : '—' }}</p>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                <p class="text-xs font-mono font-bold text-gray-900">TZS {{ number_format($p->total_amount) }}</p>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 w-20"><span style="width:6px;height:6px;border-radius:99px;background:{{ $pc }}"></span>{{ ucfirst($p->status) }}</span>
            </div>
        </a>
        @empty
        <div class="px-5 py-10 text-center text-gray-400 text-xs">No quotations yet</div>
        @endforelse
        </div>
    </div>

    {{-- Recent Invoices --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Recent Invoices</h3>
            <a href="{{ route('admin.sales-invoices.index') }}" class="text-[11px] text-gray-400 hover:text-gray-700">View all →</a>
        </div>
        <div class="divide-y divide-gray-100">
        @forelse($recentInvoices as $inv)
        @php $ic = ['draft'=>'#9ca3af','posted'=>'#3b82f6','partial'=>'#f59e0b','paid'=>'#10b981','overdue'=>'#ef4444'][$inv->status] ?? '#9ca3af'; @endphp
        <a href="{{ route('admin.sales-invoices.index') }}" class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/60 transition-colors">
            <div class="min-w-0">
                <p class="text-xs font-mono font-medium text-gray-900">{{ $inv->invoice_number }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $inv->customer?->name ?? '—' }} · {{ $inv->invoice_date ? \Illuminate\Support\Carbon::parse($inv->invoice_date)->format('d M Y') : '—' }}</p>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                <p class="text-xs font-mono font-bold text-gray-900">TZS {{ number_format($inv->total_amount) }}</p>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 w-20"><span style="width:6px;height:6px;border-radius:99px;background:{{ $ic }}"></span>{{ ucfirst($inv->status) }}</span>
            </div>
        </a>
        @empty
        <div class="px-5 py-10 text-center text-gray-400 text-xs">No invoices yet</div>
        @endforelse
        </div>
    </div>
</div>
@endsection
