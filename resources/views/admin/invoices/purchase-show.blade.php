@extends('layouts.admin')

@section('title', 'Purchase Invoice - ' . config('app.name'))
@section('page_title', 'Invoice Details')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border p-6 mb-4">
        <div class="flex items-start justify-between mb-6">
            <div>
                <p class="text-xs text-gray-400 mb-1">Invoice Number</p>
                <h2 class="text-xl font-bold text-gray-900">{{ $purchaseInvoice->invoice_number }}</h2>
            </div>
            @php $colors = ['draft'=>'gray','posted'=>'sky','partial'=>'amber','paid'=>'emerald','overdue'=>'red']; @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $colors[$purchaseInvoice->status] ?? 'gray' }}-50 text-{{ $colors[$purchaseInvoice->status] ?? 'gray' }}-700 border border-{{ $colors[$purchaseInvoice->status] ?? 'gray' }}-100">{{ ucfirst($purchaseInvoice->status) }}</span>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><p class="text-xs text-gray-400 mb-1">Vendor</p><p class="text-sm font-medium text-gray-900">{{ $purchaseInvoice->vendor?->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Warehouse</p><p class="text-sm font-medium text-gray-900">{{ $purchaseInvoice->warehouse?->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Invoice Date</p><p class="text-sm text-gray-700">{{ $purchaseInvoice->invoice_date->format('d M Y') }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Due Date</p><p class="text-sm text-gray-700">{{ $purchaseInvoice->due_date->format('d M Y') }}</p></div>
        </div>
        <div class="border-t pt-4 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900">${{ number_format($purchaseInvoice->subtotal, 2) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Tax</span><span class="font-medium text-gray-900">${{ number_format($purchaseInvoice->tax_amount, 2) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Discount</span><span class="font-medium text-gray-900">${{ number_format($purchaseInvoice->discount_amount, 2) }}</span></div>
            <div class="flex justify-between text-base font-bold pt-2 border-t"><span class="text-gray-900">Total</span><span class="text-gray-900">${{ number_format($purchaseInvoice->total_amount, 2) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Paid</span><span class="font-medium text-emerald-600">${{ number_format($purchaseInvoice->paid_amount, 2) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Balance</span><span class="font-medium text-red-600">${{ number_format($purchaseInvoice->balance_amount, 2) }}</span></div>
        </div>
        @if($purchaseInvoice->notes)<div class="mt-4 pt-4 border-t"><p class="text-xs text-gray-400 mb-1">Notes</p><p class="text-sm text-gray-600">{{ $purchaseInvoice->notes }}</p></div>@endif
    </div>
    @if($purchaseInvoice->items->count() > 0)
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Items</h3></div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-2 font-medium">Product</th><th class="px-5 py-2 font-medium">Qty</th><th class="px-5 py-2 font-medium">Unit Price</th><th class="px-5 py-2 font-medium">Total</th></tr></thead>
            <tbody>@foreach($purchaseInvoice->items as $item)<tr class="border-t border-gray-100"><td class="px-5 py-2 text-xs text-gray-700">{{ $item->product_name }}</td><td class="px-5 py-2 text-xs text-gray-500">{{ $item->quantity }}</td><td class="px-5 py-2 text-xs text-gray-500">${{ number_format($item->unit_price, 2) }}</td><td class="px-5 py-2 text-xs font-semibold text-gray-900">${{ number_format($item->total_amount, 2) }}</td></tr>@endforeach</tbody>
        </table>
    </div>
    @endif
</div>
@endsection
