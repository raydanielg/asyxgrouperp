@extends('layouts.admin')
@section('title', 'Call Center Tickets - ' . config('app.name'))
@section('page_title', 'Call Center Tickets')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.call-center.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Dashboard</a>
    <button onclick="showTicketModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ New Ticket</button>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border p-4 mb-6">
    <form method="GET" action="{{ route('admin.call-center.tickets') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <option value="">All Statuses</option>
                <option value="open" @selected(request('status') === 'open')>Open</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                <option value="resolved" @selected(request('status') === 'resolved')>Resolved</option>
                <option value="closed" @selected(request('status') === 'closed')>Closed</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Priority</label>
            <select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <option value="">All Priorities</option>
                <option value="low" @selected(request('priority') === 'low')>Low</option>
                <option value="medium" @selected(request('priority') === 'medium')>Medium</option>
                <option value="high" @selected(request('priority') === 'high')>High</option>
                <option value="urgent" @selected(request('priority') === 'urgent')>Urgent</option>
            </select>
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Filter</button>
            <a href="{{ route('admin.call-center.tickets') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Clear</a>
        </div>
    </form>
</div>

{{-- Tickets Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-700">All Tickets</h4>
        <span class="text-xs text-gray-500">{{ $tickets->total() }} records</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50">
                    <th class="px-4 py-2 font-medium">Ticket#</th>
                    <th class="px-4 py-2 font-medium">Subject</th>
                    <th class="px-4 py-2 font-medium">Category</th>
                    <th class="px-4 py-2 font-medium">Priority</th>
                    <th class="px-4 py-2 font-medium">Status</th>
                    <th class="px-4 py-2 font-medium">Assigned To</th>
                    <th class="px-4 py-2 font-medium">Created</th>
                    <th class="px-4 py-2 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2 text-xs font-mono text-gray-600">{{ $ticket->ticket_no }}</td>
                    <td class="px-4 py-2 text-xs text-gray-700 max-w-xs truncate">{{ $ticket->subject }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $ticket->category ?: '—' }}</td>
                    <td class="px-4 py-2 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $ticket->priority === 'urgent' ? 'bg-red-50 text-red-700' : ($ticket->priority === 'high' ? 'bg-amber-50 text-amber-700' : ($ticket->priority === 'medium' ? 'bg-sky-50 text-sky-700' : 'bg-gray-50 text-gray-600')) }}">{{ $ticket->priority }}</span>
                    </td>
                    <td class="px-4 py-2 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $ticket->status === 'resolved' || $ticket->status === 'closed' ? 'bg-emerald-50 text-emerald-700' : ($ticket->status === 'in_progress' ? 'bg-sky-50 text-sky-700' : 'bg-amber-50 text-amber-700') }}">{{ str_replace('_', ' ', $ticket->status) }}</span>
                    </td>
                    <td class="px-4 py-2 text-xs text-gray-600">{{ $ticket->assignee?->name ?: '—' }}</td>
                    <td class="px-4 py-2 text-xs text-gray-400">{{ $ticket->created_at->format('d M H:i') }}</td>
                    <td class="px-4 py-2 text-xs">
                        <button onclick="showUpdateModal('{{ $ticket->id }}','{{ $ticket->status }}','{{ $ticket->priority }}','{{ $ticket->assigned_to }}','{{ addslashes($ticket->resolution_notes ?? '') }}')" class="text-emerald-600 hover:text-emerald-700 font-medium">Update</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400 text-xs">No tickets found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $tickets->links() }}</div>
</div>

{{-- Create Modal --}}
<div id="ticketModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden" onclick="if(event.target===this)closeTicketModal()">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl" onclick="event.stopPropagation()">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Ticket</h3>
        <form method="POST" action="{{ route('admin.call-center.tickets.store') }}" class="space-y-4">
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
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button>
                <button type="button" onclick="closeTicketModal()" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Update Modal --}}
<div id="updateTicketModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden" onclick="if(event.target===this)closeUpdateModal()">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl" onclick="event.stopPropagation()">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Update Ticket</h3>
        <form id="updateTicketForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="ticket_id" id="update_ticket_id">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status *</label>
                <select name="status" id="update_status" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Priority</label>
                <select name="priority" id="update_priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Resolution Notes</label>
                <textarea name="resolution_notes" id="update_notes" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update</button>
                <button type="button" onclick="closeUpdateModal()" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showTicketModal() { document.getElementById('ticketModal').classList.remove('hidden'); }
function closeTicketModal() { document.getElementById('ticketModal').classList.add('hidden'); }

function showUpdateModal(id, status, priority, assignedTo, notes) {
    document.getElementById('update_ticket_id').value = id;
    document.getElementById('update_status').value = status;
    document.getElementById('update_priority').value = priority;
    document.getElementById('update_notes').value = notes;
    document.getElementById('updateTicketForm').action = '{{ route('admin.call-center.tickets.update', '') }}/' + id;
    document.getElementById('updateTicketModal').classList.remove('hidden');
}
function closeUpdateModal() { document.getElementById('updateTicketModal').classList.add('hidden'); }
</script>
@endpush