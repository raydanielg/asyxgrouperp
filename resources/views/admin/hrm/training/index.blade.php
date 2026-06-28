@extends('layouts.admin')
@section('title', 'Training - ' . config('app.name'))
@section('page_title', 'Training Plans')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage employee training programs</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Training
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Title</th><th class="px-5 py-3 font-medium">Trainer</th><th class="px-5 py-3 font-medium">Dates</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($trainings as $t)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $t->title }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $t->trainer ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $t->start_date?->format('d M Y') ?? '—' }} {{ ($t->end_date) ? '- {{ $t->end_date->format('d M Y') }}' : '' }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($t->status=='completed') ? 'bg-emerald-50 text-emerald-700' : ($t->status=='scheduled') ? 'bg-sky-50 text-sky-700' : 'if($t->status=='in_progress')bg-amber-50 text-amber-700 @else bg-gray-50 text-gray-600' }}">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span></td>
            <td class="px-5 py-3"><form id="del-tr-{{ $t->id }}" method="POST" action="{{ route('admin.training.destroy', $t) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-tr-{{ $t->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No training programs</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $trainings->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Training Program</h3>
        <form method="POST" action="{{ route('admin.training.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Trainer</label><input name="trainer" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input name="start_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input name="end_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="scheduled">Scheduled</option><option value="in_progress">In Progress</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
