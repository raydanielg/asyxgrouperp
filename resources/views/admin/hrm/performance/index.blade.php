@extends('layouts.admin')
@section('title', 'Performance Reviews - ' . config('app.name'))
@section('page_title', 'Performance Reviews')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track staff performance goals and appraisals</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Review
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Employee</th><th class="px-5 py-3 font-medium">Period</th><th class="px-5 py-3 font-medium">Rating</th><th class="px-5 py-3 font-medium">Feedback</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($reviews as $r)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $r->employee?->full_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $r->review_period ?? 'N/A' }}</td>
            <td class="px-5 py-3">@for($i=1;$i<=5;$i++)<span class="text-{{ $i <= $r->rating ? 'gold' : 'gray' }}-400">&#9733;</span>@endfor</td>
            <td class="px-5 py-3 text-xs text-gray-400 max-w-xs truncate">{{ $r->feedback ?? '—' }}</td>
            <td class="px-5 py-3"><form id="del-rev-{{ $r->id }}" method="POST" action="{{ route('admin.performance.destroy', $r) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-rev-{{ $r->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        </tr>@empty<tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No performance reviews</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $reviews->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Performance Review</h3>
        <form method="POST" action="{{ route('admin.performance.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Employee *</label><select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }}</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Review Period</label><input name="review_period" placeholder="e.g. Q1 2025" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Goals</label><textarea name="goals" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Achievements</label><textarea name="achievements" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Feedback</label><textarea name="feedback" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Rating (1-5) *</label><select name="rating" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="1">1 - Poor</option><option value="2">2 - Below Average</option><option value="3" selected>3 - Average</option><option value="4">4 - Good</option><option value="5">5 - Excellent</option></select></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
