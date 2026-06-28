@extends('layouts.admin')

@section('title', 'Ticket - ' . config('app.name'))
@section('page_title', 'Ticket Details')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border p-5 mb-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <p class="text-xs font-mono text-emerald-700 mb-1">{{ $ticket->ticket_id }}</p>
                <h2 class="text-lg font-bold text-gray-900">{{ $ticket->title }}</h2>
            </div>
            <form method="POST" action="{{ route('admin.helpdesk-tickets.status', $ticket) }}" class="flex items-center gap-2">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none">
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </form>
        </div>
        <p class="text-sm text-gray-600">{{ $ticket->description }}</p>
        <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
            <span>By: {{ $ticket->creator?->name ?? 'Unknown' }}</span>
            <span>{{ $ticket->created_at->format('d M Y H:i') }}</span>
        </div>
    </div>

    <div class="space-y-3 mb-4">
        @foreach($ticket->replies as $reply)
        <div class="bg-white rounded-xl border p-4 {{ $reply->is_internal ? 'border-amber-200 bg-amber-50/30' : '' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-900">{{ $reply->creator?->name ?? 'Unknown' }}</span>
                <span class="text-xs text-gray-400">{{ $reply->created_at->format('d M Y H:i') }}</span>
            </div>
            <p class="text-sm text-gray-600">{{ $reply->message }}</p>
        @if($reply->is_internal)
        <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Internal</span>
        @endif
        </div>
        @endforeach
        </div>

    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Add Reply</h3>
        <form method="POST" action="{{ route('admin.helpdesk-replies.store', $ticket) }}" class="space-y-3">
            @csrf
            <div><textarea name="message" required rows="3" placeholder="Type your reply..." class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2"><input type="checkbox" name="is_internal" class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Internal note</span></label>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Reply</button>
            </div>
        </form>
    </div>
</div>
@endsection
