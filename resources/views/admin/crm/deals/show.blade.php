@extends('layouts.admin')
@section('title', 'Deal ' . $deal->deal_number . ' - ' . config('app.name'))
@section('page_title', 'Deal Details')
@section('content')
@php
  $dc = ['open' => '#3b82f6', 'won' => '#10b981', 'lost' => '#ef4444', 'cancelled' => '#9ca3af'][$deal->status] ?? '#9ca3af';
  $stages = ['prospecting' => 'Prospecting', 'qualification' => 'Qualification', 'negotiation' => 'Negotiation', 'proposal' => 'Proposal', 'closed_won' => 'Closed Won', 'closed_lost' => 'Closed Lost'];
  $currentIdx = array_search($deal->stage, array_keys($stages));
@endphp

<div class="mb-4">
    <a href="{{ route('admin.crm-deals.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Deals</a>
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

{{-- Stage tracker --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Pipeline Stage</h4></div>
    <div class="px-5 py-4">
        <div class="flex items-center">
            @foreach($stages as $key => $label)
            @php
                $idx = array_search($key, array_keys($stages));
                $done = $currentIdx !== false && $idx <= $currentIdx;
                $isCurrent = $idx === $currentIdx;
                if ($key === 'closed_lost') { $done = $deal->stage === 'closed_lost'; $isCurrent = $deal->stage === 'closed_lost'; }
            @endphp
            <div class="flex-1 flex flex-col items-center relative">
                @if(!$loop->first)
                <div class="absolute top-2.5 right-1/2 w-full h-px {{ $idx <= $currentIdx ? 'bg-gray-800' : 'bg-gray-200' }}" style="transform:translateY(-50%);left:-50%;right:auto;width:100%;"></div>
                @endif
                <div class="relative z-10 w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $isCurrent ? 'border-gray-900 bg-gray-900' : ($done ? 'border-gray-800 bg-gray-800' : 'border-gray-300 bg-white') }}">
                    @if($done && !$isCurrent)<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                    @if($isCurrent)<span class="w-1.5 h-1.5 rounded-full bg-white"></span>@endif
                </div>
                <span class="mt-2 text-[10px] {{ $isCurrent ? 'font-bold text-gray-900' : ($done ? 'text-gray-600' : 'text-gray-400') }} text-center leading-tight px-1">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <div class="lg:col-span-2 bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Details</h4></div>
        <dl class="p-5 grid grid-cols-2 gap-x-6 gap-y-3">
            <div><dt class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Title</dt><dd class="text-xs text-gray-800 mt-1">{{ $deal->title }}</dd></div>
            <div><dt class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Lead</dt><dd class="text-xs text-gray-800 mt-1">{{ $deal->lead?->full_name ?? '—' }}</dd></div>
            <div><dt class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Stage</dt><dd class="text-xs text-gray-800 mt-1">{{ ucfirst(str_replace('_', ' ', $deal->stage)) }}</dd></div>
            <div><dt class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Expected Close</dt><dd class="text-xs text-gray-800 mt-1">{{ $deal->expected_close_date?->format('d M Y') ?? '—' }}</dd></div>
            <div><dt class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Assigned To</dt><dd class="text-xs text-gray-800 mt-1">{{ $deal->assignedTo?->name ?? 'Unassigned' }}</dd></div>
            <div><dt class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Created</dt><dd class="text-xs text-gray-800 mt-1">{{ $deal->created_at?->format('d M Y') }}</dd></div>
            @if($deal->lead?->company)
            <div><dt class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Company</dt><dd class="text-xs text-gray-800 mt-1">{{ $deal->lead->company }}</dd></div>
            @endif
            @if($deal->project)
            <div><dt class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Project</dt><dd class="text-xs text-gray-800 mt-1">{{ $deal->project->title }}</dd></div>
            @endif
        </dl>
        @if($deal->notes)
        <div class="px-5 pb-5">
            <dt class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Notes</dt>
            <p class="text-xs text-gray-600 mt-1 leading-relaxed whitespace-pre-wrap">{{ $deal->notes }}</p>
        </div>
        @endif
    </div>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden self-start">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Summary</h4></div>
        <dl class="p-5 space-y-2.5">
            <div class="flex justify-between"><dt class="text-[11px] text-gray-400">Deal #</dt><dd class="text-xs font-mono text-gray-700">{{ $deal->deal_number }}</dd></div>
            <div class="flex justify-between"><dt class="text-[11px] text-gray-400">Status</dt><dd class="text-xs text-gray-700">{{ ucfirst($deal->status) }}</dd></div>
            <div class="flex justify-between"><dt class="text-[11px] text-gray-400">Stage</dt><dd class="text-xs text-gray-700">{{ ucfirst(str_replace('_', ' ', $deal->stage)) }}</dd></div>
            <div class="flex justify-between border-t border-gray-800 pt-2.5 mt-1"><dt class="text-xs font-bold text-gray-900">Value</dt><dd class="text-sm font-bold font-mono text-gray-900">TZS {{ number_format($deal->value) }}</dd></div>
        </dl>
    </div>
</div>

{{-- Deal PDF preview --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Deal PDF</h4>
        <a href="{{ route('admin.crm-deals.pdf', $deal) }}" target="_blank" class="text-xs text-gray-400 hover:text-gray-700">Open full size &rarr;</a>
    </div>
    <iframe src="{{ route('admin.crm-deals.pdf', $deal) }}" class="w-full border-0" style="height:800px;"></iframe>
</div>
@endsection
