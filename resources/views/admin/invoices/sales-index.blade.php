@extends('layouts.admin')
@section('title', 'Sales Invoices - ' . config('app.name'))
@section('page_title', 'Sales Invoices')
@section('content')
@php
$totalAmount = $invoices->sum('total_amount');
$totalBalance = $invoices->sum('balance_amount');
$paidCount = $invoices->where('status', 'paid')->count();
$overdueCount = $invoices->where('status', 'overdue')->count();
$draftCount = $invoices->where('status', 'draft')->count();
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Total Invoiced</span>
        <p class="text-xl font-bold text-gray-900 mt-1">TZS {{ number_format($totalAmount) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-amber-200 p-4">
        <span class="text-[10px] font-medium text-amber-600 uppercase tracking-wider">Outstanding</span>
        <p class="text-xl font-bold text-amber-700 mt-1">TZS {{ number_format($totalBalance) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-emerald-200 p-4">
        <span class="text-[10px] font-medium text-emerald-600 uppercase tracking-wider">Paid</span>
        <p class="text-xl font-bold text-emerald-700 mt-1">{{ $paidCount }}</p>
    </div>
    <div class="bg-white rounded-xl border border-rose-200 p-4">
        <span class="text-[10px] font-medium text-rose-600 uppercase tracking-wider">Overdue</span>
        <p class="text-xl font-bold text-rose-700 mt-1">{{ $overdueCount }}</p>
    </div>
</div>

<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 text-xs text-gray-500">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Draft: {{ $draftCount }}</span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200">Posted: {{ $invoices->where('status', 'posted')->count() }}</span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Partial: {{ $invoices->where('status', 'partial')->count() }}</span>
        </div>
    </div>
    <a href="{{ route('admin.sales-invoices.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Invoice
    </a>
</div>

<div class="bg-white rounded-xl border overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Invoice #</th>
                <th class="px-5 py-3 font-medium">Customer</th>
                <th class="px-5 py-3 font-medium text-right">Total</th>
                <th class="px-5 py-3 font-medium text-right">Balance</th>
                <th class="px-5 py-3 font-medium">Type</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Date</th>
                <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr></thead>
            <tbody>
        @forelse($invoices as $invoice)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
            <td class="px-5 py-3 text-xs font-mono text-gray-700"><a href="{{ route('admin.sales-invoices.show', $invoice) }}" class="hover:text-emerald-600 font-semibold">{{ $invoice->invoice_number }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $invoice->customer?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TZS {{ number_format($invoice->total_amount) }}</td>
            <td class="px-5 py-3 text-xs text-right {{ $invoice->balance_amount > 0 ? 'text-amber-600 font-medium' : 'text-gray-500' }}">TZS {{ number_format($invoice->balance_amount) }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-100">{{ ucfirst($invoice->type) }}</span></td>
            <td class="px-5 py-3">
                @php $c=['draft'=>'gray','posted'=>'sky','partial'=>'amber','paid'=>'emerald','overdue'=>'red']; @endphp
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$invoice->status] ?? 'gray' }}-50 text-{{ $c[$invoice->status] ?? 'gray' }}-700 border border-{{ $c[$invoice->status] ?? 'gray' }}-100">
                    @if($invoice->status === 'paid')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>@endif
                    {{ ucfirst($invoice->status) }}
                </span>
            </td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $invoice->invoice_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                    <a href="{{ route('admin.sales-invoices.show', $invoice) }}" title="View" class="w-8 h-8 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="{{ route('admin.sales-invoices.edit', $invoice) }}" title="Edit" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    @if($invoice->status === 'draft')
                    <form method="POST" action="{{ route('admin.sales-invoices.post', $invoice) }}" class="inline">@csrf
                        <button title="Post" class="w-8 h-8 rounded-lg bg-violet-50 hover:bg-violet-100 text-violet-600 flex items-center justify-center transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('admin.sales-invoices.show', $invoice) }}" title="Print" target="_blank" class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('admin.sales-invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Delete invoice {{ $invoice->invoice_number }}?')">@csrf @method('DELETE')
                        <button title="Delete" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-12 text-center">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm text-gray-400">No invoices found</p>
                <a href="{{ route('admin.sales-invoices.create') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Create your first invoice →</a>
            </div>
        </td></tr>
        @endforelse
        </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t bg-gray-50/30">{{ $invoices->links() }}</div>
</div>
@endsection
