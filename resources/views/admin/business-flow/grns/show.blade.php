@extends('layouts.admin')
@section('title', 'GRN Details - ' . config('app.name'))
@section('page_title', 'GRN: ' . $grn->grn_number)
@section('content')
<div class="mb-4"><a href="{{ route('admin.grns.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to GRNs</a></div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">GRN Information</h3>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-gray-400">GRN Number</span><span class="font-mono text-gray-700">{{ $grn->grn_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">LPO</span><span class="text-gray-700">{{ $grn->lpo?->lpo_number ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Supplier</span><span class="text-gray-700">{{ $grn->supplier?->name ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Received Date</span><span class="text-gray-700">{{ $grn->received_date->format('d M Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">DN Ref</span><span class="text-gray-700">{{ $grn->delivery_note_number ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] @if($grn->status==='received')bg-emerald-50 text-emerald-700@elseif($grn->status==='discrepant')bg-amber-50 text-amber-700@else bg-red-50 text-red-700@endif">{{ ucfirst($grn->status) }}</span></div>
        </div>
        @if($grn->notes)<div class="mt-3 pt-3 border-t"><p class="text-[10px] text-gray-400 uppercase mb-1">Notes</p><p class="text-xs text-gray-600">{{ $grn->notes }}</p></div>@endif
    </div>
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Received Items</h3>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">Description</th><th class="py-2">Expected</th><th class="py-2">Received</th><th class="py-2">Discrepancy</th><th class="py-2">Remarks</th></tr></thead>
                <tbody>@foreach($grn->items as $item)<tr class="border-t border-gray-100">
                    <td class="py-2 text-gray-700">{{ $item->description }}</td>
                    <td class="py-2 text-gray-500">{{ $item->quantity_expected }} {{ $item->unit ?? '' }}</td>
                    <td class="py-2 text-gray-700 font-medium">{{ $item->quantity_received }} {{ $item->unit ?? '' }}</td>
                    <td class="py-2 @if($item->quantity_discrepant>0)text-red-600 font-semibold@else text-emerald-600@endif">{{ $item->quantity_discrepant > 0 ? $item->quantity_discrepant : '✓' }}</td>
                    <td class="py-2 text-gray-400">{{ $item->remarks ?? '—' }}</td>
                </tr>@endforeach</tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
