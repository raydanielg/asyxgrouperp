@extends('layouts.admin')
@section('title', 'Tender Details - ' . config('app.name'))
@section('page_title', 'Tender: ' . $tender->tender_number)
@section('content')
<div class="mb-4"><a href="{{ route('admin.tenders.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Tenders</a></div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Tender Information</h3>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-gray-400">Tender Number</span><span class="font-mono text-gray-700">{{ $tender->tender_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Title</span><span class="text-gray-700">{{ $tender->title }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Client</span><span class="text-gray-700">{{ $tender->client_name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Organization</span><span class="text-gray-700">{{ $tender->client_organization ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Email</span><span class="text-gray-700">{{ $tender->client_email ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Phone</span><span class="text-gray-700">{{ $tender->client_phone ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Est. Value</span><span class="font-semibold text-gray-900">TZS {{ number_format($tender->estimated_value) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Submission</span><span class="text-gray-700">{{ $tender->submission_date?->format('d M Y') ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Closing</span><span class="text-gray-700">{{ $tender->closing_date?->format('d M Y') ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status</span>@php $sc=['received'=>'amber','under_review'=>'sky','converted'=>'emerald','rejected'=>'red']; $c=$sc[$tender->status]??'gray'; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-{{ $c }}-50 text-{{ $c }}-700">{{ ucfirst(str_replace('_',' ',$tender->status)) }}</span></div>
        </div>
        @if($tender->description)
        <div class="mt-3 pt-3 border-t"><p class="text-[10px] text-gray-400 uppercase mb-1">Description</p><p class="text-xs text-gray-600">{{ $tender->description }}</p></div>
        @endif
        @if($tender->requirements)
        <div class="mt-3"><p class="text-[10px] text-gray-400 uppercase mb-1">Requirements</p><p class="text-xs text-gray-600">{{ $tender->requirements }}</p></div>
        @endif
        @if($tender->status !== 'converted')
        <div class="mt-4">
            <form method="POST" action="{{ route('admin.tenders.convert-to-lead', $tender) }}">@csrf<button type="submit" class="w-full px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700" onclick="return confirm('Convert this tender to a Lead?')">Convert to Lead →</button></form>
        </div>
        @endif
    </div>
    <div class="lg:col-span-2 space-y-4">
        @if($tender->lead)
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Converted Lead</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-gray-400">Lead Number</span><a href="{{ route('admin.crm-leads.index') }}" class="font-mono text-emerald-600">{{ $tender->lead->lead_number }}</a></div>
                <div class="flex justify-between"><span class="text-gray-400">Name</span><span class="text-gray-700">{{ $tender->lead->full_name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="text-emerald-700">{{ ucfirst($tender->lead->status) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Deals</span><span class="text-gray-700">{{ $tender->lead->deals->count() }}</span></div>
            </div>
        @if($tender->lead->deals->count() > 0)
            <div class="mt-3 pt-3 border-t"><p class="text-[10px] text-gray-400 uppercase mb-2">Deals from this Lead</p>
        @foreach($tender->lead->deals as $deal)
        <div class="flex items-center justify-between text-xs py-1"><span class="text-gray-700">{{ $deal->title }}</span><span class="text-gray-500">TZS {{ number_format($deal->value) }}</span></div>
        @endforeach
        </div>
        @endif
        </div>
        @endif
    </div>
</div>
@endsection
