@extends('layouts.admin')
@section('title', 'Proposal - ' . config('app.name'))
@section('page_title', 'Proposal Details')
@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border p-6 mb-4">
        <div class="flex items-start justify-between mb-6">
            <div><p class="text-xs text-gray-400 mb-1">Proposal Number</p><h2 class="text-xl font-bold text-gray-900">{{ $salesProposal->proposal_number }}</h2></div>
            <form method="POST" action="{{ route('admin.sales-proposals.status', $salesProposal) }}" class="flex items-center gap-2">@csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none">
                    <option value="draft" {{ $salesProposal->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ $salesProposal->status === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="accepted" {{ $salesProposal->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ $salesProposal->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><p class="text-xs text-gray-400 mb-1">Customer</p><p class="text-sm font-medium text-gray-900">{{ $salesProposal->customer?->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Proposal Date</p><p class="text-sm text-gray-700">{{ $salesProposal->proposal_date->format('d M Y') }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Due Date</p><p class="text-sm text-gray-700">{{ $salesProposal->due_date->format('d M Y') }}</p></div>
        </div>
        <div class="border-t pt-4 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900">${{ number_format($salesProposal->subtotal, 2) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Tax</span><span class="font-medium text-gray-900">${{ number_format($salesProposal->tax_amount, 2) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Discount</span><span class="font-medium text-gray-900">${{ number_format($salesProposal->discount_amount, 2) }}</span></div>
            <div class="flex justify-between text-base font-bold pt-2 border-t"><span class="text-gray-900">Total</span><span class="text-gray-900">${{ number_format($salesProposal->total_amount, 2) }}</span></div>
        </div>
        @if($salesProposal->notes)<div class="mt-4 pt-4 border-t"><p class="text-xs text-gray-400 mb-1">Notes</p><p class="text-sm text-gray-600">{{ $salesProposal->notes }}</p></div>@endif
    </div>
    @if($salesProposal->items->count() > 0)
    <div class="bg-white rounded-xl border overflow-hidden"><div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Items</h3></div>
        <table class="w-full text-sm"><thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-2 font-medium">Product</th><th class="px-5 py-2 font-medium">Qty</th><th class="px-5 py-2 font-medium">Unit Price</th><th class="px-5 py-2 font-medium">Total</th></tr></thead>
        <tbody>@foreach($salesProposal->items as $item)<tr class="border-t border-gray-100"><td class="px-5 py-2 text-xs text-gray-700">{{ $item->product_name }}</td><td class="px-5 py-2 text-xs text-gray-500">{{ $item->quantity }}</td><td class="px-5 py-2 text-xs text-gray-500">${{ number_format($item->unit_price, 2) }}</td><td class="px-5 py-2 text-xs font-semibold text-gray-900">${{ number_format($item->total_amount, 2) }}</td></tr>@endforeach</tbody></table>
    </div>@endif
</div>
@endsection
