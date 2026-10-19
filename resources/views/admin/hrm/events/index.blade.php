@extends('layouts.admin')
@section('title', 'HR Events - ' . config('app.name'))
@section('page_title', 'Events Calendar')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $events->total() }} events</span>
        <button type="button" onclick="openEvtDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Event</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Title</th>
                    <th class="px-5 py-3 font-semibold">Date</th>
                    <th class="px-5 py-3 font-semibold">Time</th>
                    <th class="px-5 py-3 font-semibold">Location</th>
                    <th class="px-5 py-3 font-semibold">Type</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($events as $e)
            <tr class="hover:bg-gray-50/60">
                <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $e->title }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $e->event_date->format('d M Y') }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $e->event_time ?? '—' }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $e->location ?? '—' }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-600">{{ ucfirst($e->type) }}</td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end">
                        <form method="POST" action="{{ route('admin.hr-events.destroy', $e) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete event?', text:'{{ addslashes($e->title) }} will be permanently removed.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-10 text-center text-xs text-gray-400">No events</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($events->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $events->links() }}</div>
    @endif
</div>

<div id="evtDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeEvtDrawer()"></div>
    <div id="evtDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Event</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Company event or meeting</p>
            </div>
            <button type="button" onclick="closeEvtDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.hr-events.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="e.g. Team Building" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Date <span class="text-red-500">*</span></label><input name="event_date" type="date" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Time</label><input name="event_time" type="time" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Location</label><input name="location" placeholder="e.g. Conference Room" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Type</label><select name="type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="meeting">Meeting</option><option value="holiday">Holiday</option><option value="training">Training</option><option value="celebration">Celebration</option><option value="other">Other</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="3" placeholder="Details..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create</button>
                <button type="button" onclick="closeEvtDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEvtDrawer() {
    document.getElementById('evtDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('evtDrawerPanel').classList.remove('-translate-x-full'));
}
function closeEvtDrawer() {
    document.getElementById('evtDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('evtDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEvtDrawer(); });
</script>
@endpush
@endsection
