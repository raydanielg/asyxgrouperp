@extends('layouts.admin')
@section('title', 'Invoice ' . $salesInvoice->invoice_number)
@section('page_title', 'Invoice: ' . $salesInvoice->invoice_number)
@section('content')
@php
    $sc = ['draft'=>'#9ca3af','posted'=>'#3b82f6','partial'=>'#f59e0b','paid'=>'#10b981','overdue'=>'#ef4444'][$salesInvoice->status] ?? '#9ca3af';
@endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.sales-invoices.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Invoices</a>
    <div class="flex items-center gap-2 flex-wrap">
        @if($salesInvoice->status === 'draft')
        <form method="POST" action="{{ route('admin.sales-invoices.post', $salesInvoice) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Post invoice {{ $salesInvoice->invoice_number }}?'})">@csrf
            <button class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Post</button>
        </form>
        <a href="{{ route('admin.sales-invoices.edit', $salesInvoice) }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Edit</a>
        @endif
        <a href="{{ route('admin.sales-invoices.receipt', $salesInvoice) }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Receipt</a>
        <a href="{{ route('admin.sales-invoices.pdf', $salesInvoice) }}" target="_blank" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Download PDF</a>
        <form method="POST" action="{{ route('admin.sales-invoices.destroy', $salesInvoice) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete invoice {{ $salesInvoice->invoice_number }}?', color: '#dc2626'})">@csrf @method('DELETE')
            <button class="px-3 py-1.5 border border-gray-200 text-red-600 text-xs font-medium rounded hover:bg-red-50 hover:border-red-200">Delete</button>
        </form>
    </div>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm font-bold text-gray-900">{{ $salesInvoice->invoice_number }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc }}"></span>{{ ucfirst($salesInvoice->status) }}</span>
                <span class="text-xs text-gray-400 capitalize">{{ $salesInvoice->type }}</span>
            </div>
            <h1 class="text-base font-bold text-gray-900 mt-1.5">{{ $salesInvoice->customer?->name ?? '—' }}</h1>
            <p class="text-[11px] text-gray-400 mt-1">{{ $salesInvoice->customer?->email }}{{ $salesInvoice->customer?->phone ? ' · ' . $salesInvoice->customer->phone : '' }}</p>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $salesInvoice->balance_amount > 0 ? 'Balance Due' : 'Total' }}</p>
            <p class="text-xl font-bold {{ $salesInvoice->balance_amount > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-0.5">TZS {{ number_format($salesInvoice->balance_amount > 0 ? $salesInvoice->balance_amount : $salesInvoice->total_amount) }}</p>
            @if($salesInvoice->balance_amount > 0)
            <p class="text-[10px] text-gray-400 mt-0.5">of TZS {{ number_format($salesInvoice->total_amount) }}</p>
            @endif
        </div>
    </div>

    {{-- Payment progress --}}
    @if($salesInvoice->total_amount > 0)
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[11px] text-gray-400">Payment progress</span>
            <span class="text-[11px] font-mono font-medium text-gray-700">{{ number_format(min(100, $salesInvoice->paid_amount / $salesInvoice->total_amount * 100), 0) }}%</span>
        </div>
        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full {{ $salesInvoice->balance_amount > 0 ? 'bg-amber-500' : 'bg-emerald-500' }} transition-all" style="width:{{ min(100, $salesInvoice->paid_amount / $salesInvoice->total_amount * 100) }}%"></div>
        </div>
        <div class="flex items-center justify-between mt-1.5">
            <span class="text-[11px] text-emerald-600">Paid: TZS {{ number_format($salesInvoice->paid_amount) }}</span>
            <span class="text-[11px] {{ $salesInvoice->balance_amount > 0 ? 'text-amber-600' : 'text-gray-400' }}">Due: TZS {{ number_format($salesInvoice->balance_amount) }}</span>
        </div>
    </div>
    @endif

    {{-- Status stepper --}}
    @php
        $steps = ['draft' => 'Draft', 'posted' => 'Posted', 'partial' => 'Partial', 'paid' => 'Paid'];
        $stepKeys = array_keys($steps);
        $curIdx = array_search($salesInvoice->status, $stepKeys);
        $curIdx = $curIdx === false ? ($salesInvoice->status === 'overdue' ? 2 : -1) : $curIdx;
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
            @if($salesInvoice->status === 'overdue')
            <div class="flex-1 h-px bg-gray-200"></div>
            <div class="flex items-center gap-2 px-1">
                <span class="w-5 h-5 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0"><span class="w-1.5 h-1.5 rounded-full bg-white"></span></span>
                <span class="text-xs font-bold text-red-600">Overdue</span>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-xs">
        <div><p class="text-gray-400">Invoice Date</p><p class="text-gray-800 mt-0.5">{{ $salesInvoice->invoice_date->format('d M Y') }}</p></div>
        <div><p class="text-gray-400">Due Date</p><p class="mt-0.5 {{ $salesInvoice->due_date->isPast() && $salesInvoice->balance_amount > 0 ? 'text-red-600 font-medium' : 'text-gray-800' }}">{{ $salesInvoice->due_date->format('d M Y') }}</p></div>
        @if($salesInvoice->warehouse)
        <div><p class="text-gray-400">Warehouse</p><p class="text-gray-800 mt-0.5">{{ $salesInvoice->warehouse->name }}</p></div>
        @endif
        <div><p class="text-gray-400">Items</p><p class="text-gray-800 mt-0.5">{{ $salesInvoice->items->count() }} line{{ $salesInvoice->items->count() === 1 ? '' : 's' }}</p></div>
        <div><p class="text-gray-400">Paid</p><p class="mt-0.5 text-emerald-600 font-medium">TZS {{ number_format($salesInvoice->paid_amount) }}</p></div>
    </div>
</div>

{{-- Items --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Items</h3></div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100">
                <th class="px-5 py-2.5 font-semibold w-8">#</th>
                <th class="px-5 py-2.5 font-semibold">Description</th>
                <th class="px-5 py-2.5 font-semibold text-right">Qty</th>
                <th class="px-5 py-2.5 font-semibold text-right">Unit Price</th>
                <th class="px-5 py-2.5 font-semibold text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($salesInvoice->items as $i => $item)
        <tr>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $item->product_name }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono text-gray-500">{{ $item->quantity }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono text-gray-500">{{ number_format($item->unit_price, 2) }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono font-bold text-gray-900">{{ number_format($item->total_amount, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No items</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    {{-- Summary --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Summary</h3></div>
        <div class="divide-y divide-gray-100">
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Subtotal</span><span class="text-xs font-mono font-medium text-gray-900">TZS {{ number_format($salesInvoice->subtotal, 2) }}</span></div>
            @if($salesInvoice->discount_amount > 0)
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Discount</span><span class="text-xs font-mono text-red-500">-TZS {{ number_format($salesInvoice->discount_amount, 2) }}</span></div>
            @endif
            @if($salesInvoice->tax_amount > 0)
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">VAT (18%)</span><span class="text-xs font-mono font-medium text-gray-900">+TZS {{ number_format($salesInvoice->tax_amount, 2) }}</span></div>
            @endif
            <div class="flex justify-between px-5 py-3.5 bg-gray-50/60"><span class="text-xs font-bold text-gray-900">Total</span><span class="text-sm font-mono font-bold text-gray-900">TZS {{ number_format($salesInvoice->total_amount, 2) }}</span></div>
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Paid</span><span class="text-xs font-mono text-emerald-600">TZS {{ number_format($salesInvoice->paid_amount, 2) }}</span></div>
            <div class="flex justify-between px-5 py-3.5 bg-gray-50/60"><span class="text-xs font-bold {{ $salesInvoice->balance_amount > 0 ? 'text-amber-600' : 'text-gray-400' }}">Balance</span><span class="text-sm font-mono font-bold {{ $salesInvoice->balance_amount > 0 ? 'text-amber-600' : 'text-gray-400' }}">TZS {{ number_format($salesInvoice->balance_amount, 2) }}</span></div>
        </div>
    </div>

    <div class="space-y-4">
        {{-- Payment To --}}
        @if($salesInvoice->bankAccounts->count())
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Payment To</h3></div>
            <div class="divide-y divide-gray-100">
                @foreach($salesInvoice->bankAccounts as $ba)
                <div class="px-5 py-3">
                    <p class="text-xs font-medium text-gray-900">{{ $ba->account_name }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $ba->bank_name }} · {{ $ba->account_number }}{{ $ba->branch ? ' · ' . $ba->branch : '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Terms --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Terms &amp; Conditions</h3></div>
            <div class="px-5 py-4"><p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $salesInvoice->terms_and_conditions ?? "1. Prices are quoted in TZS\n2. Prices are subject to change without prior notice\n3. Payment terms must be strictly observed\n4. Goods remain property of " . config('app.name') . " until fully paid" }}</p></div>
        </div>

        @if($salesInvoice->notes)
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Notes</h3></div>
            <div class="px-5 py-4"><p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $salesInvoice->notes }}</p></div>
        </div>
        @endif
    </div>
</div>

{{-- PDF preview --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Invoice PDF</h3>
        <a href="{{ route('admin.sales-invoices.pdf', $salesInvoice) }}" target="_blank" class="text-xs font-medium text-gray-600 hover:text-gray-900">Open full size →</a>
    </div>
    <div class="bg-gray-100 p-4 md:p-8">
        <iframe src="{{ route('admin.sales-invoices.pdf', $salesInvoice) }}" class="w-full bg-white rounded border border-gray-300 shadow-sm" style="height:900px;border:0"></iframe>
    </div>
</div>
@endsection
