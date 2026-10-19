@extends('layouts.admin')
@section('title', 'Deal ' . $deal->deal_number . ' - ' . config('app.name'))
@section('page_title', 'Deal Details')
@section('content')
@php $dc = ['open' => '#3b82f6', 'won' => '#10b981', 'lost' => '#ef4444', 'cancelled' => '#9ca3af'][$deal->status] ?? '#9ca3af'; @endphp

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.crm-deals.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Deals</a>
    <a href="{{ route('admin.crm-deals.pdf', $deal) }}" target="_blank" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">View PDF</a>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4 flex items-center justify-between flex-wrap gap-4">
    <div>
        <div class="flex items-center gap-2">
            <span class="font-mono text-sm font-bold text-gray-900">{{ $deal->deal_number }}</span>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $dc }}"></span>{{ ucfirst($deal->status) }}</span>
        </div>
        <p class="text-xs text-gray-400 mt-0.5">{{ $deal->title }}</p>
    </div>
    <div class="text-right">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Deal Value</p>
        <p class="text-xl font-bold text-gray-900">TZS {{ number_format($deal->value) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Details</h4></div>
        <dl class="p-5 space-y-2.5">
            <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Lead</dt><dd class="text-xs text-gray-700">{{ $deal->lead?->full_name ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Stage</dt><dd class="text-xs text-gray-700">{{ ucfirst(str_replace('_', ' ', $deal->stage)) }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Expected Close</dt><dd class="text-xs text-gray-700">{{ $deal->expected_close_date?->format('d M Y') ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Assigned To</dt><dd class="text-xs text-gray-700">{{ $deal->assignedTo?->name ?? 'Unassigned' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Created</dt><dd class="text-xs text-gray-700">{{ $deal->created_at?->format('d M Y') }}</dd></div>
        </dl>
    </div>
    @if($deal->notes)
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Notes</h4></div>
        <div class="p-5 text-xs text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $deal->notes }}</div>
    </div>
    @endif
</div>
@endsection
