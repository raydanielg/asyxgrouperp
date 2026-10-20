@extends('layouts.admin')

@section('title', 'Purchase Invoices - ' . config('app.name'))
@section('page_title', 'Purchase Invoices')

@section('content')
@php
$totalAmount = $invoices->sum('total_amount');
$totalBalance = $invoices->sum('balance_amount');
$paidCount = $invoices->where('status', 'paid')->count();
$overdueCount = $invoices->where('status', 'overdue')->count();
@endphp

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
        <span class="text-xs text-gray-400">Manage purchase invoices from vendors</span>
        <a href="{{ route('admin.purchase-invoices.create') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Invoice</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Invoiced</p><p class="text-lg font-bold text-gray-900 mt-0.5">TZS {{ number_format($totalAmount) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Outstanding</p><p class="text-lg font-bold text-amber-600 mt-0.5">TZS {{ number_format($totalBalance) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Paid</p><p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $paidCount }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Overdue</p><p class="text-lg font-bold text-red-500 mt-0.5">{{ $overdueCount }}</p></div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Invoice #</th>
                <th class="px-5 py-3 font-semibold">Vendor</th>
                <th class="px-5 py-3 font-semibold text-right">Total</th>
                <th class="px-5 py-3 font-semibold text-right">Balance</th>
                <th class="px-5 py-3 font-semibold">Status</th>
                <th class="px-5 py-3 font-semibold">Date</th>
                <th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($invoices as $invoice)
        @php $sc = ['draft'=>'#9ca3af','posted'=>'#3b82f6','partial'=>'#f59e0b','paid'=>'#10b981','overdue'=>'#ef4444'][$invoice->status] ?? '#9ca3af'; @endphp
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.purchase-invoices.show', $invoice) }}" class="text-xs font-mono font-medium text-gray-900 hover:text-gray-600">{{ $invoice->invoice_number }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-800">{{ $invoice->vendor?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold text-gray-900">TZS {{ number_format($invoice->total_amount) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono {{ $invoice->balance_amount > 0 ? 'text-amber-600 font-medium' : 'text-gray-400' }}">TZS {{ number_format($invoice->balance_amount) }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc }}"></span>{{ ucfirst($invoice->status) }}</span></td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $invoice->invoice_date ? \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('d M Y') : '—' }}</td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.purchase-invoices.show', $invoice) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <a href="{{ route('admin.purchase-invoices.edit', $invoice) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</a>
                    @if($invoice->status === 'draft')
                    <form method="POST" action="{{ route('admin.purchase-invoices.post', $invoice) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Post invoice {{ $invoice->invoice_number }}?'})">@csrf
                        <button class="text-xs font-medium text-blue-600 hover:text-blue-700">Post</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('admin.purchase-invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete invoice {{ $invoice->invoice_number }}?'})">@csrf @method('DELETE')
                        <button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No invoices found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($invoices->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection
