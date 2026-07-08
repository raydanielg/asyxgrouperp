@extends('layouts.admin')
@section('title', 'Job Cards')
@section('page_title', 'Job Cards')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage work orders and job assignments</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Job Card
    </button>
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl border p-4"><span class="text-[10px] font-medium text-gray-500">Total</span><p class="text-xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><span class="text-[10px] font-medium text-amber-600">Open</span><p class="text-xl font-bold text-amber-600 mt-1">{{ $stats['open'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><span class="text-[10px] font-medium text-sky-600">In Progress</span><p class="text-xl font-bold text-sky-600 mt-1">{{ $stats['in_progress'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><span class="text-[10px] font-medium text-emerald-600">Resolved</span><p class="text-xl font-bold text-emerald-600 mt-1">{{ $stats['resolved'] }}</p></div>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Job #</th><th class="px-5 py-3 font-medium">Title</th><th class="px-5 py-3 font-medium">Project</th><th class="px-5 py-3 font-medium">Assigned To</th><th class="px-5 py-3 font-medium">Priority</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Due Date</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($jobCards as $jc)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $jc->job_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-900 max-w-[200px]">{{ Str::limit($jc->title, 40) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $jc->project?->title ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $jc->assignedTo?->name ?? '—' }}</td>
            <td class="px-5 py-3">@php $pc = ['low'=>'gray','medium'=>'amber','high'=>'rose','critical'=>'red']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $pc[$jc->priority] ?? 'gray' }}-50 text-{{ $pc[$jc->priority] ?? 'gray' }}-700">{{ ucfirst($jc->priority) }}</span></td>
            <td class="px-5 py-3">@php $sc = ['open'=>'amber','in_progress'=>'sky','resolved'=>'emerald','closed'=>'gray']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $sc[$jc->status] ?? 'gray' }}-50 text-{{ $sc[$jc->status] ?? 'gray' }}-700">{{ str_replace('_', ' ', ucfirst($jc->status)) }}</span></td>
            <td class="px-5 py-3 text-xs {{ $jc->due_date && $jc->due_date->isPast() && $jc->status !== 'resolved' ? 'text-red-500 font-medium' : 'text-gray-400' }}">{{ $jc->due_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3 text-xs flex gap-1">
                <a href="{{ route('admin.job-cards.show', $jc) }}" class="text-indigo-600 hover:text-indigo-700" title="View"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/></svg></a>
                <a href="{{ route('admin.job-cards.print', $jc) }}" target="_blank" class="text-gray-500 hover:text-gray-700" title="Print"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></a>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No job cards found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $jobCards->links() }}</div>
</div>
{{-- Create Modal --}}
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Job Card</h3>
        <form method="POST" action="{{ route('admin.job-cards.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></textarea></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label><select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="">None</option>@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Assign To</label><select name="assigned_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="">None</option>@foreach($technicians as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Priority *</label><select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Create</button></div>
        </form>
    </div>
</div>
@endsection