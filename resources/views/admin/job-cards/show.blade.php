@extends('layouts.admin')
@section('title', 'Job Card - ' . $jobCard->job_number)
@section('page_title', 'Job Card: ' . $jobCard->job_number)
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">View and manage job card details</p>
    <div class="flex gap-2">
        <a href="{{ route('admin.job-cards.print', $jobCard) }}" target="_blank" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print
        </a>
        <a href="{{ route('admin.job-cards.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors">Back</a>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-start justify-between mb-4">
                <div><h3 class="text-lg font-bold text-gray-900">{{ $jobCard->title }}</h3><p class="text-xs text-gray-400 mt-1">Created {{ $jobCard->created_at->format('d M Y H:i') }} by {{ $jobCard->creator?->name ?? 'System' }}</p></div>
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium @if($jobCard->status === 'open') bg-amber-50 text-amber-700 @elseif($jobCard->status === 'in_progress') bg-sky-50 text-sky-700 @elseif($jobCard->status === 'resolved') bg-emerald-50 text-emerald-700 @else bg-gray-50 text-gray-700 @endif">{{ str_replace('_', ' ', ucfirst($jobCard->status)) }}</span>
            </div>
            @if($jobCard->description)
            <div class="mb-4"><h4 class="text-xs font-medium text-gray-500 mb-1">Description</h4><p class="text-sm text-gray-700">{{ $jobCard->description }}</p></div>
            @endif
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div><span class="text-xs text-gray-400">Priority</span><p class="font-medium text-gray-900 mt-0.5">@php $pc = ['low'=>'Low','medium'=>'Medium','high'=>'High','critical'=>'Critical']; @endphp{{ $pc[$jobCard->priority] ?? 'Medium' }}</p></div>
                <div><span class="text-xs text-gray-400">Due Date</span><p class="font-medium text-gray-900 mt-0.5">{{ $jobCard->due_date?->format('d M Y') ?? '—' }}</p></div>
                <div><span class="text-xs text-gray-400">Resolved At</span><p class="font-medium text-gray-900 mt-0.5">{{ $jobCard->resolved_at?->format('d M Y H:i') ?? '—' }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-6">
            <h4 class="text-sm font-bold text-gray-900 mb-3">Update Status</h4>
            <form method="POST" action="{{ route('admin.job-cards.update', $jobCard) }}" class="space-y-3">@csrf @method('PATCH')
                <input type="hidden" name="title" value="{{ $jobCard->title }}">
                <input type="hidden" name="priority" value="{{ $jobCard->priority }}">
                <div class="flex items-end gap-3">
                    <div class="flex-1"><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="open" {{ $jobCard->status === 'open' ? 'selected' : '' }}>Open</option><option value="in_progress" {{ $jobCard->status === 'in_progress' ? 'selected' : '' }}>In Progress</option><option value="resolved" {{ $jobCard->status === 'resolved' ? 'selected' : '' }}>Resolved</option><option value="closed" {{ $jobCard->status === 'closed' ? 'selected' : '' }}>Closed</option></select></div>
                    <div class="flex-1"><label class="block text-xs font-medium text-gray-600 mb-1">Assign To</label><select name="assigned_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="">Unassigned</option>@foreach($jobCard->assignedTo ? collect([$jobCard->assignedTo]) : collect([]) as $t)<option value="{{ $t->id }}" selected>{{ $t->name }}</option>@endforeach</select></div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Update</button>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Resolution Notes</label><textarea name="resolution_notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">{{ $jobCard->resolution_notes }}</textarea></div>
            </form>
        </div>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-xl border p-5">
            <h4 class="text-xs font-bold text-gray-900 mb-3">Details</h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-xs text-gray-400">Project</span><span class="text-xs font-medium text-gray-900">{{ $jobCard->project?->title ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-xs text-gray-400">Assigned To</span><span class="text-xs font-medium text-gray-900">{{ $jobCard->assignedTo?->name ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-xs text-gray-400">Created By</span><span class="text-xs font-medium text-gray-900">{{ $jobCard->creator?->name ?? 'System' }}</span></div>
                <div class="flex justify-between"><span class="text-xs text-gray-400">Job Number</span><span class="text-xs font-mono font-medium text-gray-900">{{ $jobCard->job_number }}</span></div>
            </div>
        </div>
        @if($jobCard->notes)
        <div class="bg-white rounded-xl border p-5">
            <h4 class="text-xs font-bold text-gray-900 mb-2">Notes</h4>
            <p class="text-xs text-gray-600">{{ $jobCard->notes }}</p>
        </div>
        @endif
        <form method="POST" action="{{ route('admin.job-cards.destroy', $jobCard) }}" onsubmit="return confirm('Delete this job card?')">@csrf @method('DELETE')
            <button type="submit" class="w-full px-4 py-2 border border-red-200 text-red-600 text-sm rounded-lg hover:bg-red-50 transition-colors">Delete Job Card</button>
        </form>
    </div>
</div>
@endsection