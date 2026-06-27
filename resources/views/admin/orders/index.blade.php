@extends('layouts.admin')

@section('title', 'Orders - ' . config('app.name'))
@section('page_title', 'Orders')

@section('content')
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Order ID</th>
                <th class="px-5 py-3 font-medium">Customer</th>
                <th class="px-5 py-3 font-medium">Plan</th>
                <th class="px-5 py-3 font-medium">Price</th>
                <th class="px-5 py-3 font-medium">Payment</th>
                <th class="px-5 py-3 font-medium">Date</th>
            </tr></thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $order->order_id }}</td>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $order->name ?? $order->email ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $order->plan_name ?? $order->plan?->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-xs font-semibold text-gray-900">${{ number_format($order->price, 2) }}</td>
                    <td class="px-5 py-3">
                        @if($order->payment_status === 'succeeded')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Succeeded</span>
                        @elseif($order->payment_status === 'pending')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                        @elseif($order->payment_status === 'failed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">Failed</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-100">{{ ucfirst($order->payment_status) }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No orders found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $orders->links() }}</div>
</div>
@endsection
