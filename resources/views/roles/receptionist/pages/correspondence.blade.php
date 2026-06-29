@php
$title = 'Correspondence';
$description = 'Track incoming and outgoing letters, memos and couriers.';
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

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Today</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="statToday">{{ $todayCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">This Week</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="statWeek">{{ $weekCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Pending</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="statPending">{{ $pendingCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Total</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="statTotal">{{ $totalCount ?? 0 }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border p-4 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input type="text" id="searchInput" placeholder="Search ref, sender, recipient, subject..." class="w-full sm:w-72 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            <select id="statusFilter" class="w-full sm:w-40 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                <option value="">All Status</option>
                <option value="received">Received</option>
                <option value="sent">Sent</option>
                <option value="pending">Pending</option>
                <option value="delivered">Delivered</option>
            </select>
            <select id="typeFilter" class="w-full sm:w-36 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                <option value="">All Types</option>
                <option value="incoming">Incoming</option>
                <option value="outgoing">Outgoing</option>
                <option value="internal">Internal</option>
            </select>
        </div>
        <button onclick="openCreateModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record Item
        </button>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Reference</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Sender / Recipient</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Subject</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="correspondenceTableBody" class="divide-y divide-gray-100">
                <tr><td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">Loading correspondence...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="correspondencePagination" class="px-4 py-3 border-t flex items-center justify-between"></div>
</div>

<!-- Modal -->
<div id="correspondenceModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <div class="bg-emerald-700 px-4 py-3 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-white" id="modalTitle">Record Correspondence</h3>
                </div>
                <form id="correspondenceForm" onsubmit="saveCorrespondence(event)" class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Reference Number</label>
                            <input type="text" name="reference_number" placeholder="Auto-generated if empty" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Type <span class="text-rose-500">*</span></label>
                            <select name="type" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="incoming">Incoming</option>
                                <option value="outgoing">Outgoing</option>
                                <option value="internal">Internal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Sender Name</label>
                            <input type="text" name="sender_name" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Recipient Name</label>
                            <input type="text" name="recipient_name" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" name="subject" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-rose-500">*</span></label>
                            <select name="status" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="received">Received</option>
                                <option value="sent">Sent</option>
                                <option value="pending">Pending</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                            <input type="datetime-local" name="date" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" id="saveCorrespondenceBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentItemId = null;
let currentPage = 1;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const d = new Date(dateString);
    return d.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function typeBadge(type) {
    if (type === 'incoming') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Incoming</span>';
    if (type === 'outgoing') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-200">Outgoing</span>';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Internal</span>';
}

function statusBadge(status) {
    if (status === 'received') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Received</span>';
    if (status === 'sent') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-200">Sent</span>';
    if (status === 'delivered') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-200">Delivered</span>';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Pending</span>';
}

function loadCorrespondence(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    const url = new URL('{{ route('reception.correspondence.index') }}', window.location.origin);
    url.searchParams.set('page', page);
    if (search) url.searchParams.set('search', search);
    if (status) url.searchParams.set('status', status);
    if (type) url.searchParams.set('type', type);

    document.getElementById('correspondenceTableBody').innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">Loading correspondence...</td></tr>';

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('correspondenceTableBody').innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-red-500">Failed to load correspondence.</td></tr>';
                return;
            }
            renderCorrespondence(data.correspondence);
            renderPagination(data.correspondence);
            document.getElementById('statToday').textContent = data.todayCount ?? 0;
            document.getElementById('statWeek').textContent = data.weekCount ?? 0;
            document.getElementById('statPending').textContent = data.pendingCount ?? 0;
            document.getElementById('statTotal').textContent = data.totalCount ?? 0;
        })
        .catch(() => {
            document.getElementById('correspondenceTableBody').innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-red-500">Network error. Please try again.</td></tr>';
        });
}

function renderCorrespondence(paginator) {
    const tbody = document.getElementById('correspondenceTableBody');
    tbody.innerHTML = '';
    if (!paginator.data || paginator.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">No correspondence found. Click <strong>Record Item</strong> to add one.</td></tr>';
        return;
    }
    paginator.data.forEach(c => {
        const dateField = c.type === 'outgoing' ? c.dispatched_date : c.received_date;
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/50';
        tr.innerHTML = `
            <td class="px-4 py-3 text-xs font-medium text-gray-900">${c.reference_number || '-'}</td>
            <td class="px-4 py-3">${typeBadge(c.type)}</td>
            <td class="px-4 py-3">
                <p class="text-xs text-gray-700">From: ${c.sender_name || '-'}</p>
                <p class="text-[10px] text-gray-400">To: ${c.recipient_name || '-'}</p>
            </td>
            <td class="px-4 py-3 text-xs text-gray-700">${c.subject || '-'}</td>
            <td class="px-4 py-3">${statusBadge(c.status)}</td>
            <td class="px-4 py-3 text-xs text-gray-500">${formatDateTime(dateField)}</td>
            <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1">
                    <button onclick="openEditModal(${c.id})" class="text-sky-500 hover:text-sky-700 p-1 rounded hover:bg-sky-50 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="changeStatus(${c.id})" class="text-amber-500 hover:text-amber-700 p-1 rounded hover:bg-amber-50 transition-colors" title="Change Status">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    <button onclick="deleteItem(${c.id})" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderPagination(paginator) {
    const container = document.getElementById('correspondencePagination');
    if (paginator.last_page <= 1) {
        container.innerHTML = '<span class="text-xs text-gray-500">Showing ' + paginator.data.length + ' records</span>';
        return;
    }
    let html = '<div class="flex items-center gap-2">';
    html += `<button onclick="loadCorrespondence(${paginator.current_page - 1})" ${paginator.current_page === 1 ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Previous</button>`;
    html += `<span class="text-xs text-gray-600">Page ${paginator.current_page} of ${paginator.last_page}</span>`;
    html += `<button onclick="loadCorrespondence(${paginator.current_page + 1})" ${paginator.current_page === paginator.last_page ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === paginator.last_page ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Next</button>`;
    html += '</div>';
    html += '<span class="text-xs text-gray-500">Total: ' + paginator.total + '</span>';
    container.innerHTML = html;
}

function openCreateModal() {
    currentItemId = null;
    document.getElementById('modalTitle').textContent = 'Record Correspondence';
    document.getElementById('correspondenceForm').reset();
    document.getElementById('correspondenceModal').classList.remove('hidden');
}

function openEditModal(id) {
    const url = '{{ route('reception.correspondence.index') }}';
    fetch(url + '?per_page=100', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            const c = data.correspondence.data.find(item => item.id === id);
            if (!c) return;
            currentItemId = c.id;
            document.getElementById('modalTitle').textContent = 'Edit Correspondence';
            const form = document.getElementById('correspondenceForm');
            form.reference_number.value = c.reference_number || '';
            form.type.value = c.type || 'incoming';
            form.sender_name.value = c.sender_name || '';
            form.recipient_name.value = c.recipient_name || '';
            form.subject.value = c.subject || '';
            form.description.value = c.description || '';
            form.status.value = c.status || 'received';
            form.notes.value = c.notes || '';
            const dateField = c.type === 'outgoing' ? c.dispatched_date : c.received_date;
            form.date.value = dateField ? dateField.slice(0, 16) : '';
            document.getElementById('correspondenceModal').classList.remove('hidden');
        });
}

function closeModal() {
    document.getElementById('correspondenceModal').classList.add('hidden');
    currentItemId = null;
    document.getElementById('correspondenceForm').reset();
}

function saveCorrespondence(e) {
    e.preventDefault();
    const form = document.getElementById('correspondenceForm');
    const btn = document.getElementById('saveCorrespondenceBtn');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const type = data.type;
    if (type === 'outgoing') {
        data.dispatched_date = data.date;
        data.received_date = '';
    } else {
        data.received_date = data.date;
        data.dispatched_date = '';
    }
    delete data.date;

    const url = currentItemId ? '{{ route('reception.correspondence.update', ['correspondence' => '__ID__']) }}'.replace('__ID__', currentItemId) : '{{ route('reception.correspondence.store') }}';
    const method = currentItemId ? 'PUT' : 'POST';

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
        btn.textContent = currentItemId ? 'Update Item' : 'Save Item';
        if (res.success) {
            closeModal();
            Swal.fire({ icon: 'success', title: 'Saved!', text: res.message, timer: 1500, showConfirmButton: false });
            loadCorrespondence(currentPage);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to save item.', confirmButtonColor: '#024938' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = currentItemId ? 'Update Item' : 'Save Item';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
}

function deleteItem(id) {
    Swal.fire({
        title: 'Delete Item?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.correspondence.destroy', ['correspondence' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false });
                loadCorrespondence(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to delete.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

function changeStatus(id) {
    Swal.fire({
        title: 'Change Status',
        input: 'select',
        inputOptions: { received: 'Received', sent: 'Sent', pending: 'Pending', delivered: 'Delivered' },
        inputPlaceholder: 'Select new status',
        showCancelButton: true,
        confirmButtonColor: '#024938',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed || !result.value) return;
        const url = '{{ route('reception.correspondence.status', ['correspondence' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ status: result.value })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Updated!', text: res.message, timer: 1500, showConfirmButton: false });
                loadCorrespondence(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to update.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

document.getElementById('searchInput').addEventListener('input', debounce(() => loadCorrespondence(1), 300));
document.getElementById('statusFilter').addEventListener('change', () => loadCorrespondence(1));
document.getElementById('typeFilter').addEventListener('change', () => loadCorrespondence(1));

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

loadCorrespondence(1);
</script>
@endpush
@endsection
