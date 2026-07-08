@extends('layouts.admin')
@section('title', 'Deal ' . $deal->deal_number . ' - ' . config('app.name'))
@section('page_title', 'Deal Details')
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.crm-deals.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Deals
    </a>
</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <div class="p-6 border-b bg-gradient-to-r from-sky-50 to-emerald-50 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $deal->deal_number }}</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $deal->title }}</p>
        </div>
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold
            @switch($deal->status) @case('won') bg-emerald-100 text-emerald-800 @break @case('lost') bg-red-100 text-red-800 @break @case('cancelled') bg-gray-100 text-gray-600 @break @default bg-sky-100 text-sky-800 @endswitch">
            @if($deal->status==='won')<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>@endif
            {{ ucfirst($deal->status) }}
        </span>
    </div>
    <div class="p-6 grid grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
        <div>
            <span class="text-[10px] text-gray-400 uppercase tracking-wider">Lead</span>
            <p class="font-medium text-gray-900 mt-1">{{ $deal->lead?->full_name ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-[10px] text-gray-400 uppercase tracking-wider">Value</span>
            <p class="font-bold text-emerald-700 mt-1 text-base">TZS {{ number_format($deal->value) }}</p>
        </div>
        <div>
            <span class="text-[10px] text-gray-400 uppercase tracking-wider">Stage</span>
            <p class="font-medium text-gray-900 mt-1">{{ ucfirst(str_replace('_', ' ', $deal->stage)) }}</p>
        </div>
        <div>
            <span class="text-[10px] text-gray-400 uppercase tracking-wider">Expected Close</span>
            <p class="font-medium text-gray-900 mt-1">{{ $deal->expected_close_date?->format('d M Y') ?? '—' }}</p>
        </div>
        <div>
            <span class="text-[10px] text-gray-400 uppercase tracking-wider">Assigned To</span>
            <p class="font-medium text-gray-900 mt-1">{{ $deal->assignedTo?->name ?? 'Unassigned' }}</p>
        </div>
        <div>
            <span class="text-[10px] text-gray-400 uppercase tracking-wider">Created</span>
            <p class="font-medium text-gray-900 mt-1">{{ $deal->created_at?->format('d M Y') }}</p>
        </div>
    </div>
    @if($deal->notes)
    <div class="px-6 pb-6">
        <span class="text-[10px] text-gray-400 uppercase tracking-wider">Notes</span>
        <p class="text-sm text-gray-700 mt-1 bg-gray-50 border rounded-lg p-3">{{ $deal->notes }}</p>
    </div>
    @endif
</div>
@endsection
