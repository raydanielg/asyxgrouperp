@extends('layouts.admin')

@section('page_title', $meeting->title)

@section('content')
@php
    $statusColor = ['completed' => '#10b981', 'scheduled' => '#3b82f6', 'cancelled' => '#ef4444'][$meeting->status] ?? '#9ca3af';
    $typeColor = ['project' => '#8b5cf6'][$meeting->type] ?? '#f59e0b';
    $modeColor = ['online' => '#3b82f6'][$meeting->mode] ?? '#6b7280';
@endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.meetings.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Meetings</a>
    <a href="{{ route('admin.meetings.edit', $meeting) }}" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Edit Meeting</a>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between gap-4 flex-wrap">
        <div class="min-w-0">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $typeColor }}"></span>{{ ucfirst($meeting->type) }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $modeColor }}"></span>{{ ucfirst($meeting->mode) }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $statusColor }}"></span>{{ ucfirst($meeting->status) }}</span>
            </div>
            <h1 class="text-base font-bold text-gray-900 mt-1.5">{{ $meeting->title }}</h1>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Date &amp; Time</p>
            <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $meeting->meeting_date->format('d M Y') }}</p>
            <p class="text-xs text-gray-500">{{ $meeting->start_time }}{{ $meeting->end_time ? ' – ' . $meeting->end_time : '' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">
        @if($meeting->project)
        <div class="px-5 py-3.5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Project</p>
            <a href="{{ route('admin.projects.show', $meeting->project) }}" class="text-xs font-medium text-gray-900 hover:text-gray-600">{{ $meeting->project->title }}</a>
        </div>
        @endif
        @if($meeting->mode === 'physical' && $meeting->location)
        <div class="px-5 py-3.5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Location</p>
            <p class="text-xs text-gray-700">{{ $meeting->location }}</p>
        </div>
        @endif
        @if($meeting->mode === 'online' && $meeting->meeting_link)
        <div class="px-5 py-3.5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Meeting Link</p>
            <a href="{{ $meeting->meeting_link }}" target="_blank" class="text-xs font-medium text-blue-600 hover:underline break-all">{{ $meeting->meeting_link }}</a>
        </div>
        @endif
    </div>

    @if($meeting->agenda)
    <div class="px-5 py-4 border-t border-gray-100">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Agenda</p>
        <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $meeting->agenda }}</p>
    </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    {{-- Attendance --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Attendance</h3>
            <span class="text-[10px] text-gray-400">{{ $meeting->attendees->where('attended', true)->count() }}/{{ $meeting->attendees->count() }} attended</span>
        </div>
        @if($meeting->attendees->count() > 0)
        <form method="POST" action="{{ route('admin.meetings.attendance', $meeting) }}">
            @csrf
            <div class="divide-y divide-gray-100">
                @foreach($meeting->attendees as $attendee)
                <div class="flex items-center justify-between gap-3 px-5 py-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <input type="checkbox" name="attendees[{{ $attendee->id }}][attended]" value="1" {{ $attendee->attended ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400 flex-shrink-0">
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-900 truncate">{{ $attendee->user->name }}</p>
                            <p class="text-[10px] text-gray-400 truncate">{{ $attendee->user->email }}</p>
                        </div>
                    </div>
                    <input type="hidden" name="attendees[{{ $attendee->id }}][id]" value="{{ $attendee->id }}">
                    <input type="text" name="attendees[{{ $attendee->id }}][notes]" placeholder="Notes..." value="{{ $attendee->notes ?? '' }}" class="text-xs px-2.5 py-1.5 border border-gray-200 rounded w-36 outline-none focus:border-gray-400 flex-shrink-0">
                </div>
                @endforeach
            </div>
            @if($meeting->status !== 'cancelled')
            <div class="px-5 py-3 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-medium rounded hover:bg-slate-700">Save Attendance</button>
            </div>
            @endif
        </form>
        @else
        <p class="px-5 py-6 text-xs text-gray-400 text-center">No attendees added.</p>
        @endif
    </div>

    {{-- Action Points --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Action Points</h3>
            <span class="text-[10px] text-gray-400">{{ $meeting->actionPoints->count() }}</span>
        </div>
        @if($meeting->actionPoints->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($meeting->actionPoints as $ap)
            @php $apc = ['completed' => '#10b981', 'in_progress' => '#f59e0b', 'overdue' => '#ef4444'][$ap->status] ?? '#9ca3af'; @endphp
            <div class="flex items-start justify-between gap-3 px-5 py-3">
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-900 {{ $ap->status === 'completed' ? 'line-through text-gray-400' : '' }}">{{ $ap->description }}</p>
                    <div class="flex items-center gap-3 mt-1">
                        @if($ap->assignee)
                        <span class="text-[10px] text-gray-400">{{ $ap->assignee->name }}</span>
                        @endif
                        @if($ap->due_date)
                        <span class="text-[10px] {{ $ap->due_date->isPast() && $ap->status !== 'completed' ? 'text-red-600 font-medium' : 'text-gray-400' }}">Due {{ $ap->due_date->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span style="width:6px;height:6px;border-radius:99px;background:{{ $apc }}"></span>
                    <select onchange="updateActionPoint({{ $ap->id }}, this.value)" class="text-xs px-2 py-1 border border-gray-200 rounded bg-white outline-none focus:border-gray-400">
                        <option value="pending" {{ $ap->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $ap->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ $ap->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="overdue" {{ $ap->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="px-5 py-6 text-xs text-gray-400 text-center">No action points.</p>
        @endif
    </div>
</div>

{{-- Minutes & Report --}}
@if($meeting->minutes || $meeting->report)
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    @if($meeting->minutes)
    <div class="px-5 py-4 {{ $meeting->report ? 'border-b border-gray-100' : '' }}">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Minutes</h3>
        <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $meeting->minutes }}</p>
    </div>
    @endif
    @if($meeting->report)
    <div class="px-5 py-4">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Report</h3>
        <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $meeting->report }}</p>
    </div>
    @endif
</div>
@endif
@endsection

@push('scripts')
<script>
function updateActionPoint(id, status) {
    fetch(`{{ route('admin.meetings.action-points.update', ['actionPoint' => 'PLACEHOLDER']) }}`.replace('PLACEHOLDER', id), {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ status: status })
    }).then(r => r.json()).then(data => {
        if (data.success) Swal.fire({icon: 'success', title: 'Updated', text: 'Action point status updated.', timer: 1500, showConfirmButton: false});
    });
}
</script>
@endpush
