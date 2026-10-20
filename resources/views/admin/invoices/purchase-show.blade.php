@extends('layouts.admin')

@section('title', 'Purchase Invoice - ' . config('app.name'))
@section('page_title', 'Invoice: ' . $purchaseInvoice->invoice_number)

@section('content')
@php
    $sc = ['draft'=>'#9ca3af','posted'=>'#3b82f6','partial'=>'#f59e0b','paid'=>'#10b981','overdue'=>'#ef4444'][$purchaseInvoice->status] ?? '#9ca3af';
@endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.purchase-invoices.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Purchase Invoices</a>
    <div class="flex items-center gap-2">
        @if($purchaseInvoice->status === 'draft')
        <a href="{{ route('admin.purchase-invoices.edit', $purchaseInvoice) }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Edit</a>
        <form method="POST" action="{{ route('admin.purchase-invoices.post', $purchaseInvoice) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Post invoice {{ $purchaseInvoice->invoice_number }}?'})">@csrf
            <button class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Post Invoice</button>
        </form>
        @endif
        <form method="POST" action="{{ route('admin.purchase-invoices.destroy', $purchaseInvoice) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete invoice {{ $purchaseInvoice->invoice_number }}?', color: '#dc2626'})">@csrf @method('DELETE')
            <button class="px-3 py-1.5 border border-gray-200 text-red-600 text-xs font-medium rounded hover:bg-red-50 hover:border-red-200">Delete</button>
        </form>
    </div>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm font-bold text-gray-900">{{ $purchaseInvoice->invoice_number }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc }}"></span>{{ ucfirst($purchaseInvoice->status) }}</span>
            </div>
            <h1 class="text-base font-bold text-gray-900 mt-1.5">{{ $purchaseInvoice->vendor?->name ?? '—' }}</h1>
            @if($purchaseInvoice->warehouse)<p class="text-[11px] text-gray-400 mt-1">To {{ $purchaseInvoice->warehouse->name }}</p>@endif
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $purchaseInvoice->balance_amount > 0 ? 'Balance Due' : 'Total' }}</p>
            <p class="text-xl font-bold {{ $purchaseInvoice->balance_amount > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-0.5">TZS {{ number_format($purchaseInvoice->balance_amount > 0 ? $purchaseInvoice->balance_amount : $purchaseInvoice->total_amount) }}</p>
            @if($purchaseInvoice->balance_amount > 0)
            <p class="text-[10px] text-gray-400 mt-0.5">of TZS {{ number_format($purchaseInvoice->total_amount) }}</p>
            @endif
        </div>
    </div>

    {{-- Payment progress --}}
    @if($purchaseInvoice->total_amount > 0)
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[11px] text-gray-400">Payment progress</span>
            <span class="text-[11px] font-mono font-medium text-gray-700">{{ number_format(min(100, $purchaseInvoice->paid_amount / $purchaseInvoice->total_amount * 100), 0) }}%</span>
        </div>
        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full {{ $purchaseInvoice->balance_amount > 0 ? 'bg-amber-500' : 'bg-emerald-500' }} transition-all" style="width:{{ min(100, $purchaseInvoice->paid_amount / $purchaseInvoice->total_amount * 100) }}%"></div>
        </div>
        <div class="flex items-center justify-between mt-1.5">
            <span class="text-[11px] text-emerald-600">Paid: TZS {{ number_format($purchaseInvoice->paid_amount) }}</span>
            <span class="text-[11px] {{ $purchaseInvoice->balance_amount > 0 ? 'text-amber-600' : 'text-gray-400' }}">Due: TZS {{ number_format($purchaseInvoice->balance_amount) }}</span>
        </div>
    </div>
    @endif

    {{-- Status stepper --}}
    @php
        $steps = ['draft' => 'Draft', 'posted' => 'Posted', 'partial' => 'Partial', 'paid' => 'Paid'];
        $stepKeys = array_keys($steps);
        $curIdx = array_search($purchaseInvoice->status, $stepKeys);
        $curIdx = $curIdx === false ? ($purchaseInvoice->status === 'overdue' ? 2 : -1) : $curIdx;
    @endphp
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center">
            @foreach($steps as $key => $label)
            @php $idx = array_search($key, $stepKeys); $reached = $idx <= $curIdx; $isCurrent = $idx === $curIdx; @endphp
            @if($idx > 0)
            <div class="flex-1 h-px {{ $reached ? 'bg-gray-800' : 'bg-gray-200' }}"></div>
            @endif
            <div class="flex items-center gap-2 px-1">
                <span class="w-5 h-5 rounded-full border flex items-center justify-center flex-shrink-0 {{ $reached ? 'bg-gray-900 border-gray-900' : 'border-gray-300 bg-white' }}">
                    @if($reached && !$isCurrent)
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @elseif($isCurrent)
                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                    @endif
                </span>
                <span class="text-xs {{ $isCurrent ? 'font-bold text-gray-900' : ($reached ? 'text-gray-600' : 'text-gray-400') }}">{{ $label }}</span>
            </div>
            @endforeach
            @if($purchaseInvoice->status === 'overdue')
            <div class="flex-1 h-px bg-gray-200"></div>
            <div class="flex items-center gap-2 px-1">
                <span class="w-5 h-5 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0"><span class="w-1.5 h-1.5 rounded-full bg-white"></span></span>
                <span class="text-xs font-bold text-red-600">Overdue</span>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-xs">
        <div><p class="text-gray-400">Vendor</p><p class="text-gray-800 mt-0.5">{{ $purchaseInvoice->vendor?->name ?? '—' }}</p></div>
        <div><p class="text-gray-400">Warehouse</p><p class="text-gray-800 mt-0.5">{{ $purchaseInvoice->warehouse?->name ?? '—' }}</p></div>
        <div><p class="text-gray-400">Invoice Date</p><p class="text-gray-800 mt-0.5">{{ $purchaseInvoice->invoice_date->format('d M Y') }}</p></div>
        <div><p class="text-gray-400">Due Date</p><p class="mt-0.5 {{ $purchaseInvoice->due_date->isPast() && $purchaseInvoice->balance_amount > 0 ? 'text-red-600 font-medium' : 'text-gray-800' }}">{{ $purchaseInvoice->due_date->format('d M Y') }}</p></div>
    </div>
</div>

{{-- Items --}}
@if($purchaseInvoice->items->count() > 0)
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Items</h3></div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100">
                <th class="px-5 py-2.5 font-semibold w-8">#</th>
                <th class="px-5 py-2.5 font-semibold">Product</th>
                <th class="px-5 py-2.5 font-semibold text-right">Qty</th>
                <th class="px-5 py-2.5 font-semibold text-right">Unit Price</th>
                <th class="px-5 py-2.5 font-semibold text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @foreach($purchaseInvoice->items as $i => $item)
        <tr>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $item->product_name }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono text-gray-500">{{ $item->quantity }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono text-gray-500">{{ number_format($item->unit_price) }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono font-bold text-gray-900">{{ number_format($item->total_amount) }}</td>
        </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- Summary --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Summary</h3></div>
        <div class="divide-y divide-gray-100">
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Subtotal</span><span class="text-xs font-mono font-medium text-gray-900">TZS {{ number_format($purchaseInvoice->subtotal) }}</span></div>
            @if($purchaseInvoice->discount_amount > 0)
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Discount</span><span class="text-xs font-mono text-red-500">-TZS {{ number_format($purchaseInvoice->discount_amount) }}</span></div>
            @endif
            @if($purchaseInvoice->tax_amount > 0)
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Tax</span><span class="text-xs font-mono font-medium text-gray-900">+TZS {{ number_format($purchaseInvoice->tax_amount) }}</span></div>
            @endif
            <div class="flex justify-between px-5 py-3.5 bg-gray-50/60"><span class="text-xs font-bold text-gray-900">Total</span><span class="text-sm font-mono font-bold text-gray-900">TZS {{ number_format($purchaseInvoice->total_amount) }}</span></div>
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Paid</span><span class="text-xs font-mono text-emerald-600">TZS {{ number_format($purchaseInvoice->paid_amount) }}</span></div>
            <div class="flex justify-between px-5 py-3.5 bg-gray-50/60"><span class="text-xs font-bold {{ $purchaseInvoice->balance_amount > 0 ? 'text-amber-600' : 'text-gray-400' }}">Balance</span><span class="text-sm font-mono font-bold {{ $purchaseInvoice->balance_amount > 0 ? 'text-amber-600' : 'text-gray-400' }}">TZS {{ number_format($purchaseInvoice->balance_amount) }}</span></div>
        </div>
    </div>

    {{-- Notes --}}
    @if($purchaseInvoice->notes)
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Notes</h3></div>
        <div class="px-5 py-4"><p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $purchaseInvoice->notes }}</p></div>
    </div>
    @endif
</div>
@endsection
