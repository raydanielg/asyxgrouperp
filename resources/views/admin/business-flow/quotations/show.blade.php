@extends('layouts.admin')
@section('title', 'Quotation - ' . config('app.name'))
@section('page_title', 'Quotation: ' . $quotation->quotation_number)
@section('content')
@php
    $qd = ['draft'=>'#9ca3af','sent'=>'#3b82f6','accepted'=>'#10b981','rejected'=>'#ef4444','expired'=>'#f59e0b'][$quotation->status] ?? '#9ca3af';
    $pipeline = ['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Accepted'];
    $isTerminal = in_array($quotation->status, ['rejected', 'expired']);
    $pipelineKeys = array_keys($pipeline);
    $currentIdx = array_search($quotation->status, $pipelineKeys);
    $currentIdx = $currentIdx === false ? -1 : $currentIdx;
@endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.quotations.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Quotations</a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Download PDF</a>
        <form method="POST" action="{{ route('admin.quotations.destroy', $quotation) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Quotation?', text:'{{ $quotation->quotation_number }} will be permanently removed.', color:'#dc2626'})">@csrf @method('DELETE')
            <button class="px-3 py-1.5 border border-gray-200 text-red-600 text-xs font-medium rounded hover:bg-red-50 hover:border-red-200">Delete</button>
        </form>
    </div>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm font-bold text-gray-900">{{ $quotation->quotation_number }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $qd }}"></span>{{ ucfirst($quotation->status) }}</span>
            </div>
            <h1 class="text-base font-bold text-gray-900 mt-1.5">{{ $quotation->client_name }}</h1>
            @if($quotation->client_email)<p class="text-[11px] text-gray-400 mt-1">{{ $quotation->client_email }}</p>@endif
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total</p>
            <p class="text-xl font-bold text-gray-900 mt-0.5">TZS {{ number_format($quotation->total) }}</p>
        </div>
    </div>
    {{-- Pipeline --}}
    @if(!$isTerminal)
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center">
            @foreach($pipeline as $key => $label)
            @php $idx = array_search($key, $pipelineKeys); $reached = $idx <= $currentIdx; $isCurrent = $idx === $currentIdx; @endphp
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
            <div class="ml-auto">
                <form method="POST" action="{{ route('admin.quotations.status', $quotation) }}">@csrf @method('PATCH')
                    <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400 bg-white">
                        @foreach(['draft'=>'Draft','sent'=>'Sent','accepted'=>'Accepted','rejected'=>'Rejected','expired'=>'Expired'] as $k=>$v)
                        <option value="{{ $k }}" @selected($quotation->status===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
    @else
    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span style="width:8px;height:8px;border-radius:99px;background:{{ $qd }}"></span>
            <p class="text-xs font-semibold text-gray-800">Quotation {{ $quotation->status }}</p>
        </div>
        <form method="POST" action="{{ route('admin.quotations.status', $quotation) }}">@csrf @method('PATCH')
            <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400 bg-white">
                @foreach(['draft'=>'Draft','sent'=>'Sent','accepted'=>'Accepted','rejected'=>'Rejected','expired'=>'Expired'] as $k=>$v)
                <option value="{{ $k }}" @selected($quotation->status===$k)>{{ $v }}</option>
                @endforeach
            </select>
        </form>
    </div>
    @endif
    <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-xs">
        <div><p class="text-gray-400">Lead</p><p class="text-gray-800 mt-0.5">{{ $quotation->lead?->full_name ?? '—' }}</p></div>
        <div><p class="text-gray-400">Quotation Date</p><p class="text-gray-800 mt-0.5">{{ $quotation->quotation_date->format('d M Y') }}</p></div>
        <div><p class="text-gray-400">Valid Until</p><p class="mt-0.5 {{ $quotation->valid_until && $quotation->valid_until->isPast() && $quotation->status === 'sent' ? 'text-red-600 font-medium' : 'text-gray-800' }}">{{ $quotation->valid_until?->format('d M Y') ?? '—' }}</p></div>
        <div><p class="text-gray-400">Items</p><p class="text-gray-800 mt-0.5">{{ $quotation->items->count() }} line{{ $quotation->items->count() === 1 ? '' : 's' }}</p></div>
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
                <th class="px-5 py-2.5 font-semibold text-right">Discount</th>
                <th class="px-5 py-2.5 font-semibold text-right">Tax</th>
                <th class="px-5 py-2.5 font-semibold text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @foreach($quotation->items as $i => $item)
        <tr>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $item->description }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono text-gray-500">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }} {{ $item->unit ?? '' }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono text-gray-500">{{ number_format($item->unit_price) }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono {{ $item->discount_amount > 0 ? 'text-red-500' : 'text-gray-300' }}">{{ $item->discount_amount > 0 ? number_format($item->discount_amount) : '—' }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono {{ $item->tax_percentage > 0 ? 'text-gray-500' : 'text-gray-300' }}">{{ $item->tax_percentage > 0 ? rtrim(rtrim(number_format($item->tax_percentage, 2), '0'), '.') . '%' : '—' }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono font-bold text-gray-900">{{ number_format($item->line_total) }}</td>
        </tr>
        @endforeach
        </tbody>
    </table></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    {{-- Summary --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Summary</h3></div>
        <div class="divide-y divide-gray-100">
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Subtotal</span><span class="text-xs font-mono font-medium text-gray-900">TZS {{ number_format($quotation->subtotal) }}</span></div>
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Discount</span><span class="text-xs font-mono {{ $quotation->discount_amount > 0 ? 'text-red-500' : 'text-gray-400' }}">{{ $quotation->discount_amount > 0 ? '-TZS ' . number_format($quotation->discount_amount) : '—' }}</span></div>
            <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-500">Tax</span><span class="text-xs font-mono font-medium text-gray-900">{{ $quotation->tax_amount > 0 ? '+TZS ' . number_format($quotation->tax_amount) : '—' }}</span></div>
            <div class="flex justify-between px-5 py-3.5 bg-gray-50/60"><span class="text-xs font-bold text-gray-900">Total</span><span class="text-sm font-mono font-bold text-gray-900">TZS {{ number_format($quotation->total) }}</span></div>
        </div>
    </div>

    {{-- Terms --}}
    @if($quotation->terms)
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Terms</h3></div>
        <div class="px-5 py-4"><p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $quotation->terms }}</p></div>
    </div>
    @endif
</div>

{{-- PDF preview --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">PDF Preview</h3>
        <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank" class="text-xs font-medium text-gray-600 hover:text-gray-900">Open full size →</a>
    </div>
    <div class="bg-gray-100 p-4 md:p-8">
        <iframe src="{{ route('admin.quotations.pdf', $quotation) }}?inline=1" class="w-full bg-white rounded border border-gray-300 shadow-sm" style="height:900px;border:0"></iframe>
    </div>
</div>
@endsection
