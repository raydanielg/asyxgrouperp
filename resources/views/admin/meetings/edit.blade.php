@extends('layouts.admin')

@section('page_title', 'Edit Meeting')

@section('page_actions')
    <a href="{{ route('admin.meetings.show', $meeting) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">← Back</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('admin.meetings.update', $meeting) }}" class="space-y-6">
        @csrf
        @method('PATCH')
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 space-y-4">
            <h2 class="text-lg font-bold text-gray-900">Meeting Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Title *</label>
                    <input type="text" name="title" required value="{{ $meeting->title }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type *</label>
                    <select name="type" id="meetingType" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" onchange="toggleProject()">
                        <option value="office" {{ $meeting->type === 'office' ? 'selected' : '' }}>Office Meeting</option>
                        <option value="project" {{ $meeting->type === 'project' ? 'selected' : '' }}>Project Meeting</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Mode *</label>
                    <select name="mode" id="meetingMode" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" onchange="toggleLocation()">
                        <option value="physical" {{ $meeting->mode === 'physical' ? 'selected' : '' }}>Physical</option>
                        <option value="online" {{ $meeting->mode === 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>
                <div id="projectField" style="display:{{ $meeting->type === 'project' ? '' : 'none' }};">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $meeting->project_id === $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Date *</label>
                    <input type="date" name="meeting_date" required value="{{ $meeting->meeting_date->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Start *</label>
                        <input type="time" name="start_time" required value="{{ $meeting->start_time }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">End</label>
                        <input type="time" name="end_time" value="{{ $meeting->end_time ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                </div>
                <div id="locationField" style="display:{{ $meeting->mode === 'physical' ? '' : 'none' }};">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Location</label>
                    <input type="text" name="location" value="{{ $meeting->location ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div id="linkField" style="display:{{ $meeting->mode === 'online' ? '' : 'none' }};">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Meeting Link</label>
                    <input type="text" name="meeting_link" value="{{ $meeting->meeting_link ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Agenda</label>
                <textarea name="agenda" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">{{ $meeting->agenda ?? '' }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Minutes</label>
                <textarea name="minutes" rows="4" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Meeting minutes...">{{ $meeting->minutes ?? '' }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Report</label>
                <textarea name="report" rows="4" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Meeting report...">{{ $meeting->report ?? '' }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status *</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    <option value="scheduled" {{ $meeting->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="completed" {{ $meeting->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $meeting->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.meetings.show', $meeting) }}" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-bronze text-white text-sm font-semibold rounded-lg hover:bg-bronze-dark">Update Meeting</button>
        </div>
    </form>
</div>

<script>
function toggleProject() {
    const type = document.getElementById('meetingType').value;
    document.getElementById('projectField').style.display = type === 'project' ? '' : 'none';
}
function toggleLocation() {
    const mode = document.getElementById('meetingMode').value;
    document.getElementById('locationField').style.display = mode === 'physical' ? '' : 'none';
    document.getElementById('linkField').style.display = mode === 'online' ? '' : 'none';
}
</script>
@endsection
