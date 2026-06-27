@extends('layouts.admin')
@section('title', 'Timesheets - ' . config('app.name'))
@section('page_title', 'Timesheets')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track employee work hours per project</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Entry
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Project</th><th class="px-5 py-3 font-medium">Task</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Hours</th><th class="px-5 py-3 font-medium">Description</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($timesheets as $t)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $t->project?->title ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $t->task?->title ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $t->date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-emerald-700">{{ $t->hours }}h</td>
            <td class="px-5 py-3 text-xs text-gray-400 max-w-xs truncate">{{ $t->description ?? '—' }}</td>
            <td class="px-5 py-3"><form id="del-ts-{{ $t->id }}" method="POST" action="{{ route('admin.timesheets.destroy', $t) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-ts-{{ $t->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        </tr>@empty<tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No timesheet entries</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $timesheets->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Timesheet Entry</h3>
        <form method="POST" action="{{ route('admin.timesheets.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Project *</label><select name="project_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Date *</label><input name="date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Hours *</label><input name="hours" type="number" step="0.01" required value="8" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
