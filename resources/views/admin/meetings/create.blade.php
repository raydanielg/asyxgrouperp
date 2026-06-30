@extends('layouts.admin')

@section('page_title', 'New Meeting')

@section('page_actions')
    <a href="{{ route('admin.meetings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
        ← Back
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('admin.meetings.store') }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 space-y-4">
            <h2 class="text-lg font-bold text-gray-900">Meeting Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Title *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Weekly Project Sync">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type *</label>
                    <select name="type" id="meetingType" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" onchange="toggleProject()">
                        <option value="office" {{ request('type') === 'office' ? 'selected' : '' }}>Office Meeting</option>
                        <option value="project" {{ request('type') === 'project' ? 'selected' : '' }}>Project Meeting</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Mode *</label>
                    <select name="mode" id="meetingMode" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" onchange="toggleLocation()">
                        <option value="physical">Physical</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div id="projectField" style="display:{{ request('type') === 'project' ? '' : 'none' }};">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ (string)request('project_id') === (string)$project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Date *</label>
                    <input type="date" name="meeting_date" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Start Time *</label>
                        <input type="time" name="start_time" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">End Time</label>
                        <input type="time" name="end_time" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                </div>
                <div id="locationField">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Location</label>
                    <input type="text" name="location" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Boardroom A, 3rd Floor">
                </div>
                <div id="linkField" style="display:none;">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Meeting Link</label>
                    <input type="text" name="meeting_link" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="https://zoom.us/...">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Agenda</label>
                <textarea name="agenda" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Meeting agenda items..."></textarea>
            </div>
        </div>

        {{-- Attendees --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 space-y-3">
            <h2 class="text-lg font-bold text-gray-900">Attendees</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                @foreach($users as $user)
                <label class="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="attendees[]" value="{{ $user->id }}" class="rounded">
                    <span class="text-gray-700">{{ $user->name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Action Points --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Action Points</h2>
                <button type="button" onclick="addActionPoint()" class="text-xs font-semibold text-bronze hover:underline">+ Add Action Point</button>
            </div>
            <div id="actionPointsContainer" class="space-y-3"></div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.meetings.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-bronze text-white text-sm font-semibold rounded-lg hover:bg-bronze-dark">Create Meeting</button>
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

let apCount = 0;
function addActionPoint() {
    apCount++;
    const container = document.getElementById('actionPointsContainer');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-1 md:grid-cols-3 gap-2 items-start';
    div.innerHTML = `
        <input type="text" name="action_points[${apCount}][description]" placeholder="Action point description" class="px-3 py-2 border border-gray-200 rounded-lg text-sm md:col-span-1">
        <select name="action_points[${apCount}][assigned_to]" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <option value="">Assign to...</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <input type="date" name="action_points[${apCount}][due_date]" class="px-3 py-2 border border-gray-200 rounded-lg text-sm flex-1">
            <button type="button" onclick="this.closest('.grid').remove()" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    `;
    container.appendChild(div);
}
</script>
@endsection
