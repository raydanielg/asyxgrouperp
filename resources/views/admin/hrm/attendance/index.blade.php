@extends('layouts.admin')
@section('title', 'Attendance - ' . config('app.name'))
@section('page_title', 'Attendance Management')
@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif

{{-- Clock Panel --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Clock In / Out</h4>
        <div class="text-right">
            <p class="text-sm font-bold text-gray-900 tabular-nums" id="liveClock">{{ now()->format('H:i:s') }}</p>
            <p class="text-[10px] text-gray-400">{{ now()->format('d M Y, l') }}</p>
        </div>
    </div>
    <div class="p-5 flex flex-col md:flex-row gap-3 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-medium text-gray-600 mb-1">Employee</label>
            <select id="clockEmployee" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                <option value="">— Select employee —</option>
                @foreach($employees as $e)
                <option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>
                @endforeach
            </select>
        </div>
        <form method="POST" action="{{ route('admin.attendance.clock-in') }}">@csrf<input type="hidden" name="employee_id" id="clockInEmpId"><button type="submit" onclick="return setEmpId('clockInEmpId')" class="w-full md:w-auto px-5 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Clock In</button></form>
        <form method="POST" action="{{ route('admin.attendance.clock-out') }}">@csrf<input type="hidden" name="employee_id" id="clockOutEmpId"><button type="submit" onclick="return setEmpId('clockOutEmpId')" class="w-full md:w-auto px-5 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50 transition-colors">Clock Out</button></form>
        <form method="POST" action="{{ route('admin.attendance.clock-out-all') }}" onsubmit="return confirmAction(this, {title:'Clock Out All?', text:'All employees still clocked in will be clocked out now.', icon:'warning', confirmText:'Yes, clock out all', color:'#d97706'})">@csrf<button type="submit" class="w-full md:w-auto px-5 py-2 border border-gray-200 text-gray-500 text-xs font-medium rounded hover:bg-gray-50 transition-colors">Clock Out All</button></form>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex flex-wrap items-center gap-2">
        <input id="attSearch" type="text" placeholder="Search employee..." class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white w-48">
        <input id="attDateFilter" type="date" value="{{ request('date', date('Y-m-d')) }}" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
        <select id="attEmpFilter" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Employees</option>
            @foreach($employees as $e)
            <option value="{{ $e->id }}">{{ $e->full_name }}</option>
            @endforeach
        </select>
        <select id="attStatusFilter" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Status</option>
            <option value="present">Present</option>
            <option value="late">Late</option>
            <option value="absent">Absent</option>
            <option value="remote">Remote</option>
            <option value="half_day">Half Day</option>
        </select>
        <div class="ml-auto flex items-center gap-2">
            <select id="attPerPage" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="15">15 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
            <button onclick="exportAttendancePdf()" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50 transition-colors">Export</button>
            <button type="button" onclick="openAttDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Record</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Employee</th>
                    <th class="px-5 py-3 font-semibold">Date</th>
                    <th class="px-5 py-3 font-semibold">Clock In</th>
                    <th class="px-5 py-3 font-semibold">Clock Out</th>
                    <th class="px-5 py-3 font-semibold text-right">Work Hrs</th>
                    <th class="px-5 py-3 font-semibold text-right">OT</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="attTableBody" class="divide-y divide-gray-100">
                <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">Loading attendance...</td></tr>
            </tbody>
        </table>
    </div>

    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
        <div id="attInfo" class="text-xs text-gray-400"></div>
        <div id="attPagination"></div>
    </div>
</div>

{{-- ═══ Add Record Drawer (slides from left) ═══ --}}
<div id="attDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeAttDrawer()"></div>
    <div id="attDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">Add Attendance Record</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Manual entry</p>
            </div>
            <button type="button" onclick="closeAttDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.attendance.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select...</option>
                        @foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date <span class="text-red-500">*</span></label>
                    <input name="date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Check In</label><input name="check_in" type="time" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Check Out</label><input name="check_out" type="time" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="present">Present</option><option value="absent">Absent</option><option value="late">Late</option><option value="half_day">Half Day</option><option value="remote">Remote</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Note</label>
                    <textarea name="note" rows="2" placeholder="Optional note..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save Record</button>
                <button type="button" onclick="closeAttDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const attDataUrl = '{{ route("admin.attendance.data") }}';
const attDestroyUrl = '{{ route("admin.attendance.destroy", "__ID__") }}';
const attClockOutUrl = '{{ route("admin.attendance.clock-out") }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
let attSearchTimer = null;

function openAttDrawer() {
    const d = document.getElementById('attDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('attDrawerPanel').classList.remove('-translate-x-full'));
}
function closeAttDrawer() {
    const p = document.getElementById('attDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('attDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAttDrawer(); });

function loadAttendance(page = 1) {
    const params = new URLSearchParams();
    const search = document.getElementById('attSearch').value;
    const date = document.getElementById('attDateFilter').value;
    const empId = document.getElementById('attEmpFilter').value;
    const status = document.getElementById('attStatusFilter').value;
    params.set('per_page', document.getElementById('attPerPage').value);
    params.set('page', page);
    if (search) params.set('search', search);
    if (date) params.set('date', date);
    if (empId) params.set('employee_id', empId);
    if (status) params.set('status', status);

    fetch(attDataUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(res => renderAttendance(res))
    .catch(() => {
        document.getElementById('attTableBody').innerHTML = '<tr><td colspan="8" class="px-5 py-12 text-center text-sm text-red-400">Failed to load attendance.</td></tr>';
    });
}

function renderAttendance(res) {
    const tbody = document.getElementById('attTableBody');
    const records = res.data || [];

    if (records.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-10 text-center text-sm text-gray-400">No attendance records found</td></tr>';
    } else {
        tbody.innerHTML = records.map(a => {
            const emp = a.employee || {};
            const initials = (emp.first_name || '?').charAt(0).toUpperCase();
            const fullName = (emp.first_name || '') + ' ' + (emp.last_name || '');
            const dateStr = a.date ? new Date(a.date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';

            let clockIn = '—';
            if (a.clock_in_at) {
                clockIn = '<span class="text-gray-900 font-medium">' + new Date(a.clock_in_at).toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit',second:'2-digit'}) + '</span>';
            } else if (a.check_in) {
                clockIn = '<span class="text-gray-500">' + escapeHtml(a.check_in) + '</span>';
            }

            let clockOut = '—';
            if (a.clock_out_at) {
                clockOut = '<span class="text-gray-900 font-medium">' + new Date(a.clock_out_at).toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit',second:'2-digit'}) + '</span>';
            } else if (a.clock_in_at) {
                clockOut = '<span class="text-amber-600 text-[11px]">Still working…</span>';
            } else if (a.check_out) {
                clockOut = '<span class="text-gray-500">' + escapeHtml(a.check_out) + '</span>';
            }

            const statusColors = {present: '#10b981', absent: '#ef4444', late: '#f59e0b', half_day: '#3b82f6', remote: '#8b5cf6'};
            const sc = statusColors[a.status] || '#9ca3af';
            const statusLabel = capitalize((a.status || '').replace(/_/g, ' '));

            let workHrs = '00:00';
            if (a.work_hours > 0) {
                const h = Math.floor(a.work_hours);
                const m = Math.round((a.work_hours - h) * 60);
                workHrs = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
            }
            let otHrs = '00:00';
            if (a.overtime_hours > 0) {
                const oh = Math.floor(a.overtime_hours);
                const om = Math.round((a.overtime_hours - oh) * 60);
                otHrs = String(oh).padStart(2,'0') + ':' + String(om).padStart(2,'0');
            }

            let actions = '';
            if (a.clock_in_at && !a.clock_out_at) {
                actions += '<form method="POST" action="' + attClockOutUrl + '" class="inline"><input type="hidden" name="_token" value="' + csrfToken + '"><input type="hidden" name="employee_id" value="' + a.employee_id + '"><button class="text-xs font-medium text-amber-600 hover:text-amber-800">Clock Out</button></form>';
            }
            actions += '<button onclick="deleteAttendance(' + a.id + ')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>';

            return '<tr class="hover:bg-gray-50/60">' +
                '<td class="px-5 py-3.5"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-[10px]">' + escapeHtml(initials) + '</div><div><p class="text-xs font-medium text-gray-900">' + escapeHtml(fullName) + '</p><p class="text-[10px] font-mono text-gray-400">' + escapeHtml(emp.employee_id || '') + '</p></div></div></td>' +
                '<td class="px-5 py-3.5 text-xs text-gray-500">' + dateStr + '</td>' +
                '<td class="px-5 py-3.5 text-xs">' + clockIn + '</td>' +
                '<td class="px-5 py-3.5 text-xs">' + clockOut + '</td>' +
                '<td class="px-5 py-3.5 text-xs text-right font-medium text-gray-700">' + workHrs + '</td>' +
                '<td class="px-5 py-3.5 text-xs text-right ' + (a.overtime_hours > 0 ? 'text-amber-600 font-medium' : 'text-gray-300') + '">' + otHrs + '</td>' +
                '<td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:' + sc + '"></span>' + statusLabel + '</span></td>' +
                '<td class="px-5 py-3.5 text-right"><div class="flex items-center justify-end gap-4">' + actions + '</div></td>' +
                '</tr>';
        }).join('');
    }

    document.getElementById('attInfo').textContent = res.total ? ('Showing ' + (res.from || 0) + '–' + (res.to || 0) + ' of ' + res.total) : '';
    document.getElementById('attPagination').innerHTML = res.links || '';
}

function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

function exportAttendancePdf() {
    const params = new URLSearchParams();
    const search = document.getElementById('attSearch').value;
    const date = document.getElementById('attDateFilter').value;
    const empId = document.getElementById('attEmpFilter').value;
    const status = document.getElementById('attStatusFilter').value;
    if (search) params.set('search', search);
    if (date) params.set('date', date);
    if (empId) params.set('employee_id', empId);
    if (status) params.set('status', status);
    window.open('{{ route("admin.attendance.export") }}?' + params.toString(), '_blank');
}

function deleteAttendance(id) {
    Swal.fire({
        title: 'Delete Record?', text: 'This attendance record will be permanently removed.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel', reverseButtons: true
    }).then((r) => {
        if (r.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = attDestroyUrl.replace('__ID__', id);
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = csrfToken;
            const method = document.createElement('input');
            method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
            form.appendChild(csrf); form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function setEmpId(hiddenId) {
    const empId = document.getElementById('clockEmployee').value;
    if (!empId) { Swal.fire({ icon: 'warning', title: 'Select Employee', text: 'Please select an employee first.', confirmButtonColor: '#0f172a' }); return false; }
    document.getElementById(hiddenId).value = empId;
    return true;
}

document.getElementById('attSearch').addEventListener('input', function() {
    clearTimeout(attSearchTimer);
    attSearchTimer = setTimeout(() => loadAttendance(1), 300);
});
document.getElementById('attDateFilter').addEventListener('change', () => loadAttendance(1));
document.getElementById('attEmpFilter').addEventListener('change', () => loadAttendance(1));
document.getElementById('attStatusFilter').addEventListener('change', () => loadAttendance(1));
document.getElementById('attPerPage').addEventListener('change', () => loadAttendance(1));
document.getElementById('attPagination').addEventListener('click', function(e) {
    e.preventDefault();
    const link = e.target.closest('a');
    if (!link) return;
    const page = new URL(link.href).searchParams.get('page') || 1;
    loadAttendance(page);
});

setInterval(function() {
    const now = new Date();
    const el = document.getElementById('liveClock');
    if (el) el.textContent = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');
}, 1000);

loadAttendance(1);
</script>
@endpush
@endsection
