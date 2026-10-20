@extends('layouts.admin')
@section('title', 'Quotation - ' . config('app.name'))
@section('page_title', 'Quotation Details')
@section('content')
@php $sc = ['draft'=>'#9ca3af','sent'=>'#3b82f6','accepted'=>'#10b981','rejected'=>'#ef4444'][$salesProposal->status] ?? '#9ca3af'; @endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.sales-proposals.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Quotations</a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.sales-proposals.edit', $salesProposal) }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Edit</a>
        @if($salesProposal->status === 'accepted')
        <form method="POST" action="{{ route('admin.sales-proposals.convert-to-project', $salesProposal) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Convert this accepted quotation into a project?'})">@csrf
            <button type="submit" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">→ Convert to Project</button>
        </form>
        @endif
    </div>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm font-bold text-gray-900">{{ $salesProposal->proposal_number }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc }}"></span>{{ ucfirst($salesProposal->status) }}</span>
            </div>
            <h1 class="text-base font-bold text-gray-900 mt-1.5">Sales Quotation</h1>
            <p class="text-[11px] text-gray-400 mt-1">For {{ $salesProposal->customer?->name ?? '—' }}</p>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total</p>
            <p class="text-xl font-bold text-gray-900 mt-0.5">TZS {{ number_format($salesProposal->total_amount) }}</p>
        </div>
    </div>
    <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-xs">
        <div><p class="text-gray-400">Customer</p><p class="text-gray-800 mt-0.5">{{ $salesProposal->customer?->name ?? '—' }}</p></div>
        <div><p class="text-gray-400">Email</p><p class="text-gray-800 mt-0.5">{{ $salesProposal->customer?->email ?? '—' }}</p></div>
        <div><p class="text-gray-400">Date</p><p class="text-gray-800 mt-0.5">{{ $salesProposal->proposal_date->format('d M Y') }}</p></div>
        <div><p class="text-gray-400">Valid Until</p><p class="mt-0.5 {{ $salesProposal->due_date->isPast() && $salesProposal->status !== 'accepted' ? 'text-red-600 font-medium' : 'text-gray-800' }}">{{ $salesProposal->due_date->format('d M Y') }}</p></div>
    </div>
</div>

{{-- Items --}}
@if($salesProposal->items->count() > 0)
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Items</h3></div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100">
                <th class="px-5 py-2.5 font-semibold w-8">#</th><th class="px-5 py-2.5 font-semibold">Description</th><th class="px-5 py-2.5 font-semibold text-right">Qty</th><th class="px-5 py-2.5 font-semibold text-right">Unit Price</th><th class="px-5 py-2.5 font-semibold text-right">Discount</th><th class="px-5 py-2.5 font-semibold text-right">Tax</th><th class="px-5 py-2.5 font-semibold text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @foreach($salesProposal->items as $i => $item)
        <tr>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $item->product_name }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono text-gray-500">{{ $item->quantity }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono text-gray-500">{{ number_format($item->unit_price) }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono {{ $item->discount_amount > 0 ? 'text-red-500' : 'text-gray-300' }}">{{ $item->discount_amount > 0 ? number_format($item->discount_amount) : '—' }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono {{ $item->tax_percentage > 0 ? 'text-gray-500' : 'text-gray-300' }}">{{ $item->tax_percentage > 0 ? $item->tax_percentage . '%' : '—' }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono font-bold text-gray-900">{{ number_format($item->total_amount) }}</td>
        </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    {{-- Summary --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Summary</h3></div>
        <div class="divide-y divide-gray-100">
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Subtotal</span><span class="text-xs font-mono font-medium text-gray-900">TZS {{ number_format($salesProposal->subtotal) }}</span></div>
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Discount</span><span class="text-xs font-mono {{ $salesProposal->discount_amount > 0 ? 'text-red-500' : 'text-gray-400' }}">TZS {{ number_format($salesProposal->discount_amount) }}</span></div>
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Tax</span><span class="text-xs font-mono font-medium text-gray-900">TZS {{ number_format($salesProposal->tax_amount) }}</span></div>
            <div class="flex justify-between px-5 py-3.5 bg-gray-50/60"><span class="text-xs font-bold text-gray-900">Total</span><span class="text-sm font-mono font-bold text-gray-900">TZS {{ number_format($salesProposal->total_amount) }}</span></div>
        </div>
    </div>

    {{-- Terms, Notes & Status --}}
    <div class="space-y-4">
        @if($salesProposal->payment_terms || $salesProposal->notes)
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Terms &amp; Notes</h3></div>
            <div class="px-5 py-4 space-y-3">
                @if($salesProposal->payment_terms)
                <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Payment Terms</p><p class="text-xs text-gray-600">{{ $salesProposal->payment_terms }}</p></div>
                @endif
                @if($salesProposal->notes)
                <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Notes</p><p class="text-xs text-gray-600 whitespace-pre-line">{{ $salesProposal->notes }}</p></div>
                @endif
            </div>
        </div>
        @endif

        {{-- Status update --}}
        <div class="bg-white rounded-md border border-gray-200 px-5 py-4 flex items-center justify-between">
            <span class="text-xs font-medium text-gray-600">Update Status</span>
            <form method="POST" action="{{ route('admin.sales-proposals.status', $salesProposal) }}">@csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400 bg-white">
                    <option value="draft" {{ $salesProposal->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ $salesProposal->status === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="accepted" {{ $salesProposal->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ $salesProposal->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
    </div>
</div>
@endsection
