@php
$title = 'Visitors';
$description = 'Register and manage office visitors, check-ins and badges.';
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
        <p class="text-[10px] font-medium text-gray-500 uppercase">Checked In</p>
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
            <input type="text" id="searchInput" placeholder="Search name, phone, company, badge..." class="w-full sm:w-72 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            <select id="statusFilter" class="w-full sm:w-40 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                <option value="">All Status</option>
                <option value="checked_in">Checked In</option>
                <option value="checked_out">Checked Out</option>
            </select>
        </div>
        <button onclick="openCreateModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Check In Visitor
        </button>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Visitor</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Contact / Company</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Purpose / Host</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Badge</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Check In</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Check Out</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="visitorsTableBody" class="divide-y divide-gray-100">
                <tr><td colspan="8" class="px-4 py-8 text-center text-xs text-gray-400">Loading visitors...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="visitorsPagination" class="px-4 py-3 border-t flex items-center justify-between"></div>
</div>

<!-- Modal -->
<div id="visitorModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <div class="bg-emerald-700 px-4 py-3 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-white" id="modalTitle">Check In Visitor</h3>
                </div>
                <form id="visitorForm" onsubmit="saveVisitor(event)" class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Company</label>
                            <input type="text" name="company" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Badge Number</label>
                            <input type="text" name="badge_number" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Purpose</label>
                            <input type="text" name="purpose" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Host / Person to See</label>
                            <input type="text" name="host" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                            <input type="text" name="department" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="sm:col-span-2 flex items-center gap-2 text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Check-in time is recorded automatically as the current time.
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" id="saveVisitorBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Save Visitor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentVisitorId = null;
let currentPage = 1;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function formatDate(dateString) {
    if (!dateString) return '-';
    const d = new Date(dateString);
    return d.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function statusBadge(status) {
    if (status === 'checked_in') {
        return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Checked In</span>';
    }
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">Checked Out</span>';
}

function loadVisitors(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const url = new URL('{{ route('reception.visitors.index') }}', window.location.origin);
    url.searchParams.set('page', page);
    if (search) url.searchParams.set('search', search);
    if (status) url.searchParams.set('status', status);

    document.getElementById('visitorsTableBody').innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-xs text-gray-400">Loading visitors...</td></tr>';

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('visitorsTableBody').innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-xs text-red-500">Failed to load visitors.</td></tr>';
                return;
            }
            renderVisitors(data.visitors);
            renderPagination(data.visitors);
            document.getElementById('statToday').textContent = data.todayCount ?? 0;
            document.getElementById('statWeek').textContent = data.weekCount ?? 0;
            document.getElementById('statPending').textContent = data.pendingCount ?? 0;
            document.getElementById('statTotal').textContent = data.totalCount ?? 0;
        })
        .catch(() => {
            document.getElementById('visitorsTableBody').innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-xs text-red-500">Network error. Please try again.</td></tr>';
        });
}

function renderVisitors(paginator) {
    const tbody = document.getElementById('visitorsTableBody');
    tbody.innerHTML = '';
    if (!paginator.data || paginator.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-xs text-gray-400">No visitors found. Click <strong>Check In Visitor</strong> to add one.</td></tr>';
        return;
    }
    paginator.data.forEach(v => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/50';
        tr.innerHTML = `
            <td class="px-4 py-3">
                <p class="text-xs font-medium text-gray-900">${v.name}</p>
                <p class="text-[10px] text-gray-400">${v.email || '-'}</p>
            </td>
            <td class="px-4 py-3">
                <p class="text-xs text-gray-700">${v.phone || '-'}</p>
                <p class="text-[10px] text-gray-400">${v.company || '-'}</p>
            </td>
            <td class="px-4 py-3">
                <p class="text-xs text-gray-700">${v.purpose || '-'}</p>
                <p class="text-[10px] text-gray-400">${v.host || '-'} ${v.department ? '(' + v.department + ')' : ''}</p>
            </td>
            <td class="px-4 py-3 text-xs text-gray-700">${v.badge_number || '-'}</td>
            <td class="px-4 py-3">${statusBadge(v.status)}</td>
            <td class="px-4 py-3 text-xs text-gray-500">${formatDate(v.check_in_at)}</td>
            <td class="px-4 py-3 text-xs text-gray-500">${formatDate(v.check_out_at)}</td>
            <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1">
                    <button onclick="openEditModal(${v.id})" class="text-sky-500 hover:text-sky-700 p-1 rounded hover:bg-sky-50 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    ${v.status === 'checked_in' ? `
                    <button onclick="checkOutVisitor(${v.id})" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Check Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>` : ''}
                    <button onclick="deleteVisitor(${v.id})" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderPagination(paginator) {
    const container = document.getElementById('visitorsPagination');
    if (paginator.last_page <= 1) {
        container.innerHTML = '<span class="text-xs text-gray-500">Showing ' + paginator.data.length + ' records</span>';
        return;
    }
    let html = '<div class="flex items-center gap-2">';
    html += `<button onclick="loadVisitors(${paginator.current_page - 1})" ${paginator.current_page === 1 ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Previous</button>`;
    html += `<span class="text-xs text-gray-600">Page ${paginator.current_page} of ${paginator.last_page}</span>`;
    html += `<button onclick="loadVisitors(${paginator.current_page + 1})" ${paginator.current_page === paginator.last_page ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === paginator.last_page ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Next</button>`;
    html += '</div>';
    html += '<span class="text-xs text-gray-500">Total: ' + paginator.total + '</span>';
    container.innerHTML = html;
}

function openCreateModal() {
    currentVisitorId = null;
    document.getElementById('modalTitle').textContent = 'Check In Visitor';
    document.getElementById('visitorForm').reset();
    document.getElementById('visitorModal').classList.remove('hidden');
}

function openEditModal(id) {
    const url = '{{ route('reception.visitors.index') }}';
    fetch(url + '?per_page=100', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            const visitor = data.visitors.data.find(v => v.id === id);
            if (!visitor) return;
            currentVisitorId = visitor.id;
            document.getElementById('modalTitle').textContent = 'Edit Visitor';
            const form = document.getElementById('visitorForm');
            form.name.value = visitor.name || '';
            form.phone.value = visitor.phone || '';
            form.email.value = visitor.email || '';
            form.company.value = visitor.company || '';
            form.purpose.value = visitor.purpose || '';
            form.host.value = visitor.host || '';
            form.department.value = visitor.department || '';
            form.badge_number.value = visitor.badge_number || '';
            form.notes.value = visitor.notes || '';
            document.getElementById('visitorModal').classList.remove('hidden');
        });
}

function closeModal() {
    document.getElementById('visitorModal').classList.add('hidden');
    currentVisitorId = null;
    document.getElementById('visitorForm').reset();
}

function saveVisitor(e) {
    e.preventDefault();
    const form = document.getElementById('visitorForm');
    const btn = document.getElementById('saveVisitorBtn');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const url = currentVisitorId ? '{{ route('reception.visitors.update', ['visitor' => '__ID__']) }}'.replace('__ID__', currentVisitorId) : '{{ route('reception.visitors.store') }}';
    const method = currentVisitorId ? 'PUT' : 'POST';

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
        btn.textContent = currentVisitorId ? 'Update Visitor' : 'Save Visitor';
        if (res.success) {
            closeModal();
            Swal.fire({ icon: 'success', title: 'Saved!', text: res.message, timer: 1500, showConfirmButton: false });
            loadVisitors(currentPage);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to save visitor.', confirmButtonColor: '#024938' });
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = currentVisitorId ? 'Update Visitor' : 'Save Visitor';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
}

function deleteVisitor(id) {
    Swal.fire({
        title: 'Delete Visitor?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.visitors.destroy', ['visitor' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false });
                loadVisitors(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to delete.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

function checkOutVisitor(id) {
    Swal.fire({
        title: 'Check Out Visitor?',
        text: 'Record the visitor as checked out now.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#024938',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, check out',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.visitors.checkout', ['visitor' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Checked Out!', text: res.message, timer: 1500, showConfirmButton: false });
                loadVisitors(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to check out.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

document.getElementById('searchInput').addEventListener('input', debounce(() => loadVisitors(1), 300));
document.getElementById('statusFilter').addEventListener('change', () => loadVisitors(1));

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

loadVisitors(1);
</script>
@endpush
@endsection
