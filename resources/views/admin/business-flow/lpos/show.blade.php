@extends('layouts.admin')
@section('title', 'LPO Details - ' . config('app.name'))
@section('page_title', 'LPO: ' . $lpo->lpo_number)
@section('content')
<div class="mb-4"><a href="{{ route('admin.lpos.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to LPOs</a></div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- LPO Info --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">LPO Information</h3>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-gray-400">LPO Number</span><span class="font-mono text-gray-700">{{ $lpo->lpo_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Project</span><span class="text-gray-700">{{ $lpo->project?->title ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Supplier</span><span class="text-gray-700">{{ $lpo->supplier?->name ?? $lpo->supplier_name ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">LPO Date</span><span class="text-gray-700">{{ $lpo->lpo_date->format('d M Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Expected Delivery</span><span class="text-gray-700">{{ $lpo->expected_delivery_date?->format('d M Y') ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Subtotal</span><span class="text-gray-700">TZS {{ number_format($lpo->subtotal) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Tax (18%)</span><span class="text-gray-700">TZS {{ number_format($lpo->tax_amount) }}</span></div>
            <div class="flex justify-between border-t pt-2"><span class="font-semibold text-gray-900">Total</span><span class="font-bold text-emerald-700">TZS {{ number_format($lpo->total) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status</span>@php $sc=['draft'=>'gray','sent'=>'sky','partially_received'=>'amber','received'=>'emerald','closed'=>'emerald','cancelled'=>'red']; $c=$sc[$lpo->status]??'gray'; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-{{ $c }}-50 text-{{ $c }}-700">{{ ucfirst(str_replace('_',' ',$lpo->status)) }}</span></div>
        </div>
        @if($lpo->terms)<div class="mt-3 pt-3 border-t"><p class="text-[10px] text-gray-400 uppercase mb-1">Terms</p><p class="text-xs text-gray-600">{{ $lpo->terms }}</p></div>@endif
        {{-- Status Update --}}
        <div class="mt-4">
            <form method="POST" action="{{ route('admin.lpos.status', $lpo) }}">@csrf @method('PATCH')
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs mb-2 outline-none">
                    @foreach(['draft'=>'Draft','sent'=>'Sent','partially_received'=>'Partially Received','received'=>'Received','closed'=>'Closed','cancelled'=>'Cancelled'] as $k=>$v)<option value="{{ $k }}" @selected($lpo->status===$k)>{{ $v }}</option>@endforeach
                </select>
                <button type="submit" class="w-full px-3 py-2 bg-sky-600 text-white text-xs font-medium rounded-lg hover:bg-sky-700">Update Status</button>
            </form>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="lg:col-span-2 space-y-4">
        {{-- LPO Items --}}
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">LPO Items</h3>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">Description</th><th class="py-2">Ordered</th><th class="py-2">Received</th><th class="py-2">Unit Price</th><th class="py-2">Line Total</th></tr></thead>
                <tbody>@foreach($lpo->items as $item)<tr class="border-t border-gray-100">
                    <td class="py-2 text-gray-700">{{ $item->description }}</td>
                    <td class="py-2 text-gray-500">{{ $item->quantity_ordered }} {{ $item->unit ?? '' }}</td>
                    <td class="py-2 @if($item->quantity_received>=$item->quantity_ordered)text-emerald-600@elseif($item->quantity_received>0)text-amber-600@else text-gray-400@endif">{{ $item->quantity_received }}</td>
                    <td class="py-2 text-gray-500">TZS {{ number_format($item->unit_price) }}</td>
                    <td class="py-2 font-semibold text-gray-900">TZS {{ number_format($item->line_total) }}</td>
                </tr>@endforeach</tbody>
            </table></div>
        </div>

        {{-- GRNs --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between border-b pb-3 mb-3"><h3 class="text-sm font-bold text-gray-900">Goods Received Notes (GRN)</h3><a href="{{ route('admin.grns.index') }}" class="text-[10px] text-emerald-600">Create GRN</a></div>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">GRN No.</th><th class="py-2">Date</th><th class="py-2">Status</th><th class="py-2">Actions</th></tr></thead>
                <tbody>@forelse($lpo->grns as $g)<tr class="border-t border-gray-100">
                    <td class="py-2 font-mono text-gray-700">{{ $g->grn_number }}</td>
                    <td class="py-2 text-gray-500">{{ $g->received_date->format('d M Y') }}</td>
                    <td class="py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] @if($g->status==='received')bg-emerald-50 text-emerald-700@else bg-amber-50 text-amber-700@endif">{{ ucfirst($g->status) }}</span></td>
                    <td class="py-2"><a href="{{ route('admin.grns.show', $g) }}" class="text-sky-600 text-xs">View</a></td>
                
        </tr>
        @empty
        <tr><td colspan="4" class="py-4 text-center text-gray-400">No GRNs yet</td></tr>
        @endforelse
        </tbody>
            </table></div>
        </div>

        {{-- Delivery Notes --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between border-b pb-3 mb-3"><h3 class="text-sm font-bold text-gray-900">Delivery Notes</h3><a href="{{ route('admin.delivery-notes.index') }}" class="text-[10px] text-emerald-600">Create DN</a></div>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">DN No.</th><th class="py-2">Date</th><th class="py-2">Delivered By</th><th class="py-2">Status</th></tr></thead>
                <tbody>@forelse($lpo->deliveryNotes as $d)<tr class="border-t border-gray-100">
                    <td class="py-2 font-mono text-gray-700">{{ $d->delivery_note_number }}</td>
                    <td class="py-2 text-gray-500">{{ $d->delivery_date->format('d M Y') }}</td>
                    <td class="py-2 text-gray-500">{{ $d->delivered_by ?? 'N/A' }}</td>
                    <td class="py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 text-emerald-700">{{ ucfirst($d->status) }}</span></td>
                
        </tr>
        @empty
        <tr><td colspan="4" class="py-4 text-center text-gray-400">No delivery notes yet</td></tr>
        @endforelse
        </tbody>
            </table></div>
        </div>

        {{-- Vendor Invoices --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between border-b pb-3 mb-3"><h3 class="text-sm font-bold text-gray-900">Vendor Invoices</h3><a href="{{ route('admin.vendor-invoices.index') }}" class="text-[10px] text-emerald-600">Create Invoice</a></div>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">Invoice No.</th><th class="py-2">Date</th><th class="py-2">Total</th><th class="py-2">Paid</th><th class="py-2">Balance</th><th class="py-2">Status</th><th class="py-2">Actions</th></tr></thead>
                <tbody>@forelse($lpo->vendorInvoices as $v)<tr class="border-t border-gray-100">
                    <td class="py-2 font-mono text-gray-700">{{ $v->vendor_invoice_number }}</td>
                    <td class="py-2 text-gray-500">{{ $v->invoice_date->format('d M Y') }}</td>
                    <td class="py-2 font-semibold text-gray-900">TZS {{ number_format($v->total) }}</td>
                    <td class="py-2 text-emerald-600">TZS {{ number_format($v->amount_paid) }}</td>
                    <td class="py-2 text-red-600">TZS {{ number_format($v->balance) }}</td>
                    <td class="py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] @if($v->status==='paid')bg-emerald-50 text-emerald-700@elseif($v->status==='partially_paid')bg-amber-50 text-amber-700@else bg-red-50 text-red-700@endif">{{ ucfirst(str_replace('_',' ',$v->status)) }}</span></td>
                    <td class="py-2"><a href="{{ route('admin.vendor-invoices.show', $v) }}" class="text-sky-600 text-xs">View</a></td>
                
        </tr>
        @empty
        <tr><td colspan="7" class="py-4 text-center text-gray-400">No vendor invoices yet</td></tr>
        @endforelse
        </tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
