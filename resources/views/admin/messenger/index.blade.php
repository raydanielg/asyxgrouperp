@extends('layouts.admin')
@section('title', 'Messenger - ' . config('app.name'))
@section('page_title', 'Messenger')
@section('content')

<div class="bg-white rounded-md border border-gray-200 h-[calc(100vh-8rem)] flex overflow-hidden">
    {{-- Contacts --}}
    <div class="w-72 border-r border-gray-200 flex flex-col flex-shrink-0">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Contacts</h3>
            <span class="text-[10px] text-gray-400">{{ $users->count() }}</span>
        </div>
        <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
        @foreach($users as $user)
        <div class="contact-item flex items-center gap-3 px-5 py-3 hover:bg-gray-50 cursor-pointer transition-colors" data-id="{{ $user->id }}" onclick="selectUser({{ $user->id }}, '{{ addslashes($user->name) }}', this)">
            <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center text-white font-bold text-[11px] flex-shrink-0">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-900 truncate">{{ $user->name }}</p>
                <p class="text-[10px] text-gray-400 truncate">{{ $user->email }}</p>
            </div>
        </div>
        @endforeach
        </div>
    </div>

    {{-- Chat area --}}
    <div class="flex-1 flex flex-col min-w-0" id="chatArea">
        <div class="flex-1 flex flex-col items-center justify-center text-center" id="chatPlaceholder">
            <svg class="w-10 h-10 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <p class="text-xs text-gray-400">Select a contact to start chatting</p>
        </div>
        <div class="flex-1 flex flex-col hidden min-h-0" id="chatContent">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center text-white font-bold text-[11px]" id="chatAvatar">U</div>
                <div>
                    <h3 class="text-xs font-bold text-gray-900" id="chatUserName">User</h3>
                    <p class="text-[10px] text-gray-400 flex items-center gap-1"><span style="width:5px;height:5px;border-radius:99px;background:#10b981"></span>Online</p>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-5 space-y-3 bg-gray-50/50" id="messagesContainer"></div>
            <div class="px-5 py-3 border-t border-gray-100 flex gap-2">
                <input type="text" id="messageInput" placeholder="Type a message..." class="flex-1 px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400" onkeypress="if(event.key==='Enter')sendMessage()">
                <button onclick="sendMessage()" class="px-4 py-2 bg-slate-900 text-white text-xs font-medium rounded hover:bg-slate-700">Send</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentUserId = null;
function selectUser(id, name, el) {
    currentUserId = id;
    document.querySelectorAll('.contact-item').forEach(c => c.classList.remove('bg-gray-100'));
    el.classList.add('bg-gray-100');
    document.getElementById('chatPlaceholder').classList.add('hidden');
    document.getElementById('chatContent').classList.remove('hidden');
    document.getElementById('chatUserName').textContent = name;
    document.getElementById('chatAvatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('messagesContainer').innerHTML = '<p class="text-[11px] text-gray-400 text-center py-4">No messages yet. Start a conversation!</p>';
    document.getElementById('messageInput').focus();
}
function sendMessage() {
    const input = document.getElementById('messageInput');
    const msg = input.value.trim();
    if (!msg || !currentUserId) return;
    const container = document.getElementById('messagesContainer');
    if (container.querySelector('.text-center')) container.innerHTML = '';
    const div = document.createElement('div');
    div.className = 'flex justify-end';
    div.innerHTML = '<div class="bg-gray-900 text-white text-xs px-3.5 py-2 rounded max-w-xs">' + msg.replace(/</g, '&lt;') + '</div>';
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
    input.value = '';
}
</script>
@endpush
@endsection
