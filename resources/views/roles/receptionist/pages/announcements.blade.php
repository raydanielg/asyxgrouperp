@php
$title = 'Announcements';
$description = 'Post and view office notices and announcements.';
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
        <p class="text-[10px] font-medium text-gray-500 uppercase">Active</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="statActive">{{ $activeCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">High Priority</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="statHigh">{{ $highPriorityCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Total</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="statTotal">{{ $totalCount ?? 0 }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border p-4 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input type="text" id="searchInput" placeholder="Search title or body..." class="w-full sm:w-72 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            <select id="statusFilter" class="w-full sm:w-40 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <select id="priorityFilter" class="w-full sm:w-40 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                <option value="">All Priority</option>
                <option value="low">Low</option>
                <option value="normal">Normal</option>
                <option value="high">High</option>
            </select>
        </div>
        <button onclick="openCreateModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Post Announcement
        </button>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Title</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Audience</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Priority</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Duration</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="announcementsTableBody" class="divide-y divide-gray-100">
                <tr><td colspan="6" class="px-4 py-8 text-center text-xs text-gray-400">Loading announcements...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="announcementsPagination" class="px-4 py-3 border-t flex items-center justify-between"></div>
</div>

<!-- Modal -->
<div id="announcementModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <div class="bg-emerald-700 px-4 py-3 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-white" id="modalTitle">Post Announcement</h3>
                </div>
                <form id="announcementForm" onsubmit="saveAnnouncement(event)" class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Title <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Body</label>
                            <textarea name="body" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Audience <span class="text-rose-500">*</span></label>
                            <select name="audience" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="all">All</option>
                                <option value="staff">Staff</option>
                                <option value="visitors">Visitors</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Priority <span class="text-rose-500">*</span></label>
                            <select name="priority" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-rose-500">*</span></label>
                            <select name="status" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="datetime-local" name="start_date" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                            <input type="datetime-local" name="end_date" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" id="saveAnnouncementBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Save Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentAnnouncementId = null;
let currentPage = 1;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const d = new Date(dateString);
    return d.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function audienceBadge(audience) {
    if (audience === 'all') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">All</span>';
    if (audience === 'staff') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-200">Staff</span>';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Visitors</span>';
}

function priorityBadge(priority) {
    if (priority === 'high') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">High</span>';
    if (priority === 'normal') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Normal</span>';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-200">Low</span>';
}

function statusBadge(status) {
    if (status === 'active') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-200">Inactive</span>';
}

function durationText(start, end) {
    if (!start && !end) return '-';
    return formatDateTime(start) + ' - ' + (end ? formatDateTime(end) : 'Open');
}

function loadAnnouncements(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const priority = document.getElementById('priorityFilter').value;
    const url = new URL('{{ route('reception.announcements.index') }}', window.location.origin);
    url.searchParams.set('page', page);
    if (search) url.searchParams.set('search', search);
    if (status) url.searchParams.set('status', status);
    if (priority) url.searchParams.set('priority', priority);

    document.getElementById('announcementsTableBody').innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-xs text-gray-400">Loading announcements...</td></tr>';

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('announcementsTableBody').innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-xs text-red-500">Failed to load announcements.</td></tr>';
                return;
            }
            renderAnnouncements(data.announcements);
            renderPagination(data.announcements);
            document.getElementById('statActive').textContent = data.activeCount ?? 0;
            document.getElementById('statHigh').textContent = data.highPriorityCount ?? 0;
            document.getElementById('statTotal').textContent = data.totalCount ?? 0;
        })
        .catch(() => {
            document.getElementById('announcementsTableBody').innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-xs text-red-500">Network error. Please try again.</td></tr>';
        });
}

function renderAnnouncements(paginator) {
    const tbody = document.getElementById('announcementsTableBody');
    tbody.innerHTML = '';
    if (!paginator.data || paginator.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-xs text-gray-400">No announcements found. Click <strong>Post Announcement</strong> to add one.</td></tr>';
        return;
    }
    paginator.data.forEach(a => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/50';
        tr.innerHTML = `
            <td class="px-4 py-3">
                <p class="text-xs font-medium text-gray-900">${a.title}</p>
                <p class="text-[10px] text-gray-400 line-clamp-1">${a.body || '-'}</p>
            </td>
            <td class="px-4 py-3">${audienceBadge(a.audience)}</td>
            <td class="px-4 py-3">${priorityBadge(a.priority)}</td>
            <td class="px-4 py-3">${statusBadge(a.status)}</td>
            <td class="px-4 py-3 text-xs text-gray-500">${durationText(a.start_date, a.end_date)}</td>
            <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1">
                    <button onclick="openEditModal(${a.id})" class="text-sky-500 hover:text-sky-700 p-1 rounded hover:bg-sky-50 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="toggleStatus(${a.id})" class="${a.status === 'active' ? 'text-amber-500 hover:text-amber-700 hover:bg-amber-50' : 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50'} p-1 rounded transition-colors" title="${a.status === 'active' ? 'Deactivate' : 'Activate'}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    <button onclick="deleteAnnouncement(${a.id})" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderPagination(paginator) {
    const container = document.getElementById('announcementsPagination');
    if (paginator.last_page <= 1) {
        container.innerHTML = '<span class="text-xs text-gray-500">Showing ' + paginator.data.length + ' records</span>';
        return;
    }
    let html = '<div class="flex items-center gap-2">';
    html += `<button onclick="loadAnnouncements(${paginator.current_page - 1})" ${paginator.current_page === 1 ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Previous</button>`;
    html += `<span class="text-xs text-gray-600">Page ${paginator.current_page} of ${paginator.last_page}</span>`;
    html += `<button onclick="loadAnnouncements(${paginator.current_page + 1})" ${paginator.current_page === paginator.last_page ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === paginator.last_page ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Next</button>`;
    html += '</div>';
    html += '<span class="text-xs text-gray-500">Total: ' + paginator.total + '</span>';
    container.innerHTML = html;
}

function openCreateModal() {
    currentAnnouncementId = null;
    document.getElementById('modalTitle').textContent = 'Post Announcement';
    document.getElementById('announcementForm').reset();
    document.getElementById('announcementModal').classList.remove('hidden');
}

function openEditModal(id) {
    const url = '{{ route('reception.announcements.index') }}';
    fetch(url + '?per_page=100', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            const a = data.announcements.data.find(item => item.id === id);
            if (!a) return;
            currentAnnouncementId = a.id;
            document.getElementById('modalTitle').textContent = 'Edit Announcement';
            const form = document.getElementById('announcementForm');
            form.title.value = a.title || '';
            form.body.value = a.body || '';
            form.audience.value = a.audience || 'all';
            form.priority.value = a.priority || 'normal';
            form.status.value = a.status || 'active';
            form.start_date.value = a.start_date ? a.start_date.slice(0, 16) : '';
            form.end_date.value = a.end_date ? a.end_date.slice(0, 16) : '';
            document.getElementById('announcementModal').classList.remove('hidden');
        });
}

function closeModal() {
    document.getElementById('announcementModal').classList.add('hidden');
    currentAnnouncementId = null;
    document.getElementById('announcementForm').reset();
}

function saveAnnouncement(e) {
    e.preventDefault();
    const form = document.getElementById('announcementForm');
    const btn = document.getElementById('saveAnnouncementBtn');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const url = currentAnnouncementId ? '{{ route('reception.announcements.update', ['announcement' => '__ID__']) }}'.replace('__ID__', currentAnnouncementId) : '{{ route('reception.announcements.store') }}';
    const method = currentAnnouncementId ? 'PUT' : 'POST';

    btn.disabled = true;
    btn.textContent = 'Saving...';

    fetch(url, {
        method: method,
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
        btn.textContent = currentAnnouncementId ? 'Update Announcement' : 'Save Announcement';
        if (res.success) {
            closeModal();
            Swal.fire({ icon: 'success', title: 'Saved!', text: res.message, timer: 1500, showConfirmButton: false });
            loadAnnouncements(currentPage);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to save announcement.', confirmButtonColor: '#024938' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = currentAnnouncementId ? 'Update Announcement' : 'Save Announcement';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
}

function deleteAnnouncement(id) {
    Swal.fire({
        title: 'Delete Announcement?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.announcements.destroy', ['announcement' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false });
                loadAnnouncements(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to delete.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

function toggleStatus(id) {
    Swal.fire({
        title: 'Toggle Status?',
        text: 'Activate or deactivate this announcement.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#024938',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, toggle',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.announcements.toggle', ['announcement' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Toggled!', text: res.message, timer: 1500, showConfirmButton: false });
                loadAnnouncements(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to toggle.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

document.getElementById('searchInput').addEventListener('input', debounce(() => loadAnnouncements(1), 300));
document.getElementById('statusFilter').addEventListener('change', () => loadAnnouncements(1));
document.getElementById('priorityFilter').addEventListener('change', () => loadAnnouncements(1));

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

loadAnnouncements(1);
</script>
@endpush
@endsection
