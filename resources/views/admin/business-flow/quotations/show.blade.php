@extends('layouts.admin')
@section('title', 'Quotation - ' . config('app.name'))
@section('page_title', 'Quotation: ' . $quotation->quotation_number)
@section('content')
@php $qd=['draft'=>'#9ca3af','sent'=>'#3b82f6','accepted'=>'#10b981','rejected'=>'#ef4444','expired'=>'#ef4444'][$quotation->status] ?? '#9ca3af'; @endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.quotations.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Quotations</a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.quotations.pdf', $quotation) }}" class="px-4 py-2 bg-slate-900 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors flex items-center gap-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download PDF
        </a>
        <form method="POST" action="{{ route('admin.quotations.destroy', $quotation) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Quotation?', text:'{{ $quotation->quotation_number }} will be permanently removed.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
            <button class="px-4 py-2 text-xs font-medium text-gray-400 hover:text-red-600 border border-gray-200 rounded hover:border-red-200 transition-colors">Delete</button>
        </form>
    </div>
</div>

{{-- Header --}}
<div class="bg-white rounded-md border border-gray-200 p-5 mb-5 flex items-center justify-between flex-wrap gap-4">
    <div>
        <div class="flex items-center gap-3">
            <h3 class="font-bold text-gray-900 font-mono">{{ $quotation->quotation_number }}</h3>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $qd }}"></span>{{ ucfirst($quotation->status) }}</span>
        </div>
        <p class="text-xs text-gray-400 mt-1">{{ $quotation->client_name }}@if($quotation->client_email) · {{ $quotation->client_email }}@endif</p>
    </div>
    <form method="POST" action="{{ route('admin.quotations.status', $quotation) }}" class="flex items-center gap-2">@csrf @method('PATCH')
        <select name="status" class="px-3 py-2 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
            @foreach(['draft'=>'Draft','sent'=>'Sent','accepted'=>'Accepted','rejected'=>'Rejected','expired'=>'Expired'] as $k=>$v)
            <option value="{{ $k }}" @selected($quotation->status===$k)>{{ $v }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Update</button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Details + Items --}}
    <div class="space-y-5">
        <div class="bg-white rounded-md border border-gray-200">
            <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Details</h4></div>
            <dl class="p-5 space-y-2.5">
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Lead</dt><dd class="text-xs text-gray-800 text-right">{{ $quotation->lead?->full_name ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Date</dt><dd class="text-xs text-gray-800 text-right">{{ $quotation->quotation_date->format('d M Y') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Valid Until</dt><dd class="text-xs text-gray-800 text-right">{{ $quotation->valid_until?->format('d M Y') ?? '—' }}</dd></div>
            </dl>
            <div class="px-5 py-3.5 border-t border-gray-100 space-y-2.5">
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Subtotal</dt><dd class="text-xs text-gray-800 text-right">{{ number_format($quotation->subtotal) }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Discount</dt><dd class="text-xs text-red-500 text-right">-{{ number_format($quotation->discount_amount) }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Tax</dt><dd class="text-xs text-gray-800 text-right">+{{ number_format($quotation->tax_amount) }}</dd></div>
                <div class="flex justify-between gap-4 pt-2 border-t border-gray-100"><dt class="text-xs font-bold text-gray-900">Total</dt><dd class="text-sm font-bold text-gray-900 text-right">{{ number_format($quotation->total) }} <span class="text-gray-400 text-xs font-normal">TZS</span></dd></div>
            </div>
        </div>

        @if($quotation->terms)
        <div class="bg-white rounded-md border border-gray-200">
            <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Terms</h4></div>
            <p class="p-5 text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $quotation->terms }}</p>
        </div>
        @endif
    </div>

    {{-- Items table --}}
    <div class="lg:col-span-2 bg-white rounded-md border border-gray-200 overflow-hidden self-start">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Items</h4>
            <span class="text-[11px] text-gray-400">{{ $quotation->items->count() }} line(s)</span>
        </div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left border-b border-gray-100 bg-gray-50/50">
                <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Qty</th>
                <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Unit Price</th>
                <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Disc.</th>
                <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Tax</th>
                <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Total</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
            @foreach($quotation->items as $item)
            <tr>
                <td class="px-5 py-3 text-xs text-gray-700">{{ $item->description }}</td>
                <td class="px-4 py-3 text-xs text-gray-500 text-right">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }} {{ $item->unit ?? '' }}</td>
                <td class="px-4 py-3 text-xs text-gray-500 text-right">{{ number_format($item->unit_price) }}</td>
                <td class="px-4 py-3 text-xs text-gray-500 text-right">{{ number_format($item->discount_amount) }}</td>
                <td class="px-4 py-3 text-xs text-gray-500 text-right">{{ rtrim(rtrim(number_format($item->tax_percentage, 2), '0'), '.') }}%</td>
                <td class="px-5 py-3 text-xs font-medium text-gray-900 text-right">{{ number_format($item->line_total) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table></div>
    </div>
</div>

{{-- PDF preview --}}
<div class="mt-5 bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">PDF Preview</h4>
        <a href="{{ route('admin.quotations.pdf', $quotation) }}" class="text-[11px] font-medium text-gray-500 hover:text-gray-800">Download ↓</a>
    </div>
    <iframe src="{{ route('admin.quotations.pdf', $quotation) }}?inline=1" class="w-full bg-gray-50" style="height:75vh;border:0"></iframe>
</div>
@endsection
