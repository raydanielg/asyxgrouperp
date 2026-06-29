@php
$title = 'Parcels';
$description = 'Record received and dispatched parcels and packages.';
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
            <input type="text" id="searchInput" placeholder="Search tracking, sender, recipient, courier..." class="w-full sm:w-72 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            <select id="statusFilter" class="w-full sm:w-44 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                <option value="">All Status</option>
                <option value="received">Received</option>
                <option value="out_for_delivery">Out for Delivery</option>
                <option value="delivered">Delivered</option>
                <option value="returned">Returned</option>
            </select>
        </div>
        <button onclick="openCreateModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record Parcel
        </button>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Tracking</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Sender / Recipient</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Courier</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Received</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Delivered</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="parcelsTableBody" class="divide-y divide-gray-100">
                <tr><td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">Loading parcels...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="parcelsPagination" class="px-4 py-3 border-t flex items-center justify-between"></div>
</div>

<!-- Modal -->
<div id="parcelModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <div class="bg-emerald-700 px-4 py-3 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-white" id="modalTitle">Record Parcel</h3>
                </div>
                <form id="parcelForm" onsubmit="saveParcel(event)" class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tracking Number</label>
                            <input type="text" name="tracking_number" placeholder="Auto-generated if empty" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Courier</label>
                            <input type="text" name="courier" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Sender Name</label>
                            <input type="text" name="sender_name" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Recipient Name</label>
                            <input type="text" name="recipient_name" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-rose-500">*</span></label>
                            <select name="status" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="received">Received</option>
                                <option value="out_for_delivery">Out for Delivery</option>
                                <option value="delivered">Delivered</option>
                                <option value="returned">Returned</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Received Date</label>
                            <input type="datetime-local" name="received_date" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" id="saveParcelBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Save Parcel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentParcelId = null;
let currentPage = 1;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const d = new Date(dateString);
    return d.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function statusBadge(status) {
    if (status === 'received') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Received</span>';
    if (status === 'out_for_delivery') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-200">Out for Delivery</span>';
    if (status === 'delivered') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Delivered</span>';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">Returned</span>';
}

function loadParcels(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const url = new URL('{{ route('reception.parcels.index') }}', window.location.origin);
    url.searchParams.set('page', page);
    if (search) url.searchParams.set('search', search);
    if (status) url.searchParams.set('status', status);

    document.getElementById('parcelsTableBody').innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">Loading parcels...</td></tr>';

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('parcelsTableBody').innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-red-500">Failed to load parcels.</td></tr>';
                return;
            }
            renderParcels(data.parcels);
            renderPagination(data.parcels);
            document.getElementById('statToday').textContent = data.todayCount ?? 0;
            document.getElementById('statWeek').textContent = data.weekCount ?? 0;
            document.getElementById('statPending').textContent = data.pendingCount ?? 0;
            document.getElementById('statTotal').textContent = data.totalCount ?? 0;
        })
        .catch(() => {
            document.getElementById('parcelsTableBody').innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-red-500">Network error. Please try again.</td></tr>';
        });
}

function renderParcels(paginator) {
    const tbody = document.getElementById('parcelsTableBody');
    tbody.innerHTML = '';
    if (!paginator.data || paginator.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">No parcels found. Click <strong>Record Parcel</strong> to add one.</td></tr>';
        return;
    }
    paginator.data.forEach(p => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/50';
        tr.innerHTML = `
            <td class="px-4 py-3 text-xs font-medium text-gray-900">${p.tracking_number || '-'}</td>
            <td class="px-4 py-3">
                <p class="text-xs text-gray-700">From: ${p.sender_name || '-'}</p>
                <p class="text-[10px] text-gray-400">To: ${p.recipient_name || '-'}</p>
            </td>
            <td class="px-4 py-3 text-xs text-gray-700">${p.courier || '-'}</td>
            <td class="px-4 py-3">${statusBadge(p.status)}</td>
            <td class="px-4 py-3 text-xs text-gray-500">${formatDateTime(p.received_date)}</td>
            <td class="px-4 py-3 text-xs text-gray-500">${formatDateTime(p.delivered_date)}</td>
            <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1">
                    <button onclick="openEditModal(${p.id})" class="text-sky-500 hover:text-sky-700 p-1 rounded hover:bg-sky-50 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    ${p.status !== 'delivered' ? `
                    <button onclick="markDelivered(${p.id})" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Mark Delivered">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>` : ''}
                    <button onclick="deleteParcel(${p.id})" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderPagination(paginator) {
    const container = document.getElementById('parcelsPagination');
    if (paginator.last_page <= 1) {
        container.innerHTML = '<span class="text-xs text-gray-500">Showing ' + paginator.data.length + ' records</span>';
        return;
    }
    let html = '<div class="flex items-center gap-2">';
    html += `<button onclick="loadParcels(${paginator.current_page - 1})" ${paginator.current_page === 1 ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Previous</button>`;
    html += `<span class="text-xs text-gray-600">Page ${paginator.current_page} of ${paginator.last_page}</span>`;
    html += `<button onclick="loadParcels(${paginator.current_page + 1})" ${paginator.current_page === paginator.last_page ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === paginator.last_page ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Next</button>`;
    html += '</div>';
    html += '<span class="text-xs text-gray-500">Total: ' + paginator.total + '</span>';
    container.innerHTML = html;
}

function openCreateModal() {
    currentParcelId = null;
    document.getElementById('modalTitle').textContent = 'Record Parcel';
    document.getElementById('parcelForm').reset();
    document.getElementById('parcelModal').classList.remove('hidden');
}

function openEditModal(id) {
    const url = '{{ route('reception.parcels.index') }}';
    fetch(url + '?per_page=100', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            const p = data.parcels.data.find(item => item.id === id);
            if (!p) return;
            currentParcelId = p.id;
            document.getElementById('modalTitle').textContent = 'Edit Parcel';
            const form = document.getElementById('parcelForm');
            form.tracking_number.value = p.tracking_number || '';
            form.courier.value = p.courier || '';
            form.sender_name.value = p.sender_name || '';
            form.recipient_name.value = p.recipient_name || '';
            form.status.value = p.status || 'received';
            form.notes.value = p.notes || '';
            form.received_date.value = p.received_date ? p.received_date.slice(0, 16) : '';
            document.getElementById('parcelModal').classList.remove('hidden');
        });
}

function closeModal() {
    document.getElementById('parcelModal').classList.add('hidden');
    currentParcelId = null;
    document.getElementById('parcelForm').reset();
}

function saveParcel(e) {
    e.preventDefault();
    const form = document.getElementById('parcelForm');
    const btn = document.getElementById('saveParcelBtn');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const url = currentParcelId ? '{{ route('reception.parcels.update', ['parcel' => '__ID__']) }}'.replace('__ID__', currentParcelId) : '{{ route('reception.parcels.store') }}';
    const method = currentParcelId ? 'PUT' : 'POST';

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
        btn.textContent = currentParcelId ? 'Update Parcel' : 'Save Parcel';
        if (res.success) {
            closeModal();
            Swal.fire({ icon: 'success', title: 'Saved!', text: res.message, timer: 1500, showConfirmButton: false });
            loadParcels(currentPage);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to save parcel.', confirmButtonColor: '#024938' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = currentParcelId ? 'Update Parcel' : 'Save Parcel';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
}

function deleteParcel(id) {
    Swal.fire({
        title: 'Delete Parcel?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.parcels.destroy', ['parcel' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false });
                loadParcels(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to delete.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

function markDelivered(id) {
    Swal.fire({
        title: 'Mark Delivered?',
        text: 'Record this parcel as delivered now.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#024938',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, deliver',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.parcels.deliver', ['parcel' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Delivered!', text: res.message, timer: 1500, showConfirmButton: false });
                loadParcels(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to deliver.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

document.getElementById('searchInput').addEventListener('input', debounce(() => loadParcels(1), 300));
document.getElementById('statusFilter').addEventListener('change', () => loadParcels(1));

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

loadParcels(1);
</script>
@endpush
@endsection
