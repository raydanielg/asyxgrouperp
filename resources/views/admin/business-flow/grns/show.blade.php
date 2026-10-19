@extends('layouts.admin')
@section('title', 'GRN Details - ' . config('app.name'))
@section('page_title', 'GRN: ' . $grn->grn_number)
@section('content')
@php $gd=['received'=>'#10b981','discrepant'=>'#f59e0b','rejected'=>'#ef4444'][$grn->status] ?? '#9ca3af'; @endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.grns.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to GRNs</a>
    <form method="POST" action="{{ route('admin.grns.destroy', $grn) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete GRN?', text:'{{ $grn->grn_number }} and its items will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
        <button class="px-3 py-2 text-xs font-medium text-gray-500 hover:text-red-600 border border-gray-200 rounded hover:border-red-200">Delete GRN</button>
    </form>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 p-5 mb-5 flex items-center justify-between flex-wrap gap-4">
    <div>
        <div class="flex items-center gap-3">
            <h3 class="font-bold text-gray-900 font-mono">{{ $grn->grn_number }}</h3>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $gd }}"></span>{{ ucfirst($grn->status) }}</span>
        </div>
        <p class="text-xs text-gray-400 mt-1">{{ $grn->supplier?->name ?? '—' }}@if($grn->lpo) · LPO {{ $grn->lpo->lpo_number }}@endif</p>
    </div>
    <div class="text-right">
        <p class="text-lg font-bold text-gray-900">{{ $grn->items->count() }} <span class="text-xs font-normal text-gray-400">items received</span></p>
        <p class="text-[11px] text-gray-400">{{ $grn->received_date->format('d M Y') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Details --}}
    <div class="space-y-5">
        <div class="bg-white rounded-md border border-gray-200">
            <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Details</h4></div>
            <dl class="p-5 space-y-2.5">
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">GRN Number</dt><dd class="text-xs font-mono text-gray-800 text-right">{{ $grn->grn_number }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">LPO</dt><dd class="text-xs text-gray-800 text-right">@if($grn->lpo)<a href="{{ route('admin.lpos.show', $grn->lpo) }}" class="font-mono hover:underline">{{ $grn->lpo->lpo_number }}</a>@else—@endif</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Supplier</dt><dd class="text-xs text-gray-800 text-right max-w-[160px] truncate">{{ $grn->supplier?->name ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Received Date</dt><dd class="text-xs text-gray-800 text-right">{{ $grn->received_date->format('d M Y') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">DN Ref</dt><dd class="text-xs font-mono text-gray-800 text-right">{{ $grn->delivery_note_number ?? '—' }}</dd></div>
            </dl>
        </div>

        @if($grn->notes)
        <div class="bg-white rounded-md border border-gray-200">
            <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Notes</h4></div>
            <p class="p-5 text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $grn->notes }}</p>
        </div>
        @endif

        {{-- Summary --}}
        @php $totDisc = $grn->items->sum('quantity_discrepant'); @endphp
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-400">Total expected</span>
                <span class="text-xs font-medium text-gray-800">{{ number_format($grn->items->sum('quantity_expected'), 2) + 0 }}</span>
            </div>
            <div class="flex justify-between items-center mt-2">
                <span class="text-xs text-gray-400">Total received</span>
                <span class="text-xs font-medium text-gray-800">{{ number_format($grn->items->sum('quantity_received'), 2) + 0 }}</span>
            </div>
            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-400">Discrepancies</span>
                <span class="text-xs font-bold {{ $totDisc > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $totDisc > 0 ? $totDisc + 0 : 'None' }}</span>
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Received Items</h4>
                <span class="text-[11px] text-gray-400">{{ $grn->items->count() }} line(s)</span>
            </div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="text-left border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Expected</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Received</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Discrepancy</th>
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Remarks</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($grn->items as $item)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $item->description }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500 text-right">{{ number_format($item->quantity_expected, 2) + 0 }} {{ $item->unit ?? '' }}</td>
                    <td class="px-4 py-3 text-xs font-medium text-gray-900 text-right">{{ number_format($item->quantity_received, 2) + 0 }} {{ $item->unit ?? '' }}</td>
                    <td class="px-4 py-3 text-xs text-right">
                        @if($item->quantity_discrepant > 0)
                        <span class="inline-flex items-center gap-1.5 text-red-600"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>{{ number_format($item->quantity_discrepant, 2) + 0 }}</span>
                        @else
                        <span class="inline-flex items-center gap-1.5 text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>OK</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $item->remarks ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No items recorded</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
