@extends('layouts.admin')
@section('title', 'Purchase Return - ' . config('app.name'))
@section('page_title', 'Return Details')
@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border p-6 mb-4">
        <div class="flex items-start justify-between mb-6">
            <div><p class="text-xs text-gray-400 mb-1">Return Number</p><h2 class="text-xl font-bold text-gray-900">{{ $purchaseReturn->return_number }}</h2></div>
            @php $c=['draft'=>'gray','approved'=>'sky','completed'=>'emerald','cancelled'=>'red']; @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $c[$purchaseReturn->status] ?? 'gray' }}-50 text-{{ $c[$purchaseReturn->status] ?? 'gray' }}-700 border border-{{ $c[$purchaseReturn->status] ?? 'gray' }}-100">{{ ucfirst($purchaseReturn->status) }}</span>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><p class="text-xs text-gray-400 mb-1">Vendor</p><p class="text-sm font-medium text-gray-900">{{ $purchaseReturn->vendor?->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Original Invoice</p><p class="text-sm font-medium text-gray-900">{{ $purchaseReturn->originalInvoice?->invoice_number ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Return Date</p><p class="text-sm text-gray-700">{{ $purchaseReturn->return_date->format('d M Y') }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Reason</p><p class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $purchaseReturn->reason)) }}</p></div>
        </div>
        <div class="border-t pt-4 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900">${{ number_format($purchaseReturn->subtotal, 2) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Tax</span><span class="font-medium text-gray-900">${{ number_format($purchaseReturn->tax_amount, 2) }}</span></div>
            <div class="flex justify-between text-base font-bold pt-2 border-t"><span class="text-gray-900">Total</span><span class="text-gray-900">${{ number_format($purchaseReturn->total_amount, 2) }}</span></div>
        </div>
        @if($purchaseReturn->notes)<div class="mt-4 pt-4 border-t"><p class="text-xs text-gray-400 mb-1">Notes</p><p class="text-sm text-gray-600">{{ $purchaseReturn->notes }}</p></div>@endif
    </div>
    @if($purchaseReturn->items->count() > 0)
    <div class="bg-white rounded-xl border overflow-hidden"><div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Items</h3></div>
        <table class="w-full text-sm"><thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-2 font-medium">Product</th><th class="px-5 py-2 font-medium">Qty</th><th class="px-5 py-2 font-medium">Unit Price</th><th class="px-5 py-2 font-medium">Total</th></tr></thead>
        <tbody>@foreach($purchaseReturn->items as $item)<tr class="border-t border-gray-100"><td class="px-5 py-2 text-xs text-gray-700">{{ $item->product_name }}</td><td class="px-5 py-2 text-xs text-gray-500">{{ $item->quantity }}</td><td class="px-5 py-2 text-xs text-gray-500">${{ number_format($item->unit_price, 2) }}</td><td class="px-5 py-2 text-xs font-semibold text-gray-900">${{ number_format($item->total_amount, 2) }}</td></tr>@endforeach</tbody></table>
    </div>@endif
</div>
@endsection
