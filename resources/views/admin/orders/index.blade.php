@extends('layouts.admin')

@section('title', 'Orders - ' . config('app.name'))
@section('page_title', 'Orders')

@section('content')

@php
    $totalRevenue = $orders->where('payment_status', 'succeeded')->sum('price');
    $sc = ['succeeded' => '#10b981', 'pending' => '#f59e0b', 'failed' => '#ef4444'];
@endphp

<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $orders->total() }} orders</span>
        <span class="text-xs text-gray-500">Succeeded revenue: <span class="font-mono font-bold text-gray-900">TZS {{ number_format($totalRevenue) }}</span></span>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Order ID</th><th class="px-5 py-3 font-semibold">Customer</th><th class="px-5 py-3 font-semibold">Plan</th><th class="px-5 py-3 font-semibold text-right">Price</th><th class="px-5 py-3 font-semibold">Payment</th><th class="px-5 py-3 font-semibold text-right">Date</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($orders as $order)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono font-medium text-gray-900">{{ $order->order_id }}</td>
            <td class="px-5 py-3.5">
                <p class="text-xs text-gray-800">{{ $order->name ?? '—' }}</p>
                @if($order->email)<p class="text-[10px] text-gray-400 mt-0.5">{{ $order->email }}</p>@endif
            </td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $order->plan_name ?? $order->plan?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold text-gray-900">TZS {{ number_format($order->price) }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc[$order->payment_status] ?? '#9ca3af' }}"></span>{{ ucfirst($order->payment_status) }}</span></td>
            <td class="px-5 py-3.5 text-xs text-right text-gray-400">{{ $order->created_at->format('d M Y') }}</td>
            <td class="px-5 py-3.5">
                @if($order->payment_status === 'pending')
                <div class="flex items-center justify-end gap-4">
                    <form method="POST" action="{{ route('admin.orders.approve', $order) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Approve & activate this company?', color: '#059669'})">@csrf @method('PATCH')<button class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Approve</button></form>
                    <form method="POST" action="{{ route('admin.orders.reject', $order) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Reject this request?', color: '#dc2626'})">@csrf @method('PATCH')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Reject</button></form>
                </div>
                @else
                <div class="text-right text-xs text-gray-300">—</div>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No orders found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($orders->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
