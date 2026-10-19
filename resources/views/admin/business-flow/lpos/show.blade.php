@extends('layouts.admin')
@section('title', 'LPO Details - ' . config('app.name'))
@section('page_title', 'LPO: ' . $lpo->lpo_number)
@section('content')
@php $ld=['draft'=>'#9ca3af','sent'=>'#3b82f6','partially_received'=>'#f59e0b','received'=>'#10b981','closed'=>'#10b981','cancelled'=>'#ef4444'][$lpo->status] ?? '#9ca3af'; @endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.lpos.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to LPOs</a>
    <form method="POST" action="{{ route('admin.lpos.status', $lpo) }}" class="flex items-center gap-2">@csrf @method('PATCH')
        <select name="status" class="px-3 py-2 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
            @foreach(['draft'=>'Draft','sent'=>'Sent','partially_received'=>'Partially Received','received'=>'Received','closed'=>'Closed','cancelled'=>'Cancelled'] as $k=>$v)
            <option value="{{ $k }}" @selected($lpo->status===$k)>{{ $v }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Update</button>
    </form>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 p-5 mb-5 flex items-center justify-between flex-wrap gap-4">
    <div>
        <div class="flex items-center gap-3">
            <h3 class="font-bold text-gray-900 font-mono">{{ $lpo->lpo_number }}</h3>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $ld }}"></span>{{ ucfirst(str_replace('_',' ',$lpo->status)) }}</span>
        </div>
        <p class="text-xs text-gray-400 mt-1">{{ $lpo->supplier?->name ?? $lpo->supplier_name ?? '—' }}@if($lpo->project) · {{ $lpo->project->title }}@endif</p>
    </div>
    <div class="text-right">
        <p class="text-lg font-bold text-gray-900">{{ number_format($lpo->total) }} <span class="text-xs font-normal text-gray-400">TZS</span></p>
        <p class="text-[11px] text-gray-400">incl. VAT 18%</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Info --}}
    <div class="space-y-5">
        <div class="bg-white rounded-md border border-gray-200">
            <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Details</h4></div>
            <dl class="p-5 space-y-2.5">
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">LPO Date</dt><dd class="text-xs text-gray-800 text-right">{{ $lpo->lpo_date->format('d M Y') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Expected Delivery</dt><dd class="text-xs text-gray-800 text-right">{{ $lpo->expected_delivery_date?->format('d M Y') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Project</dt><dd class="text-xs text-gray-800 text-right max-w-[160px] truncate">{{ $lpo->project?->title ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Supplier</dt><dd class="text-xs text-gray-800 text-right max-w-[160px] truncate">{{ $lpo->supplier?->name ?? $lpo->supplier_name ?? '—' }}</dd></div>
            </dl>
            <div class="px-5 py-3.5 border-t border-gray-100 space-y-2.5">
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Subtotal</dt><dd class="text-xs text-gray-800 text-right">{{ number_format($lpo->subtotal) }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">VAT (18%)</dt><dd class="text-xs text-gray-800 text-right">+{{ number_format($lpo->tax_amount) }}</dd></div>
                <div class="flex justify-between gap-4 pt-2 border-t border-gray-100"><dt class="text-xs font-bold text-gray-900">Total</dt><dd class="text-sm font-bold text-gray-900 text-right">{{ number_format($lpo->total) }} <span class="text-gray-400 text-xs font-normal">TZS</span></dd></div>
            </div>
        </div>

        @if($lpo->terms || $lpo->notes)
        <div class="bg-white rounded-md border border-gray-200">
            <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Terms &amp; Notes</h4></div>
            <div class="p-5 space-y-3">
                @if($lpo->terms)<div><p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Terms</p><p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $lpo->terms }}</p></div>@endif
                @if($lpo->notes)<div><p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Notes</p><p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $lpo->notes }}</p></div>@endif
            </div>
        </div>
        @endif
    </div>

    {{-- Right column --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Items --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Items</h4>
                <span class="text-[11px] text-gray-400">{{ $lpo->items->count() }} line(s)</span>
            </div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="text-left border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Ordered</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Received</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Unit Price</th>
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Line Total</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                @foreach($lpo->items as $item)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $item->description }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500 text-right">{{ rtrim(rtrim(number_format($item->quantity_ordered, 2), '0'), '.') }} {{ $item->unit ?? '' }}</td>
                    <td class="px-4 py-3 text-xs text-right">
                        <span class="inline-flex items-center gap-1.5 {{ $item->quantity_received >= $item->quantity_ordered ? 'text-emerald-600' : ($item->quantity_received > 0 ? 'text-amber-600' : 'text-gray-400') }}">
                            <span style="width:5px;height:5px;border-radius:99px;background:currentColor"></span>{{ rtrim(rtrim(number_format($item->quantity_received, 2), '0'), '.') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 text-right">{{ number_format($item->unit_price) }}</td>
                    <td class="px-5 py-3 text-xs font-medium text-gray-900 text-right">{{ number_format($item->line_total) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table></div>
        </div>

        {{-- GRNs --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Goods Received Notes</h4>
                <a href="{{ route('admin.grns.index') }}" class="text-[11px] font-medium text-gray-500 hover:text-gray-900">Create GRN →</a>
            </div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="text-left border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">GRN No.</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right"></th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($lpo->grns as $g)
                <tr>
                    <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $g->grn_number }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $g->received_date->format('d M Y') }}</td>
                    <td class="px-4 py-3"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span class="w-1.5 h-1.5 rounded-full {{ $g->status==='received' ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>{{ ucfirst($g->status) }}</span></td>
                    <td class="px-5 py-3 text-right"><a href="{{ route('admin.grns.show', $g) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a></td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400 text-xs">No GRNs yet</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>

        {{-- Delivery Notes --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Delivery Notes</h4>
                <a href="{{ route('admin.delivery-notes.index') }}" class="text-[11px] font-medium text-gray-500 hover:text-gray-900">Create DN →</a>
            </div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="text-left border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">DN No.</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Delivered By</th>
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($lpo->deliveryNotes as $d)
                <tr>
                    <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $d->delivery_note_number }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $d->delivery_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $d->delivered_by ?? '—' }}</td>
                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ ucfirst($d->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400 text-xs">No delivery notes yet</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>

        {{-- Vendor Invoices --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Vendor Invoices</h4>
                <a href="{{ route('admin.vendor-invoices.index') }}" class="text-[11px] font-medium text-gray-500 hover:text-gray-900">Create Invoice →</a>
            </div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="text-left border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Invoice No.</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Total</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Paid</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Balance</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right"></th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($lpo->vendorInvoices as $v)
                <tr>
                    <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $v->vendor_invoice_number }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $v->invoice_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-xs text-right font-medium text-gray-900">{{ number_format($v->total) }}</td>
                    <td class="px-4 py-3 text-xs text-right text-emerald-600">{{ number_format($v->amount_paid) }}</td>
                    <td class="px-4 py-3 text-xs text-right {{ $v->balance > 0 ? 'text-red-500' : 'text-gray-400' }}">{{ number_format($v->balance) }}</td>
                    <td class="px-4 py-3">
                        @php $vd=['paid'=>'#10b981','partially_paid'=>'#f59e0b','unpaid'=>'#ef4444'][$v->status] ?? '#9ca3af'; @endphp
                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $vd }}"></span>{{ ucfirst(str_replace('_',' ',$v->status)) }}</span>
                    </td>
                    <td class="px-5 py-3 text-right"><a href="{{ route('admin.vendor-invoices.show', $v) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-6 text-center text-gray-400 text-xs">No vendor invoices yet</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
