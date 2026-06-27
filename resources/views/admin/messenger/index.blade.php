@extends('layouts.admin')
@section('title', 'Messenger - ' . config('app.name'))
@section('page_title', 'Messenger')
@section('content')
<div class="bg-white rounded-xl border h-[calc(100vh-8rem)] flex overflow-hidden">
    {{-- User List --}}
    <div class="w-64 border-r flex flex-col">
        <div class="p-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Contacts</h3></div>
        <div class="flex-1 overflow-y-auto">
            @foreach($users as $user)
            <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50" onclick="selectUser({{ $user->id }}, '{{ addslashes($user->name) }}')">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                <div class="flex-1 min-w-0"><p class="text-xs font-medium text-gray-900 truncate">{{ $user->name }}</p><p class="text-xs text-gray-400 truncate">{{ $user->email }}</p></div>
            </div>
            @endforeach
        </div>
    </div>
    {{-- Chat Area --}}
    <div class="flex-1 flex flex-col" id="chatArea">
        <div class="flex-1 flex items-center justify-center text-gray-400 text-sm" id="chatPlaceholder">Select a contact to start chatting</div>
        <div class="flex-1 flex flex-col hidden" id="chatContent">
            <div class="p-4 border-b flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-xs" id="chatAvatar">U</div>
                <h3 class="text-sm font-semibold text-gray-900" id="chatUserName">User</h3>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="messagesContainer"></div>
            <div class="p-4 border-t flex gap-2">
                <input type="text" id="messageInput" placeholder="Type a message..." class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" onkeypress="if(event.key==='Enter')sendMessage()">
                <button onclick="sendMessage()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Send</button>
            </div>
        </div>
    </div>
</div>
<script>
let currentUserId = null;
function selectUser(id, name) {
    currentUserId = id;
    document.getElementById('chatPlaceholder').classList.add('hidden');
    document.getElementById('chatContent').classList.remove('hidden');
    document.getElementById('chatUserName').textContent = name;
    document.getElementById('chatAvatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('messagesContainer').innerHTML = '<p class="text-xs text-gray-400 text-center py-4">No messages yet. Start a conversation!</p>';
}
function sendMessage() {
    const input = document.getElementById('messageInput');
    const msg = input.value.trim();
    if (!msg || !currentUserId) return;
    const container = document.getElementById('messagesContainer');
    if (container.querySelector('.text-center')) container.innerHTML = '';
    const div = document.createElement('div');
    div.className = 'flex justify-end';
    div.innerHTML = '<div class="bg-emerald-600 text-white text-sm px-4 py-2 rounded-lg max-w-xs">' + msg.replace(/</g, '&lt;') + '</div>';
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
    input.value = '';
}
</script>
@endsection
