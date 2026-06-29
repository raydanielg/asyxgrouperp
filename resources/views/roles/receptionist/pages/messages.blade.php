@php
$title = 'Messages';
$description = 'Send and receive internal messages and notifications.';
@endphp
@extends('layouts.admin')
@section('title', $title)
@section('page_title', $title)
@section('content')
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10">
        <h2 class="text-2xl font-bold">{{ $title }}</h2>
        <p class="text-emerald-100 text-sm mt-1">{{ $description }}</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Unread</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="statUnread">{{ $unreadCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Inbox</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="statInbox">{{ $inboxCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Sent</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="statSent">{{ $sentCount ?? 0 }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border p-4 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-lg">
            <button onclick="switchTab('inbox')" id="tabInbox" class="px-4 py-1.5 text-xs font-medium rounded-md text-gray-700 hover:bg-white hover:shadow-sm transition-all">Inbox</button>
            <button onclick="switchTab('sent')" id="tabSent" class="px-4 py-1.5 text-xs font-medium rounded-md text-gray-700 hover:bg-white hover:shadow-sm transition-all">Sent</button>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input type="text" id="searchInput" placeholder="Search messages..." class="w-full sm:w-64 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            <button onclick="openComposeModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Compose
            </button>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div id="inboxSection" class="">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">From</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Subject</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Priority</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="inboxTableBody" class="divide-y divide-gray-100">
                    <tr><td colspan="6" class="px-4 py-8 text-center text-xs text-gray-400">Loading inbox...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="inboxPagination" class="px-4 py-3 border-t flex items-center justify-between"></div>
    </div>

    <div id="sentSection" class="hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">To</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Subject</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Priority</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="sentTableBody" class="divide-y divide-gray-100">
                    <tr><td colspan="5" class="px-4 py-8 text-center text-xs text-gray-400">Loading sent...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="sentPagination" class="px-4 py-3 border-t flex items-center justify-between"></div>
    </div>
</div>

<!-- Compose Modal -->
<div id="composeModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeComposeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <div class="bg-emerald-700 px-4 py-3 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-white">Compose Message</h3>
                </div>
                <form id="composeForm" onsubmit="sendMessage(event)" class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Recipient <span class="text-rose-500">*</span></label>
                            <select name="recipient_id" id="recipientSelect" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="">Select recipient</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="low">Low</option>
                                <option value="normal" selected>Normal</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" name="subject" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Message</label>
                            <textarea name="body" rows="4" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeComposeModal()" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" id="sendMessageBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeViewModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <div class="bg-emerald-700 px-4 py-3 sm:px-6 flex items-center justify-between">
                    <h3 class="text-base font-semibold leading-6 text-white">Message</h3>
                    <button onclick="closeViewModal()" class="text-emerald-100 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs text-gray-500" id="viewMeta">From: - | To: -</p>
                            <p class="text-sm text-gray-400 mt-0.5" id="viewDate">-</p>
                        </div>
                        <span id="viewPriority" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-200">Normal</span>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2" id="viewSubject">-</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap" id="viewBody">-</div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeViewModal()" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentTab = 'inbox';
let currentInboxPage = 1;
let currentSentPage = 1;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const users = @json($users ?? []);

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const d = new Date(dateString);
    return d.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function priorityBadge(priority) {
    if (priority === 'high') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">High</span>';
    if (priority === 'normal') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Normal</span>';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-200">Low</span>';
}

function loadMessages(page = 1) {
    if (currentTab === 'inbox') currentInboxPage = page;
    else currentSentPage = page;

    const search = document.getElementById('searchInput').value;
    const url = new URL('{{ route('reception.messages.index') }}', window.location.origin);
    url.searchParams.set('tab', currentTab);
    if (search) url.searchParams.set('search', search);
    if (currentTab === 'inbox') url.searchParams.set('inbox_page', page);
    else url.searchParams.set('sent_page', page);

    const tableBody = currentTab === 'inbox' ? document.getElementById('inboxTableBody') : document.getElementById('sentTableBody');
    tableBody.innerHTML = `<tr><td colspan="${currentTab === 'inbox' ? 6 : 5}" class="px-4 py-8 text-center text-xs text-gray-400">Loading ${currentTab}...</td></tr>`;

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            document.getElementById('statUnread').textContent = data.unreadCount ?? 0;
            document.getElementById('statInbox').textContent = data.inboxCount ?? 0;
            document.getElementById('statSent').textContent = data.sentCount ?? 0;
            renderMessages(data);
        })
        .catch(() => {
            tableBody.innerHTML = `<tr><td colspan="${currentTab === 'inbox' ? 6 : 5}" class="px-4 py-8 text-center text-xs text-red-500">Network error. Please try again.</td></tr>`;
        });
}

function renderMessages(data) {
    if (currentTab === 'inbox') {
        renderInbox(data.inbox);
    } else {
        renderSent(data.sent);
    }
}

function renderInbox(paginator) {
    const tbody = document.getElementById('inboxTableBody');
    tbody.innerHTML = '';
    if (!paginator.data || paginator.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-xs text-gray-400">No messages in inbox.</td></tr>';
    } else {
        paginator.data.forEach(m => {
            const tr = document.createElement('tr');
            tr.className = m.status === 'unread' ? 'bg-emerald-50/30 hover:bg-emerald-50/50' : 'hover:bg-gray-50/50';
            tr.innerHTML = `
                <td class="px-4 py-3">${statusIndicator(m.status)}</td>
                <td class="px-4 py-3 text-xs font-medium ${m.status === 'unread' ? 'text-gray-900' : 'text-gray-600'}">${m.sender?.name || '-'}</td>
                <td class="px-4 py-3 text-xs ${m.status === 'unread' ? 'font-semibold text-gray-900' : 'text-gray-600'}">${m.subject || '(No subject)'}</td>
                <td class="px-4 py-3">${priorityBadge(m.priority)}</td>
                <td class="px-4 py-3 text-xs text-gray-500">${formatDateTime(m.sent_at)}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="viewMessage(${m.id})" class="text-sky-500 hover:text-sky-700 p-1 rounded hover:bg-sky-50 transition-colors" title="View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button onclick="toggleRead(${m.id}, '${m.status === 'unread' ? 'read' : 'unread'}')" class="text-amber-500 hover:text-amber-700 p-1 rounded hover:bg-amber-50 transition-colors" title="${m.status === 'unread' ? 'Mark Read' : 'Mark Unread'}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                        <button onclick="deleteMessage(${m.id})" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
    renderPagination(paginator, 'inboxPagination', 'currentInboxPage');
}

function renderSent(paginator) {
    const tbody = document.getElementById('sentTableBody');
    tbody.innerHTML = '';
    if (!paginator.data || paginator.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-xs text-gray-400">No sent messages.</td></tr>';
    } else {
        paginator.data.forEach(m => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50';
            tr.innerHTML = `
                <td class="px-4 py-3 text-xs text-gray-700">${m.recipient?.name || '-'}</td>
                <td class="px-4 py-3 text-xs text-gray-700">${m.subject || '(No subject)'}</td>
                <td class="px-4 py-3">${priorityBadge(m.priority)}</td>
                <td class="px-4 py-3 text-xs text-gray-500">${formatDateTime(m.sent_at)}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="viewMessage(${m.id})" class="text-sky-500 hover:text-sky-700 p-1 rounded hover:bg-sky-50 transition-colors" title="View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button onclick="deleteMessage(${m.id})" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
    renderPagination(paginator, 'sentPagination', 'currentSentPage');
}

function statusIndicator(status) {
    if (status === 'unread') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Unread</span>';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-200">Read</span>';
}

function renderPagination(paginator, containerId, pageVar) {
    const container = document.getElementById(containerId);
    if (paginator.last_page <= 1) {
        container.innerHTML = '<span class="text-xs text-gray-500">Showing ' + paginator.data.length + ' records</span>';
        return;
    }
    let html = '<div class="flex items-center gap-2">';
    html += `<button onclick="goToPage(${paginator.current_page - 1})" ${paginator.current_page === 1 ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Previous</button>`;
    html += `<span class="text-xs text-gray-600">Page ${paginator.current_page} of ${paginator.last_page}</span>`;
    html += `<button onclick="goToPage(${paginator.current_page + 1})" ${paginator.current_page === paginator.last_page ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === paginator.last_page ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Next</button>`;
    html += '</div>';
    html += '<span class="text-xs text-gray-500">Total: ' + paginator.total + '</span>';
    container.innerHTML = html;
}

function goToPage(page) {
    loadMessages(page);
}

function switchTab(tab) {
    currentTab = tab;
    document.getElementById('inboxSection').classList.toggle('hidden', tab !== 'inbox');
    document.getElementById('sentSection').classList.toggle('hidden', tab !== 'sent');
    document.getElementById('tabInbox').className = tab === 'inbox' ? 'px-4 py-1.5 text-xs font-medium rounded-md bg-white text-emerald-700 shadow-sm' : 'px-4 py-1.5 text-xs font-medium rounded-md text-gray-700 hover:bg-white hover:shadow-sm transition-all';
    document.getElementById('tabSent').className = tab === 'sent' ? 'px-4 py-1.5 text-xs font-medium rounded-md bg-white text-emerald-700 shadow-sm' : 'px-4 py-1.5 text-xs font-medium rounded-md text-gray-700 hover:bg-white hover:shadow-sm transition-all';
    loadMessages(1);
}

function openComposeModal() {
    const select = document.getElementById('recipientSelect');
    select.innerHTML = '<option value="">Select recipient</option>';
    users.forEach(u => {
        const option = document.createElement('option');
        option.value = u.id;
        option.textContent = u.name;
        select.appendChild(option);
    });
    document.getElementById('composeForm').reset();
    document.getElementById('composeModal').classList.remove('hidden');
}

function closeComposeModal() {
    document.getElementById('composeModal').classList.add('hidden');
    document.getElementById('composeForm').reset();
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

function sendMessage(e) {
    e.preventDefault();
    const form = document.getElementById('composeForm');
    const btn = document.getElementById('sendMessageBtn');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    btn.disabled = true;
    btn.textContent = 'Sending...';

    fetch('{{ route('reception.messages.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.textContent = 'Send';
        if (res.success) {
            closeComposeModal();
            Swal.fire({ icon: 'success', title: 'Sent!', text: res.message, timer: 1500, showConfirmButton: false });
            switchTab('sent');
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to send message.', confirmButtonColor: '#024938' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Send';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
}

function viewMessage(id) {
    const url = '{{ route('reception.messages.show', ['message' => '__ID__']) }}'.replace('__ID__', id);
    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const m = res.item;
            document.getElementById('viewSubject').textContent = m.subject || '(No subject)';
            document.getElementById('viewBody').textContent = m.body || '-';
            document.getElementById('viewDate').textContent = formatDateTime(m.sent_at);
            document.getElementById('viewMeta').textContent = `From: ${m.sender?.name || '-'} | To: ${m.recipient?.name || '-'}`;
            document.getElementById('viewPriority').textContent = (m.priority || 'normal').charAt(0).toUpperCase() + (m.priority || 'normal').slice(1);
            document.getElementById('viewPriority').className = m.priority === 'high' ? 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200' : m.priority === 'normal' ? 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200' : 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-200';
            document.getElementById('viewModal').classList.remove('hidden');
            loadMessages(currentTab === 'inbox' ? currentInboxPage : currentSentPage);
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load message.', confirmButtonColor: '#024938' }));
}

function toggleRead(id, status) {
    const url = '{{ route('reception.messages.status', ['message' => '__ID__']) }}'.replace('__ID__', id);
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ status: status })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            loadMessages(currentInboxPage);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to update.', confirmButtonColor: '#024938' });
        }
    })
    .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
}

function deleteMessage(id) {
    Swal.fire({
        title: 'Delete Message?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.messages.destroy', ['message' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false });
                loadMessages(currentTab === 'inbox' ? currentInboxPage : currentSentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to delete.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

document.getElementById('searchInput').addEventListener('input', debounce(() => loadMessages(1), 300));

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

switchTab('inbox');
</script>
@endpush
@endsection
