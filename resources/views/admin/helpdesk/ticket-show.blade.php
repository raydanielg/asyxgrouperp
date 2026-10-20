@extends('layouts.admin')

@section('title', 'Ticket - ' . config('app.name'))
@section('page_title', 'Ticket Details')

@section('content')
@php
    $prio = ['low' => '#3b82f6', 'medium' => '#f59e0b', 'high' => '#f97316', 'urgent' => '#ef4444'];
    $stat = ['open' => '#10b981', 'in_progress' => '#f59e0b', 'resolved' => '#3b82f6', 'closed' => '#9ca3af'];
@endphp

<div class="max-w-3xl">
    <div class="mb-4"><a href="{{ route('admin.helpdesk-tickets.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Tickets</a></div>

    {{-- Header card --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
        <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[10px] font-mono text-gray-400 uppercase tracking-wide">{{ $ticket->ticket_id }}</p>
                <h2 class="text-base font-bold text-gray-900 mt-1">{{ $ticket->title }}</h2>
                <div class="flex items-center gap-3 mt-2">
                    @if($ticket->category)
                    <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">{{ $ticket->category->name }}</span>
                    @endif
                    <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $prio[$ticket->priority] ?? '#9ca3af' }}"></span>{{ $ticket->priority }}</span>
                    <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $stat[$ticket->status] ?? '#9ca3af' }}"></span>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                </div>
            </div>
            @if(auth()->user()->hasPermission('edit-helpdesk-tickets'))
            <form method="POST" action="{{ route('admin.helpdesk-tickets.status', $ticket) }}" class="flex-shrink-0">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400 bg-white">
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </form>
            @endif
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $ticket->description }}</p>
        </div>
        <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-4 text-[11px] text-gray-400">
                <span>By <span class="text-gray-600">{{ $ticket->creator?->name ?? 'Unknown' }}</span></span>
                <span>{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                <span>Assigned: <span class="{{ $ticket->assignee ? 'text-gray-600' : 'text-gray-300' }}">{{ $ticket->assignee?->name ?? 'Unassigned' }}</span></span>
            </div>
            @if(auth()->user()->hasPermission('edit-helpdesk-tickets'))
            <form method="POST" action="{{ route('admin.helpdesk-tickets.assign', $ticket) }}">
                @csrf @method('PATCH')
                <select name="assigned_to" onchange="this.form.submit()" class="px-3 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400 bg-white">
                    <option value="">Assign to...</option>
                    @foreach($assignees as $user)
                    <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </form>
            @endif
        </div>
    </div>

    {{-- Conversation --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Conversation <span class="text-gray-400 font-normal normal-case">({{ $ticket->replies->count() }})</span></h3></div>
        <div class="divide-y divide-gray-100">
            @forelse($ticket->replies as $reply)
            <div class="px-5 py-4 {{ $reply->is_internal ? 'bg-amber-50/40' : '' }}">
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-900">{{ $reply->creator?->name ?? 'Unknown' }}</span>
                        @if($reply->is_internal)
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-700"><span style="width:5px;height:5px;border-radius:99px;background:#f59e0b"></span>Internal note</span>
                        @endif
                    </div>
                    <span class="text-[10px] text-gray-400">{{ $reply->created_at->format('d M Y, H:i') }}</span>
                </div>
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $reply->message }}</p>
            </div>
            @empty
            <p class="px-5 py-6 text-center text-xs text-gray-400">No replies yet</p>
            @endforelse
        </div>
    </div>

    {{-- Reply box --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Add Reply</h3></div>
        <form method="POST" action="{{ route('admin.helpdesk-replies.store', $ticket) }}" class="p-5">
            @csrf
            <textarea name="message" required rows="3" placeholder="Type your reply..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
            <div class="flex items-center justify-between mt-3">
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="is_internal" class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Internal note (staff only)</label>
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-medium rounded hover:bg-slate-700">Reply</button>
            </div>
        </form>
    </div>
</div>
@endsection
