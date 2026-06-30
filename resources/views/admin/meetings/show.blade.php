@extends('layouts.admin')

@section('page_title', $meeting->title)

@section('page_actions')
    <a href="{{ route('admin.meetings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">← Back</a>
    <a href="{{ route('admin.meetings.edit', $meeting) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">Edit</a>
@endsection

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $meeting->title }}</h1>
                <div class="flex items-center gap-3 mt-2">
                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $meeting->type === 'project' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($meeting->type) }}</span>
                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $meeting->mode === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">{{ ucfirst($meeting->mode) }}</span>
                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full
                        {{ $meeting->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $meeting->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $meeting->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">{{ ucfirst($meeting->status) }}</span>
                </div>
            </div>
            <div class="text-right text-sm text-gray-500">
                <p>{{ $meeting->meeting_date->format('d M Y') }}</p>
                <p>{{ $meeting->start_time }}{{ $meeting->end_time ? ' – ' . $meeting->end_time : '' }}</p>
            </div>
        </div>

        @if($meeting->project)
        <div class="mt-4 pt-4 border-t">
            <p class="text-[10px] text-gray-400 uppercase mb-1">Project</p>
            <a href="{{ route('admin.projects.show', $meeting->project) }}" class="text-sm font-semibold text-bronze hover:underline">{{ $meeting->project->title }}</a>
        </div>
        @endif

        @if($meeting->mode === 'physical' && $meeting->location)
        <div class="mt-3"><p class="text-[10px] text-gray-400 uppercase mb-1">Location</p><p class="text-sm text-gray-700">{{ $meeting->location }}</p></div>
        @endif
        @if($meeting->mode === 'online' && $meeting->meeting_link)
        <div class="mt-3"><p class="text-[10px] text-gray-400 uppercase mb-1">Meeting Link</p><a href="{{ $meeting->meeting_link }}" target="_blank" class="text-sm text-blue-600 hover:underline">{{ $meeting->meeting_link }}</a></div>
        @endif

        @if($meeting->agenda)
        <div class="mt-4 pt-4 border-t">
            <p class="text-[10px] text-gray-400 uppercase mb-2">Agenda</p>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $meeting->agenda }}</p>
        </div>
        @endif
    </div>

    {{-- Attendance --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Attendance ({{ $meeting->attendees->where('attended', true)->count() }}/{{ $meeting->attendees->count() }})</h2>
        @if($meeting->attendees->count() > 0)
        <form method="POST" action="{{ route('admin.meetings.attendance', $meeting) }}" class="space-y-3">
            @csrf
            <div class="space-y-2">
                @foreach($meeting->attendees as $attendee)
                <div class="flex items-center justify-between px-4 py-2.5 border border-gray-100 rounded-lg">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="attendees[{{ $attendee->id }}][attended]" value="1" {{ $attendee->attended ? 'checked' : '' }} class="rounded">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $attendee->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $attendee->user->email }}</p>
                        </div>
                    </div>
                    <input type="hidden" name="attendees[{{ $attendee->id }}][id]" value="{{ $attendee->id }}">
                    <input type="text" name="attendees[{{ $attendee->id }}][notes]" placeholder="Notes..." value="{{ $attendee->notes ?? '' }}" class="text-xs px-2 py-1 border border-gray-200 rounded w-40">
                </div>
                @endforeach
            </div>
            @if($meeting->status !== 'cancelled')
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-navy text-white text-sm font-semibold rounded-lg hover:bg-opacity-90">Save Attendance</button>
            </div>
            @endif
        </form>
        @else
        <p class="text-sm text-gray-400">No attendees added.</p>
        @endif
    </div>

    {{-- Action Points --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Action Points ({{ $meeting->actionPoints->count() }})</h2>
        @if($meeting->actionPoints->count() > 0)
        <div class="space-y-2">
            @foreach($meeting->actionPoints as $ap)
            <div class="flex items-start justify-between px-4 py-3 border border-gray-100 rounded-lg">
                <div class="flex-1">
                    <p class="text-sm text-gray-900 {{ $ap->status === 'completed' ? 'line-through text-gray-400' : '' }}">{{ $ap->description }}</p>
                    <div class="flex items-center gap-3 mt-1">
                        @if($ap->assignee)
                        <span class="text-xs text-gray-500">Assigned: {{ $ap->assignee->name }}</span>
                        @endif
                        @if($ap->due_date)
                        <span class="text-xs {{ $ap->due_date->isPast() && $ap->status !== 'completed' ? 'text-red-600 font-semibold' : 'text-gray-500' }}">Due: {{ $ap->due_date->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
                <select onchange="updateActionPoint({{ $ap->id }}, this.value)" class="text-xs px-2 py-1 border border-gray-200 rounded">
                    <option value="pending" {{ $ap->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ $ap->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $ap->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="overdue" {{ $ap->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-400">No action points.</p>
        @endif
    </div>

    {{-- Minutes & Report --}}
    @if($meeting->minutes || $meeting->report)
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 space-y-4">
        @if($meeting->minutes)
        <div>
            <h2 class="text-lg font-bold text-gray-900 mb-2">Minutes</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $meeting->minutes }}</p>
        </div>
        @endif
        @if($meeting->report)
        <div class="pt-4 border-t">
            <h2 class="text-lg font-bold text-gray-900 mb-2">Report</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $meeting->report }}</p>
        </div>
        @endif
    </div>
    @endif
</div>

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
        if (data.success) Swal.fire('Updated!', 'Action point status updated.', 'success');
    });
}
</script>
@endsection
