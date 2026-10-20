@extends('layouts.admin')
@section('title', 'My Notifications')
@section('page_title', 'Notifications')

@section('content')
<div class="max-w-3xl">
<div class="bg-white rounded-md border border-gray-200 divide-y divide-gray-100">
    @forelse($notifications as $n)
    <div class="px-5 py-3.5 flex items-start gap-3 {{ $n->read_at ? 'opacity-60' : '' }}">
        <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $n->read_at ? '#d1d5db' : '#3b82f6' }}"></span>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900">{{ $n->title }}</p>
            @if($n->body)<p class="text-xs text-gray-500 mt-0.5">{{ $n->body }}</p>@endif
            <p class="text-[10px] text-gray-400 mt-1">{{ $n->created_at->format('d M Y H:i') }} · {{ str_replace('_',' ',$n->type) }}</p>
        </div>
        @unless($n->read_at)
        <form method="POST" action="{{ route('admin.my-notifications.read', $n) }}">@csrf
            <button class="text-[10px] font-medium text-gray-500 hover:text-gray-800 whitespace-nowrap">Mark read</button>
        </form>
        @endunless
    </div>
    @empty
    <div class="px-5 py-10 text-center text-xs text-gray-400">No notifications yet.</div>
    @endforelse
</div>
<div class="mt-3">{{ $notifications->links() }}</div>
</div>
@endsection
