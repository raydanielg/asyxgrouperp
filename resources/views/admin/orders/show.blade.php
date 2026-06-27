@extends('layouts.admin')
@section('title', 'Order Details - ' . config('app.name'))
@section('page_title', 'Order Details')
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-start justify-between mb-6">
            <div><p class="text-xs text-gray-400 mb-1">Order ID</p><h2 class="text-xl font-bold text-gray-900 font-mono">{{ $order->order_id }}</h2></div>
            @if($order->payment_status === 'succeeded')<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Succeeded</span>
            @elseif($order->payment_status === 'pending')<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
            @else<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">{{ ucfirst($order->payment_status) }}</span>@endif
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs text-gray-400 mb-1">Customer Name</p><p class="text-sm font-medium text-gray-900">{{ $order->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Email</p><p class="text-sm font-medium text-gray-900">{{ $order->email ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Plan</p><p class="text-sm font-medium text-gray-900">{{ $order->plan_name ?? $order->plan?->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Price</p><p class="text-sm font-medium text-gray-900">${{ number_format($order->price, 2) }} {{ $order->currency }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Payment Type</p><p class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Transaction ID</p><p class="text-sm font-mono text-gray-700">{{ $order->txn_id ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Date</p><p class="text-sm text-gray-700">{{ $order->created_at->format('d M Y H:i') }}</p></div>
        </div>
    </div>
</div>
@endsection
