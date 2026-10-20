@extends('layouts.admin')

@section('title', 'Helpdesk Tickets - ' . config('app.name'))
@section('page_title', 'Support Tickets')

@section('content')
@php
    $prio = ['low' => '#3b82f6', 'medium' => '#f59e0b', 'high' => '#f97316', 'urgent' => '#ef4444'];
    $stat = ['open' => '#10b981', 'in_progress' => '#f59e0b', 'resolved' => '#3b82f6', 'closed' => '#9ca3af'];
    $open = $tickets->where('status', 'open')->count();
    $urgent = $tickets->where('priority', 'urgent')->count();
@endphp

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $tickets->total() }} tickets · {{ $open }} open · {{ $urgent }} urgent</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Ticket</button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ $tickets->total() }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Open</p><p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $open }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">In Progress</p><p class="text-lg font-bold text-amber-600 mt-0.5">{{ $tickets->where('status','in_progress')->count() }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Urgent</p><p class="text-lg font-bold text-red-500 mt-0.5">{{ $urgent }}</p></div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Ticket</th><th class="px-5 py-3 font-semibold">Category</th><th class="px-5 py-3 font-semibold">Priority</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold">Assigned</th><th class="px-5 py-3 font-semibold text-right">Created</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($tickets as $ticket)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5">
                <a href="{{ route('admin.helpdesk-tickets.show', $ticket) }}" class="text-xs font-medium text-gray-900 hover:text-gray-600">{{ $ticket->title }}</a>
                <p class="text-[10px] font-mono text-gray-400 mt-0.5">{{ $ticket->ticket_id }}</p>
            </td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $ticket->category?->name ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $prio[$ticket->priority] ?? '#9ca3af' }}"></span>{{ $ticket->priority }}</span></td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $stat[$ticket->status] ?? '#9ca3af' }}"></span>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></td>
            <td class="px-5 py-3.5 text-xs {{ $ticket->assignee ? 'text-gray-500' : 'text-gray-300' }}">{{ $ticket->assignee?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right text-gray-400">{{ $ticket->created_at->format('d M Y') }}</td>
            <td class="px-5 py-3.5 text-right"><a href="{{ route('admin.helpdesk-tickets.show', $ticket) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a></td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No tickets found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($tickets->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $tickets->links() }}</div>
    @endif
</div>

{{-- New Ticket Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Ticket</h3><p class="text-[11px] text-slate-400 mt-0.5">Log a support request</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.helpdesk-tickets.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="Brief summary of the issue" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description <span class="text-red-500">*</span></label><textarea name="description" required rows="5" placeholder="Describe the problem in detail..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Priority <span class="text-red-500">*</span></label><select name="priority" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="urgent">Urgent</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><select name="category_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">None</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Ticket</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer() {
    document.getElementById('createDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('createDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDrawer() {
    document.getElementById('createDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('createDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
</script>
@endpush
