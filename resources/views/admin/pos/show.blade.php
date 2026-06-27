@extends('layouts.admin')
@section('title', 'POS Sale Details - ' . config('app.name'))
@section('page_title', 'Sale Receipt')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.pos.reports') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to POS Reports</a>
</div>
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border p-8">
        <div class="text-center mb-6">
            <h2 class="text-lg font-bold text-gray-900">{{ config('app.name') }}</h2>
            <p class="text-xs text-gray-400">POS Sale Receipt</p>
            <p class="text-xs font-mono text-gray-500 mt-1">{{ $posSale->sale_number }}</p>
        </div>
        <div class="flex justify-between text-xs mb-4 pb-4 border-b">
            <div><p class="text-gray-400">Date</p><p class="text-gray-700">{{ $posSale->created_at->format('d M Y H:i') }}</p></div>
            <div><p class="text-gray-400">Cashier</p><p class="text-gray-700">{{ $posSale->cashier?->name ?? 'N/A' }}</p></div>
            <div><p class="text-gray-400">Payment</p><p class="text-gray-700">{{ ucfirst($posSale->payment_method) }}</p></div>
        </div>
        <table class="w-full text-sm mb-4">
            <thead><tr class="text-left text-xs text-gray-500 border-b"><th class="py-2">Item</th><th class="py-2 text-center">Qty</th><th class="py-2 text-right">Price</th><th class="py-2 text-right">Total</th></tr></thead>
            <tbody>@foreach($posSale->items as $item)<tr class="border-b border-gray-50"><td class="py-2 text-xs text-gray-900">{{ $item->product_name }}</td><td class="py-2 text-center text-xs text-gray-500">{{ $item->quantity }}</td><td class="py-2 text-right text-xs text-gray-500">${{ number_format($item->unit_price, 2) }}</td><td class="py-2 text-right text-xs font-medium text-gray-900">${{ number_format($item->total, 2) }}</td></tr>@endforeach</tbody>
        </table>
        <div class="space-y-1 text-xs">
            <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="text-gray-900">${{ number_format($posSale->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Tax</span><span class="text-gray-700">${{ number_format($posSale->tax_amount, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Discount</span><span class="text-gray-700">-${{ number_format($posSale->discount_amount, 2) }}</span></div>
            <div class="flex justify-between text-sm font-bold border-t pt-2 mt-2"><span class="text-gray-900">Total</span><span class="text-emerald-700">${{ number_format($posSale->total_amount, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Paid</span><span class="text-gray-700">${{ number_format($posSale->paid_amount, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Change</span><span class="text-gray-700">${{ number_format($posSale->paid_amount - $posSale->total_amount, 2) }}</span></div>
        </div>
        <div class="text-center mt-6 pt-4 border-t">
            <p class="text-xs text-gray-400">Thank you for your purchase!</p>
            <button onclick="window.print()" class="mt-3 px-4 py-2 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700">Print Receipt</button>
        </div>
    </div>
</div>
@endsection
