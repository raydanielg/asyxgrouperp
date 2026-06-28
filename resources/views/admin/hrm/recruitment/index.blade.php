@extends('layouts.admin')
@section('title', 'Recruitment - ' . config('app.name'))
@section('page_title', 'Recruitment & Job Postings')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage job postings and recruitment workflows</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Job Posting
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Title</th><th class="px-5 py-3 font-medium">Department</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Vacancies</th><th class="px-5 py-3 font-medium">Deadline</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($jobs as $j)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $j->title }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $j->department ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $j->job_type)) }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $j->vacancies }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $j->deadline?->format('d M Y') ?? 'No deadline' }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($j->status=='open') ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-600' }}">{{ ucfirst($j->status) }}</span></td>
            <td class="px-5 py-3"><form id="del-job-{{ $j->id }}" method="POST" action="{{ route('admin.job-postings.destroy', $j) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-job-{{ $j->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No job postings</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $jobs->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Job Posting</h3>
        <form method="POST" action="{{ route('admin.job-postings.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Department</label><input name="department" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Location</label><input name="location" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Job Type</label><select name="job_type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="full_time">Full Time</option><option value="part_time">Part Time</option><option value="contract">Contract</option><option value="remote">Remote</option></select></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Vacancies</label><input name="vacancies" type="number" value="1" min="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Deadline</label><input name="deadline" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Requirements</label><textarea name="requirements" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="open">Open</option><option value="closed">Closed</option><option value="draft">Draft</option></select></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
