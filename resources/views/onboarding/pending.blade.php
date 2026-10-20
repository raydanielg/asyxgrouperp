@extends('layouts.auth')

@section('title', 'Request Pending - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md text-center" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white/95 backdrop-blur-xl rounded-md shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] border border-white/60 px-8 py-10">

        <div class="w-14 h-14 mx-auto rounded-full bg-amber-50 border border-amber-200 flex items-center justify-center mb-5">
            <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>

        <h2 class="text-base font-bold text-gray-900">Request Submitted</h2>
        <p class="text-xs text-gray-400 mt-1.5 mb-6 leading-relaxed">Your company setup is under review. We'll activate your workspace once approved.</p>

        <div class="text-left divide-y divide-gray-100 border border-gray-100 rounded-md mb-6">
            <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Company</span><span class="text-[11px] font-medium text-gray-900">{{ $company->name }}</span></div>
            <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Plan</span><span class="text-[11px] font-medium text-gray-900">{{ $company->subscription_plan ?? $order?->plan_name ?? '—' }}</span></div>
            <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Billing</span><span class="text-[11px] font-medium text-gray-900 capitalize">{{ $company->billing_cycle ?? '—' }}</span></div>
            @if($order)
            <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Quote Ref</span><span class="text-[11px] font-mono text-gray-900">{{ $order->order_id }}</span></div>
            <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Amount</span><span class="text-[11px] font-mono font-bold text-gray-900">TZS {{ number_format($order->price) }}</span></div>
            <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Status</span><span class="inline-flex items-center gap-1.5 text-[11px] text-gray-700 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $order->payment_status === 'succeeded' ? '#10b981' : ($order->payment_status === 'failed' ? '#ef4444' : '#f59e0b') }}"></span>{{ $order->payment_status }}</span></div>
            @endif
        </div>

        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit" class="text-xs font-medium text-gray-500 hover:text-gray-800">Sign out →</button>
        </form>
    </div>
    <p class="mt-6 text-[11px] text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
</div>
@endsection
