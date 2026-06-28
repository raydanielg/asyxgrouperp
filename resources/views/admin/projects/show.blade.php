@extends('layouts.admin')
@section('title', 'Project Details - ' . config('app.name'))
@section('page_title', 'Project Details')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.projects.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Projects</a>
    <button onclick="downloadPdf('{{ route('admin.projects.pdf', $project) }}', '{{ $project->project_number }}')" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
    </button>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-3">{{ $project->title }}</h3>
        <p class="text-xs text-gray-500 mb-4">{{ $project->description ?? 'No description' }}</p>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-gray-400">Project #</span><span class="font-mono text-gray-700">{{ $project->project_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Manager</span><span class="text-gray-700">{{ $project->manager?->name ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Start Date</span><span class="text-gray-700">{{ $project->start_date?->format('d M Y') ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Due Date</span><span class="text-gray-700">{{ $project->due_date?->format('d M Y') ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Budget</span><span class="font-semibold text-gray-900">TZS {{ number_format($project->budget) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Priority</span><span class="text-gray-700">{{ ucfirst($project->priority) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span></div>
        </div>
        <div class="mt-4"><div class="flex items-center justify-between mb-1"><span class="text-[10px] text-gray-400 uppercase">Progress</span><span class="text-xs font-bold text-emerald-700">{{ $project->progress }}%</span></div><div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width:{{ $project->progress }}%"></div></div></div>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl border p-6">
        <div class="flex items-center justify-between mb-3"><h3 class="text-sm font-bold text-gray-900">Tasks</h3><button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">+ Add Task</button></div>
        <div class="space-y-2">
        @forelse($project->tasks as $t)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1"><p class="text-xs font-medium text-gray-900">{{ $t->title }}</p><p class="text-[10px] text-gray-400">{{ $t->assignee?->name ?? 'Unassigned' }} {{ ($t->due_date) ? '- Due {{ $t->due_date->format('d M') }}' : '' }}</p></div>
            <div class="flex items-center gap-2"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($t->status=='done') ? 'bg-emerald-50 text-emerald-700' : (($t->status=='in_progress') ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-600') }}">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span><form id="del-task-{{ $t->id }}" method="POST" action="{{ route('admin.projects.tasks.destroy', $t) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-task-{{ $t->id }}')" class="text-red-500 hover:text-red-700 text-[10px]">Delete</button></div>
        
        </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-4">No tasks yet</p>
        @endforelse
        </div>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Bugs</h3>
        <div class="space-y-2">
        @forelse($project->bugs as $b)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1"><p class="text-xs font-medium text-gray-900">{{ $b->title }}</p><p class="text-[10px] text-gray-400">Severity: {{ ucfirst($b->severity) }}</p></div>
            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($b->status=='open') ? 'bg-red-50 text-red-700' : (($b->status=='fixed') ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600') }}">{{ ucfirst($b->status) }}</span>
        
        </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-4">No bugs reported</p>
        @endforelse
        </div>
    </div>
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Timesheets</h3>
        <div class="space-y-2">
        @forelse($project->timesheets as $ts)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1"><p class="text-xs font-medium text-gray-900">{{ $ts->description ?? 'Timesheet entry' }}</p><p class="text-[10px] text-gray-400">{{ $ts->date->format('d M Y') }}</p></div>
            <span class="text-xs font-semibold text-emerald-700">{{ $ts->hours }}h</span>
        
        </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-4">No timesheet entries</p>
        @endforelse
        </div>
    </div>
</div>
<div id="taskModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Task</h3>
        <form method="POST" action="{{ route('admin.projects.tasks.store', $project) }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="todo">To Do</option><option value="in_progress">In Progress</option><option value="review">Review</option><option value="done">Done</option></select></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Priority</label><select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option></select></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Progress (%)</label><input name="progress" type="number" min="0" max="100" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('taskModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Add Task</button></div>
        </form>
    </div>
</div>
@endsection
