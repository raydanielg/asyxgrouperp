@php
$title = 'Appointments';
$description = 'Schedule and track visitor and staff appointments.';
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
        <p class="text-[10px] font-medium text-gray-500 uppercase">Upcoming</p>
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
            <input type="text" id="searchInput" placeholder="Search visitor, phone, company, host..." class="w-full sm:w-72 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            <select id="statusFilter" class="w-full sm:w-40 px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <button onclick="openCreateModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Appointment
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
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date & Time</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Duration</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Time Alert</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="appointmentsTableBody" class="divide-y divide-gray-100">
                <tr><td colspan="8" class="px-4 py-8 text-center text-xs text-gray-400">Loading appointments...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="appointmentsPagination" class="px-4 py-3 border-t flex items-center justify-between"></div>
</div>

<!-- Modal -->
<div id="appointmentModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <div class="bg-emerald-700 px-4 py-3 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-white" id="modalTitle">New Appointment</h3>
                </div>
                <form id="appointmentForm" onsubmit="saveAppointment(event)" class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2 relative">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Visitor Name <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="visitor_name" id="visitorAutocomplete" required autocomplete="off" placeholder="Type to search existing visitors..." class="w-full px-3 py-2 pr-10 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                <svg class="w-4 h-4 text-gray-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Start typing to pick an existing visitor and auto-fill their details.</p>
                            <div id="visitorSuggestions" class="hidden absolute z-20 w-full bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto mt-1"></div>
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
                            <label class="block text-xs font-medium text-gray-700 mb-1">Duration (minutes)</label>
                            <input type="number" name="duration" min="1" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
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
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Appointment Date & Time <span class="text-rose-500">*</span></label>
                            <input type="text" id="appointmentDate" name="appointment_date" required placeholder="Select date and time..." class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" id="saveAppointmentBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Save Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
let currentAppointmentId = null;
let currentPage = 1;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const d = new Date(dateString);
    return d.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function statusBadge(status) {
    if (status === 'scheduled') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-200">Scheduled</span>';
    if (status === 'completed') return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Completed</span>';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">Cancelled</span>';
}

function timeAlert(dateString, status, duration) {
    if (!dateString || status === 'cancelled' || status === 'completed') return '<span class="text-gray-400 text-[10px]">-</span>';
    const now = new Date();
    const appt = new Date(dateString);
    const end = duration ? new Date(appt.getTime() + parseInt(duration) * 60000) : appt;
    if (now >= end) return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200 animate-pulse">Overdue</span>';
    if (now >= appt) return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Time reached</span>';
    const diffMin = (appt - now) / 60000;
    if (diffMin <= 15) return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-200">Starting soon</span>';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-200">Upcoming</span>';
}

function searchVisitors(query) {
    const dropdown = document.getElementById('visitorSuggestions');
    if (!query || query.length < 2) { dropdown.classList.add('hidden'); return; }
    const url = new URL('{{ route('reception.visitors.index') }}', window.location.origin);
    url.searchParams.set('search', query);
    url.searchParams.set('per_page', 10);
    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            dropdown.innerHTML = '';
            if (!data.success || !data.visitors.data.length) {
                dropdown.classList.add('hidden');
                return;
            }
            data.visitors.data.forEach(v => {
                const div = document.createElement('div');
                div.className = 'px-3 py-2 hover:bg-emerald-50 cursor-pointer text-xs text-gray-700 border-b last:border-0';
                div.innerHTML = `<strong>${v.name}</strong><br><span class="text-[10px] text-gray-400">${v.phone || '-'} &middot; ${v.email || '-'} &middot; ${v.company || '-'}</span>`;
                div.onclick = () => selectVisitor(v);
                dropdown.appendChild(div);
            });
            dropdown.classList.remove('hidden');
        });
}

function selectVisitor(v) {
    document.getElementById('visitorAutocomplete').value = v.name;
    const form = document.getElementById('appointmentForm');
    form.phone.value = v.phone || '';
    form.email.value = v.email || '';
    form.company.value = v.company || '';
    form.purpose.value = v.purpose || '';
    document.getElementById('visitorSuggestions').classList.add('hidden');
}

function hideSuggestions() {
    setTimeout(() => document.getElementById('visitorSuggestions').classList.add('hidden'), 200);
}

function loadAppointments(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const url = new URL('{{ route('reception.appointments.index') }}', window.location.origin);
    url.searchParams.set('page', page);
    if (search) url.searchParams.set('search', search);
    if (status) url.searchParams.set('status', status);

    document.getElementById('appointmentsTableBody').innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">Loading appointments...</td></tr>';

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('appointmentsTableBody').innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-red-500">Failed to load appointments.</td></tr>';
                return;
            }
            renderAppointments(data.appointments);
            renderPagination(data.appointments);
            document.getElementById('statToday').textContent = data.todayCount ?? 0;
            document.getElementById('statWeek').textContent = data.weekCount ?? 0;
            document.getElementById('statPending').textContent = data.pendingCount ?? 0;
            document.getElementById('statTotal').textContent = data.totalCount ?? 0;
        })
        .catch(() => {
            document.getElementById('appointmentsTableBody').innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-xs text-red-500">Network error. Please try again.</td></tr>';
        });
}

function renderAppointments(paginator) {
    const tbody = document.getElementById('appointmentsTableBody');
    tbody.innerHTML = '';
    if (!paginator.data || paginator.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-xs text-gray-400">No appointments found. Click <strong>New Appointment</strong> to add one.</td></tr>';
        return;
    }
    paginator.data.forEach(a => {
        const alertHtml = timeAlert(a.appointment_date, a.status, a.duration);
        const isOverdue = a.status === 'scheduled' && alertHtml.includes('Overdue');
        const isReached = a.status === 'scheduled' && alertHtml.includes('Time reached');
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/50 ' + (isOverdue ? 'border-l-4 border-l-rose-400 bg-rose-50/30' : isReached ? 'border-l-4 border-l-amber-400 bg-amber-50/30' : '');
        tr.innerHTML = `
            <td class="px-4 py-3">
                <p class="text-xs font-medium text-gray-900">${a.visitor_name}</p>
                <p class="text-[10px] text-gray-400">${a.email || '-'}</p>
            </td>
            <td class="px-4 py-3">
                <p class="text-xs text-gray-700">${a.phone || '-'}</p>
                <p class="text-[10px] text-gray-400">${a.company || '-'}</p>
            </td>
            <td class="px-4 py-3">
                <p class="text-xs text-gray-700">${a.purpose || '-'}</p>
                <p class="text-[10px] text-gray-400">${a.host || '-'} ${a.department ? '(' + a.department + ')' : ''}</p>
            </td>
            <td class="px-4 py-3 text-xs text-gray-700">${formatDateTime(a.appointment_date)}</td>
            <td class="px-4 py-3 text-xs text-gray-500">${a.duration ? a.duration + ' min' : '-'}</td>
            <td class="px-4 py-3">${statusBadge(a.status)}</td>
            <td class="px-4 py-3">${alertHtml}</td>
            <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1">
                    <button onclick="openEditModal(${a.id})" class="text-sky-500 hover:text-sky-700 p-1 rounded hover:bg-sky-50 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    ${a.status === 'scheduled' ? `
                    <button onclick="completeAppointment(${a.id})" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Complete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                    <button onclick="cancelAppointment(${a.id})" class="text-amber-500 hover:text-amber-700 p-1 rounded hover:bg-amber-50 transition-colors" title="Cancel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>` : ''}
                    <button onclick="deleteAppointment(${a.id})" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderPagination(paginator) {
    const container = document.getElementById('appointmentsPagination');
    if (paginator.last_page <= 1) {
        container.innerHTML = '<span class="text-xs text-gray-500">Showing ' + paginator.data.length + ' records</span>';
        return;
    }
    let html = '<div class="flex items-center gap-2">';
    html += `<button onclick="loadAppointments(${paginator.current_page - 1})" ${paginator.current_page === 1 ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Previous</button>`;
    html += `<span class="text-xs text-gray-600">Page ${paginator.current_page} of ${paginator.last_page}</span>`;
    html += `<button onclick="loadAppointments(${paginator.current_page + 1})" ${paginator.current_page === paginator.last_page ? 'disabled' : ''} class="px-3 py-1 border rounded-lg text-xs font-medium ${paginator.current_page === paginator.last_page ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">Next</button>`;
    html += '</div>';
    html += '<span class="text-xs text-gray-500">Total: ' + paginator.total + '</span>';
    container.innerHTML = html;
}

function openCreateModal() {
    currentAppointmentId = null;
    document.getElementById('modalTitle').textContent = 'New Appointment';
    document.getElementById('appointmentForm').reset();
    document.getElementById('appointmentModal').classList.remove('hidden');
}

function openEditModal(id) {
    const url = '{{ route('reception.appointments.index') }}';
    fetch(url + '?per_page=100', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            const a = data.appointments.data.find(item => item.id === id);
            if (!a) return;
            currentAppointmentId = a.id;
            document.getElementById('modalTitle').textContent = 'Edit Appointment';
            const form = document.getElementById('appointmentForm');
            form.visitor_name.value = a.visitor_name || '';
            form.phone.value = a.phone || '';
            form.email.value = a.email || '';
            form.company.value = a.company || '';
            form.purpose.value = a.purpose || '';
            form.host.value = a.host || '';
            form.department.value = a.department || '';
            form.duration.value = a.duration || '';
            form.notes.value = a.notes || '';
            form.appointment_date.value = a.appointment_date ? a.appointment_date.slice(0, 16) : '';
            document.getElementById('appointmentModal').classList.remove('hidden');
        });
}

function closeModal() {
    document.getElementById('appointmentModal').classList.add('hidden');
    currentAppointmentId = null;
    document.getElementById('appointmentForm').reset();
}

function saveAppointment(e) {
    e.preventDefault();
    const form = document.getElementById('appointmentForm');
    const btn = document.getElementById('saveAppointmentBtn');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const url = currentAppointmentId ? '{{ route('reception.appointments.update', ['appointment' => '__ID__']) }}'.replace('__ID__', currentAppointmentId) : '{{ route('reception.appointments.store') }}';
    const method = currentAppointmentId ? 'PUT' : 'POST';

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
        btn.textContent = currentAppointmentId ? 'Update Appointment' : 'Save Appointment';
        if (res.success) {
            closeModal();
            Swal.fire({ icon: 'success', title: 'Saved!', text: res.message, timer: 1500, showConfirmButton: false });
            loadAppointments(currentPage);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to save appointment.', confirmButtonColor: '#024938' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = currentAppointmentId ? 'Update Appointment' : 'Save Appointment';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
}

function deleteAppointment(id) {
    Swal.fire({
        title: 'Delete Appointment?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.appointments.destroy', ['appointment' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false });
                loadAppointments(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to delete.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

function completeAppointment(id) {
    Swal.fire({
        title: 'Complete Appointment?',
        text: 'Mark this appointment as completed.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#024938',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, complete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.appointments.complete', ['appointment' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Completed!', text: res.message, timer: 1500, showConfirmButton: false });
                loadAppointments(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to complete.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

function cancelAppointment(id) {
    Swal.fire({
        title: 'Cancel Appointment?',
        text: 'Mark this appointment as cancelled.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, cancel',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('reception.appointments.cancel', ['appointment' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Cancelled!', text: res.message, timer: 1500, showConfirmButton: false });
                loadAppointments(currentPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to cancel.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}

document.getElementById('searchInput').addEventListener('input', debounce(() => loadAppointments(1), 300));
document.getElementById('statusFilter').addEventListener('change', () => loadAppointments(1));

const visitorAutocomplete = document.getElementById('visitorAutocomplete');
visitorAutocomplete.addEventListener('input', debounce(() => searchVisitors(visitorAutocomplete.value), 300));
visitorAutocomplete.addEventListener('blur', hideSuggestions);
visitorAutocomplete.addEventListener('focus', () => { if (visitorAutocomplete.value.length >= 2) searchVisitors(visitorAutocomplete.value); });

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

flatpickr('#appointmentDate', { enableTime: true, dateFormat: 'Y-m-d H:i', time_24hr: true, minuteIncrement: 5, allowInput: true });
loadAppointments(1);
</script>
@endpush
@endsection
