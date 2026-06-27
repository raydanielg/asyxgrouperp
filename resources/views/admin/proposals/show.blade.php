@extends('layouts.admin')
@section('title', 'Quotation - ' . config('app.name'))
@section('page_title', 'Quotation Details')
@section('content')
<div class="max-w-4xl">
    {{-- Actions Bar --}}
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('admin.sales-proposals.index') }}" class="text-xs text-gray-500 hover:text-emerald-600 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Back to Quotations</a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sales-proposals.edit', $salesProposal) }}" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit</a>
            @if($salesProposal->status === 'accepted' && !$salesProposal->converted_to_invoice)<form method="POST" action="{{ route('admin.sales-proposals.convert', $salesProposal) }}">@csrf<button type="submit" class="px-3 py-1.5 bg-amber-600 text-white text-xs font-medium rounded-lg hover:bg-amber-700" onclick="return confirm('Convert to invoice?')">→ Convert to Invoice</button></form>@endif
        </div>
    </div>

    {{-- Quotation Document --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        {{-- Header with Gradient --}}
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 px-8 py-6 text-white">
            <div class="flex items-start justify-between">
                <div><h2 class="text-2xl font-bold">QUOTATION</h2><p class="text-emerald-100 text-xs mt-1">{{ $salesProposal->proposal_number }}</p></div>
                <div class="text-right"><p class="text-emerald-100 text-[10px] uppercase">Status</p>@php $c=['draft'=>'gray','sent'=>'sky','accepted'=>'emerald','rejected'=>'red']; @endphp<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white border border-white/30">{{ ucfirst($salesProposal->status) }}</span></div>
            </div>
        </div>

        {{-- Customer & Date Info --}}
        <div class="px-8 py-6 grid grid-cols-1 md:grid-cols-2 gap-6 border-b">
            <div><p class="text-[10px] text-gray-400 uppercase mb-2">From</p><p class="text-sm font-bold text-gray-900">{{ config('app.name') }}</p></div>
            <div><p class="text-[10px] text-gray-400 uppercase mb-2">Bill To</p><p class="text-sm font-bold text-gray-900">{{ $salesProposal->customer?->name ?? 'N/A' }}</p><p class="text-xs text-gray-500">{{ $salesProposal->customer?->email ?? '' }}</p></div>
            <div><p class="text-[10px] text-gray-400 uppercase mb-1">Date</p><p class="text-sm text-gray-700">{{ $salesProposal->proposal_date->format('d M Y') }}</p></div>
            <div><p class="text-[10px] text-gray-400 uppercase mb-1">Valid Until</p><p class="text-sm text-gray-700">{{ $salesProposal->due_date->format('d M Y') }}</p></div>
        </div>

        {{-- Items Table --}}
        @if($salesProposal->items->count() > 0)
        <div class="px-8 py-6">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50 border-b">
                    <th class="px-4 py-3 font-medium">#</th><th class="px-4 py-3 font-medium">Description</th><th class="px-4 py-3 font-medium text-center">Qty</th><th class="px-4 py-3 font-medium text-right">Unit Price</th><th class="px-4 py-3 font-medium text-right">Discount</th><th class="px-4 py-3 font-medium text-right">Tax</th><th class="px-4 py-3 font-medium text-right">Total</th>
                </tr></thead>
                <tbody>@foreach($salesProposal->items as $i => $item)<tr class="border-b border-gray-100">
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 text-xs text-gray-700 font-medium">{{ $item->product_name }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500 text-center">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500 text-right">TZS {{ number_format($item->unit_price) }}</td>
                    <td class="px-4 py-3 text-xs text-red-500 text-right">TZS {{ number_format($item->discount_amount) }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500 text-right">{{ $item->tax_percentage }}%</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-900 text-right">TZS {{ number_format($item->total_amount) }}</td>
                </tr>@endforeach</tbody>
            </table>
        </div>
        @endif

        {{-- Summary --}}
        <div class="px-8 py-6 bg-gray-50/50 border-t">
            <div class="flex justify-end">
                <div class="w-80 space-y-2">
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900">TZS {{ number_format($salesProposal->subtotal) }}</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Discount</span><span class="font-medium text-red-600">TZS {{ number_format($salesProposal->discount_amount) }}</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Tax</span><span class="font-medium text-gray-900">TZS {{ number_format($salesProposal->tax_amount) }}</span></div>
                    <div class="border-t-2 border-emerald-300 pt-2 flex justify-between items-center"><span class="font-bold text-gray-900">Total</span><span class="font-bold text-xl text-emerald-700">TZS {{ number_format($salesProposal->total_amount) }}</span></div>
                </div>
            </div>
        </div>

        {{-- Terms & Notes --}}
        @if($salesProposal->payment_terms || $salesProposal->notes)
        <div class="px-8 py-6 border-t space-y-4">
            @if($salesProposal->payment_terms)<div><p class="text-[10px] text-gray-400 uppercase mb-1">Payment Terms</p><p class="text-xs text-gray-600">{{ $salesProposal->payment_terms }}</p></div>@endif
            @if($salesProposal->notes)<div><p class="text-[10px] text-gray-400 uppercase mb-1">Notes</p><p class="text-xs text-gray-600">{{ $salesProposal->notes }}</p></div>@endif
        </div>
        @endif
    </div>

    {{-- Status Update --}}
    <div class="mt-4 bg-white rounded-xl border p-4 flex items-center gap-3">
        <span class="text-xs text-gray-500">Update Status:</span>
        <form method="POST" action="{{ route('admin.sales-proposals.status', $salesProposal) }}" class="flex items-center gap-2">@csrf @method('PATCH')
            <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none">
                <option value="draft" {{ $salesProposal->status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ $salesProposal->status === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="accepted" {{ $salesProposal->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="rejected" {{ $salesProposal->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </form>
    </div>
</div>
@endsection
