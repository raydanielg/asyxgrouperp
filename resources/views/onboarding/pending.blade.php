@extends('layouts.auth')

@section('title', 'Request Submitted - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white/95 backdrop-blur-xl rounded-md shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] border border-white/60 overflow-hidden">

        {{-- Congrats header --}}
        <div class="px-8 pt-8 pb-5 text-center border-b border-gray-100">
            <div class="w-14 h-14 mx-auto rounded-full bg-emerald-50 border-2 border-emerald-400 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Congratulations!</h2>
            <p class="text-xs text-gray-400 mt-1 leading-relaxed">Your request has been submitted and is under review.<br>We'll activate your workspace once approved.</p>
        </div>

        {{-- Receipt-style summary --}}
        <div class="p-6" id="printArea">
            <div class="border border-gray-200 rounded-md divide-y divide-gray-100">
                <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Company</span><span class="text-[11px] font-medium text-gray-900">{{ $company->name }}</span></div>
                @if($company->email)<div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Email</span><span class="text-[11px] font-mono text-gray-700">{{ $company->email }}</span></div>@endif
                @if($company->phone)<div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Phone</span><span class="text-[11px] font-mono text-gray-700">{{ $company->phone }}</span></div>@endif
                @if($company->city || $company->country)<div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Location</span><span class="text-[11px] text-gray-700">{{ collect([$company->city, $company->country])->filter()->implode(', ') }}</span></div>@endif
                @if($company->tax_id || $company->vrn)<div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">TIN / VRN</span><span class="text-[11px] font-mono text-gray-700">{{ collect([$company->tax_id, $company->vrn])->filter()->implode(' · ') }}</span></div>@endif
                <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Plan</span><span class="text-[11px] font-medium text-gray-900">{{ $company->subscription_plan ?? $order?->plan_name ?? '—' }}</span></div>
                <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Billing</span><span class="text-[11px] font-medium text-gray-900 capitalize">{{ $company->billing_cycle ?? '—' }}</span></div>
                @if($order)
                <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Quote Ref</span><span class="text-[11px] font-mono text-gray-900">{{ $order->order_id }}</span></div>
                <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Amount</span><span class="text-[11px] font-mono font-bold text-gray-900">TZS {{ number_format($order->price) }}</span></div>
                <div class="px-4 py-2.5 flex justify-between"><span class="text-[11px] text-gray-400">Status</span><span class="inline-flex items-center gap-1.5 text-[11px] text-gray-700 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $order->payment_status === 'succeeded' ? '#10b981' : ($order->payment_status === 'failed' ? '#ef4444' : '#f59e0b') }}"></span>{{ $order->payment_status === 'succeeded' ? 'Approved' : ($order->payment_status === 'failed' ? 'Rejected' : 'Under review') }}</span></div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 pb-6 flex items-center justify-between">
            <button onclick="printReceipt()" class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print receipt
            </button>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="text-xs font-medium text-gray-500 hover:text-gray-800">Sign out →</button>
            </form>
        </div>
    </div>
    <p class="mt-6 text-center text-[11px] text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
</div>

<script>
function printReceipt() {
    const w = window.open('', '_blank', 'width=420,height=620');
    w.document.write(`<!DOCTYPE html><html><head><title>Request Summary</title><style>
        body{font-family:'Courier New',monospace;font-size:12px;margin:24px;color:#111}
        .head{text-align:center;border-bottom:1px dashed #999;padding-bottom:10px;margin-bottom:10px}
        h2{font-size:14px;margin:0}
        p{font-size:11px;color:#555;margin:4px 0 0}
        table{width:100%;border-collapse:collapse}
        td{padding:7px 4px;border-bottom:1px dashed #ccc;font-size:11px}
        td:first-child{color:#666}
        td:last-child{text-align:right;font-weight:bold}
        .foot{text-align:center;margin-top:14px;font-size:10px;color:#777;border-top:1px dashed #999;padding-top:10px}
    </style></head><body>
    <div class="head"><h2>{{ config('app.name', 'ASYX ERP') }}</h2><p>COMPANY ONBOARDING REQUEST</p></div>
    <table>
        <tr><td>Company</td><td>{{ $company->name }}</td></tr>
        <tr><td>Email</td><td>{{ $company->email }}</td></tr>
        <tr><td>Phone</td><td>{{ $company->phone ?? '—' }}</td></tr>
        <tr><td>Location</td><td>{{ collect([$company->city, $company->country])->filter()->implode(', ') ?: '—' }}</td></tr>
        <tr><td>TIN / VRN</td><td>{{ collect([$company->tax_id, $company->vrn])->filter()->implode(' · ') ?: '—' }}</td></tr>
        <tr><td>Plan</td><td>{{ $company->subscription_plan ?? '—' }}</td></tr>
        <tr><td>Billing</td><td>{{ ucfirst($company->billing_cycle ?? '') }}</td></tr>
        <tr><td>Quote Ref</td><td>{{ $order?->order_id }}</td></tr>
        <tr><td>Amount</td><td>TZS {{ number_format($order?->price ?? 0) }}</td></tr>
        <tr><td>Status</td><td>{{ strtoupper($order?->payment_status ?? 'PENDING') }}</td></tr>
        <tr><td>Submitted</td><td>{{ $order?->created_at?->format('d M Y H:i') ?? now()->format('d M Y H:i') }}</td></tr>
    </table>
    <div class="foot">Keep this reference for follow-up.<br>{{ config('app.name', 'ASYX ERP') }}</div>
    </body></html>`);
    w.document.close();
    w.focus();
    setTimeout(() => w.print(), 300);
}
</script>
@endsection
