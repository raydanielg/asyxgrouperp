@extends('layouts.admin')

@section('page_title', 'Meetings')

@section('page_actions')
    <a href="{{ route('admin.meetings.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-bronze text-white text-sm font-semibold rounded-lg hover:bg-bronze-dark transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Meeting
    </a>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Scheduled</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['scheduled'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Completed</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Project</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['projectMeetings'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Office</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['officeMeetings'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Pending Actions</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['pendingActions'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <select name="type" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">All Types</option>
                <option value="project" {{ request('type') === 'project' ? 'selected' : '' }}>Project Meetings</option>
                <option value="office" {{ request('office') === 'office' ? 'selected' : '' }}>Office Meetings</option>
            </select>
            <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">All Status</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <select name="project_id" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-navy text-white text-sm font-semibold rounded-lg hover:bg-opacity-90">Filter</button>
        </form>
    </div>

    {{-- Meetings List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 bg-gray-50 border-b">
                        <th class="px-4 py-3 font-medium">Date</th>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Type</th>
                        <th class="px-4 py-3 font-medium">Mode</th>
                        <th class="px-4 py-3 font-medium">Project</th>
                        <th class="px-4 py-3 font-medium">Attendees</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $meeting)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">{{ $meeting->meeting_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $meeting->title }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $meeting->type === 'project' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst($meeting->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $meeting->mode === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($meeting->mode) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $meeting->project?->title ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $meeting->attendees->count() }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full
                                {{ $meeting->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $meeting->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $meeting->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($meeting->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.meetings.show', $meeting) }}" class="text-bronze hover:underline text-xs font-semibold">View</a>
                            <a href="{{ route('admin.meetings.edit', $meeting) }}" class="text-blue-600 hover:underline text-xs font-semibold ml-2">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">No meetings found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $meetings->links() }}
    </div>
</div>
@endsection
