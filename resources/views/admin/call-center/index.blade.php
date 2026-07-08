@extends('layouts.admin')
@section('title', 'Call Center - ' . config('app.name'))
@section('page_title', 'Call Center Dashboard')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $stats['total_calls'] }}</p><p class="text-xs text-gray-500 mt-1">Total Calls</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-sky-600">{{ $stats['inbound'] }}</p><p class="text-xs text-gray-500 mt-1">Inbound</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $stats['outbound'] }}</p><p class="text-xs text-gray-500 mt-1">Outbound</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-red-600">{{ $stats['missed'] }}</p><p class="text-xs text-gray-500 mt-1">Missed</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-gray-700">{{ sprintf('%02d:%02d', floor($stats['avg_duration']/60), $stats['avg_duration']%60) }}</p><p class="text-xs text-gray-500 mt-1">Avg Duration</p></div>
</div>

{{-- Tickets Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $ticketStats['total'] }}</p><p class="text-xs text-gray-500 mt-1">Total Tickets</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-amber-600">{{ $ticketStats['open'] }}</p><p class="text-xs text-gray-500 mt-1">Open</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-sky-600">{{ $ticketStats['in_progress'] }}</p><p class="text-xs text-gray-500 mt-1">In Progress</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $ticketStats['resolved'] }}</p><p class="text-xs text-gray-500 mt-1">Resolved</p></div>
</div>

{{-- Action Points Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $apStats['total'] }}</p><p class="text-xs text-gray-500 mt-1">Total Action Points</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-amber-600">{{ $apStats['pending'] }}</p><p class="text-xs text-gray-500 mt-1">Pending Approval</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-sky-600">{{ $apStats['approved'] }}</p><p class="text-xs text-gray-500 mt-1">Approved</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-red-600">{{ $apStats['overdue'] }}</p><p class="text-xs text-gray-500 mt-1">Overdue</p></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Recent Calls --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
            <h4 class="text-sm font-bold text-gray-700">Recent Calls</h4>
            <a href="{{ route('admin.call-center.calls') }}" class="text-xs text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Direction</th><th class="px-4 py-2 font-medium">Phone</th><th class="px-4 py-2 font-medium">Duration</th><th class="px-4 py-2 font-medium">Status</th><th class="px-4 py-2 font-medium">Time</th></tr></thead>
            <tbody>
            @forelse($recentCalls as $call)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $call->call_direction === 'inbound' ? 'bg-sky-50 text-sky-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $call->call_direction }}</span></td>
                <td class="px-4 py-2 text-xs text-gray-600">{{ $call->caller_phone }}</td>
                <td class="px-4 py-2 text-xs text-gray-600">{{ $call->duration_formatted }}</td>
                <td class="px-4 py-2 text-xs"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $call->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($call->status === 'missed' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ $call->status }}</span></td>
                <td class="px-4 py-2 text-xs text-gray-400">{{ $call->call_start->format('d M H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-xs">No calls logged</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>

    {{-- Campaigns --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h4 class="text-sm font-bold text-gray-700">Campaigns</h4></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Name</th><th class="px-4 py-2 font-medium">Calls</th><th class="px-4 py-2 font-medium">Status</th></tr></thead>
            <tbody>
            @forelse($campaigns as $camp)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs text-gray-700">{{ $camp->name }}</td>
                <td class="px-4 py-2 text-xs text-gray-600">{{ $camp->call_logs_count }}</td>
                <td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $camp->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-500' }}">{{ $camp->status }}</span></td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400 text-xs">No campaigns</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="px-5 py-3 border-t">{{ $campaigns->links() }}</div>
    </div>
</div>

{{-- Recent Tickets --}}
<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-700">Recent Tickets</h4>
        <a href="{{ route('admin.call-center.tickets') }}" class="text-xs text-emerald-600 hover:text-emerald-700">View All</a>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Ticket#</th><th class="px-4 py-2 font-medium">Subject</th><th class="px-4 py-2 font-medium">Priority</th><th class="px-4 py-2 font-medium">Status</th><th class="px-4 py-2 font-medium">Created</th></tr></thead>
        <tbody>
        @forelse($myTickets as $ticket)
        <tr class="border-t border-gray-100">
            <td class="px-4 py-2 text-xs font-mono text-gray-600">{{ $ticket->ticket_no }}</td>
            <td class="px-4 py-2 text-xs text-gray-700 max-w-xs truncate">{{ $ticket->subject }}</td>
            <td class="px-4 py-2 text-xs">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $ticket->priority === 'urgent' ? 'bg-red-50 text-red-700' : ($ticket->priority === 'high' ? 'bg-amber-50 text-amber-700' : ($ticket->priority === 'medium' ? 'bg-sky-50 text-sky-700' : 'bg-gray-50 text-gray-600')) }}">{{ $ticket->priority }}</span>
            </td>
            <td class="px-4 py-2 text-xs">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $ticket->status === 'resolved' || $ticket->status === 'closed' ? 'bg-emerald-50 text-emerald-700' : ($ticket->status === 'in_progress' ? 'bg-sky-50 text-sky-700' : 'bg-amber-50 text-amber-700') }}">{{ str_replace('_', ' ', $ticket->status) }}</span>
            </td>
            <td class="px-4 py-2 text-xs text-gray-400">{{ $ticket->created_at->diffForHumans() }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-xs">No tickets yet.
            <button onclick="showCreateTicketModal()" class="text-emerald-600 hover:text-emerald-700 font-medium">Create one</button>
        </td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

{{-- Action Points Pending Approval --}}
@if($isAdmin && $apStats['pending'] > 0)
<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-700">Action Points Pending Approval</h4>
        <span class="text-xs text-amber-600 font-medium">{{ $apStats['pending'] }} pending</span>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Activity</th><th class="px-4 py-2 font-medium">Responsible</th><th class="px-4 py-2 font-medium">Batch</th><th class="px-4 py-2 font-medium">Uploaded By</th><th class="px-4 py-2 font-medium"></th></tr></thead>
        <tbody>
        @foreach($recentActionPoints->where('approval_status', 'pending')->take(5) as $ap)
        <tr class="border-t border-gray-100">
            <td class="px-4 py-2 text-xs text-gray-700 max-w-xs truncate">{{ $ap->activity }}</td>
            <td class="px-4 py-2 text-xs text-gray-600">{{ $ap->responsible_person }}</td>
            <td class="px-4 py-2 text-[10px] text-gray-400 font-mono">{{ $ap->import_batch }}</td>
            <td class="px-4 py-2 text-xs text-gray-600">{{ $ap->creator?->name ?? '—' }}</td>
            <td class="px-4 py-2 text-xs">
                <a href="{{ route('admin.call-center.action-points.import') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">Review</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endif

{{-- Create Ticket Modal --}}
<div id="createTicketModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden" onclick="if(event.target===this)closeCreateTicketModal()">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl" onclick="event.stopPropagation()">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Ticket</h3>
        <form id="createTicketForm" method="POST" action="{{ route('admin.call-center.tickets.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Subject *</label>
                <input type="text" name="subject" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                <input type="text" name="category" placeholder="e.g. Technical, Billing" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Priority *</label>
                <select name="priority" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            @if($isAdmin)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Assign To</label>
                <select name="assigned_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="">— Unassigned —</option>
                    @foreach($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="flex gap-2 pt-2">
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Ticket</button>
                <button type="button" onclick="closeCreateTicketModal()" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showCreateTicketModal() { document.getElementById('createTicketModal').classList.remove('hidden'); }
function closeCreateTicketModal() { document.getElementById('createTicketModal').classList.add('hidden'); }
</script>
@endpush
