@extends('layouts.admin')
@section('title', 'Projects - ' . config('app.name'))
@section('page_title', 'Projects')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage projects, tasks, and progress</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Project
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Project #</th><th class="px-5 py-3 font-medium">Title</th><th class="px-5 py-3 font-medium">Manager</th><th class="px-5 py-3 font-medium">Progress</th><th class="px-5 py-3 font-medium">Due Date</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($projects as $p)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $p->project_number }}</td>
            <td class="px-5 py-3"><a href="{{ route('admin.projects.show', $p) }}" class="text-xs font-medium text-gray-900 hover:text-emerald-600">{{ $p->title }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $p->manager?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3"><div class="flex items-center gap-2"><div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width:{{ $p->progress }}%"></div></div><span class="text-[10px] text-gray-500">{{ $p->progress }}%</span></div></td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $p->due_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3">@php $c=['planning'=>'sky','in_progress'=>'amber','completed'=>'emerald','on_hold'=>'gray','cancelled'=>'red']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$p->status] ?? 'gray' }}-50 text-{{ $c[$p->status] ?? 'gray' }}-700">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-2"><a href="{{ route('admin.projects.show', $p) }}" class="text-sky-600 hover:text-sky-700 text-xs">View</a><form id="del-prj-{{ $p->id }}" method="POST" action="{{ route('admin.projects.destroy', $p) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-prj-{{ $p->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        </tr>@empty<tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No projects found</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $projects->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Project</h3>
        <form method="POST" action="{{ route('admin.projects.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input name="start_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Manager</label><select name="manager_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Unassigned</option>@foreach($managers as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach</select></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="planning">Planning</option><option value="in_progress">In Progress</option><option value="completed">Completed</option><option value="on_hold">On Hold</option><option value="cancelled">Cancelled</option></select></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Priority</label><select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></div></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Progress (%)</label><input name="progress" type="number" min="0" max="100" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Budget</label><input name="budget" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
