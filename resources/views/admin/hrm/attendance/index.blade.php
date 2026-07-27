@extends('layouts.admin')
@section('title', 'Attendance - ' . config('app.name'))
@section('page_title', 'Attendance Management')
@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- ═══ Clock In / Clock Out Panel ═══ --}}
<div class="bg-white rounded-xl border p-5 mb-4">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-bold text-gray-800">Clock In / Clock Out</h3>
            <p class="text-xs text-gray-400 mt-0.5">Select employee and clock them in or out</p>
        </div>
        <div class="text-right">
            <p class="text-lg font-bold text-emerald-600" id="liveClock">{{ now()->format('H:i:s') }}</p>
            <p class="text-[10px] text-gray-400">{{ now()->format('d M Y, l') }}</p>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-3 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-medium text-gray-600 mb-1">Employee</label>
            <select id="clockEmployee" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                <option value="">— Select Employee —</option>
                @foreach($employees as $e)
                <option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>
                @endforeach
            </select>
        </div>
        <form method="POST" action="{{ route('admin.attendance.clock-in') }}">@csrf<input type="hidden" name="employee_id" id="clockInEmpId"><button type="submit" onclick="return setEmpId('clockInEmpId')" class="w-full md:w-auto px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            Clock In
        </button></form>
        <form method="POST" action="{{ route('admin.attendance.clock-out') }}">@csrf<input type="hidden" name="employee_id" id="clockOutEmpId"><button type="submit" onclick="return setEmpId('clockOutEmpId')" class="w-full md:w-auto px-5 py-2.5 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            Clock Out
        </button></form>
        <form method="POST" action="{{ route('admin.attendance.clock-out-all') }}">@csrf<button type="submit" onclick="return confirm('Clock out ALL employees still clocked in?')" class="w-full md:w-auto px-5 py-2.5 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Clock Out All
        </button></form>
    </div>
</div>

{{-- ═══ Attendance Table ═══ --}}
<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    {{-- Toolbar --}}
    <div class="px-5 py-4 border-b bg-gray-50/40 flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="attSearch" type="text" placeholder="Search employee..." class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
            </div>
            <input id="attDateFilter" type="date" value="{{ request('date', date('Y-m-d')) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
            <select id="attEmpFilter" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="">All Employees</option>
                @foreach($employees as $e)
                <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                @endforeach
            </select>
            <select id="attStatusFilter" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="">All Status</option>
                <option value="present">Present</option>
                <option value="late">Late</option>
                <option value="absent">Absent</option>
                <option value="remote">Remote</option>
                <option value="half_day">Half Day</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <select id="attPerPage" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="15">15 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
            <button onclick="exportAttendancePdf()" class="px-3 py-2 bg-violet-50 text-violet-700 border border-violet-200 text-xs font-semibold rounded-lg hover:bg-violet-100 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF
            </button>
            <button onclick="document.getElementById('manualModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Record
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] text-gray-500 uppercase tracking-wider bg-gray-50/50 border-b">
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
            <tbody id="attTableBody">
                <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">Loading attendance...</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t bg-gray-50/30 flex items-center justify-between flex-wrap gap-2">
        <div id="attInfo" class="text-[10px] text-gray-500 font-medium"></div>
        <div id="attPagination"></div>
    </div>
</div>

{{-- ═══ Manual Entry Modal ═══ --}}
<div id="manualModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Manual Attendance Record</h3>
        <form method="POST" action="{{ route('admin.attendance.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Employee *</label><select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Date *</label><input name="date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Check In</label><input name="check_in" type="time" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Check Out</label><input name="check_out" type="time" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status *</label><select name="status" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="present">Present</option><option value="absent">Absent</option><option value="late">Late</option><option value="half_day">Half Day</option><option value="remote">Remote</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Note</label><textarea name="note" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('manualModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save</button></div>
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

function loadAttendance(page = 1) {
    const search = document.getElementById('attSearch').value;
    const date = document.getElementById('attDateFilter').value;
    const empId = document.getElementById('attEmpFilter').value;
    const status = document.getElementById('attStatusFilter').value;
    const perPage = document.getElementById('attPerPage').value;

    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (date) params.set('date', date);
    if (empId) params.set('employee_id', empId);
    if (status) params.set('status', status);
    params.set('per_page', perPage);
    params.set('page', page);

    fetch(attDataUrl + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
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
        tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-12 text-center"><svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p class="text-sm text-gray-400">No attendance records for this date</p></td></tr>';
    } else {
        tbody.innerHTML = records.map(a => {
            const emp = a.employee || {};
            const initials = (emp.first_name || '?').charAt(0).toUpperCase();
            const fullName = (emp.first_name || '') + ' ' + (emp.last_name || '');
            const dateStr = a.date ? new Date(a.date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';

            let clockIn = '—';
            if (a.clock_in_at) {
                const dt = new Date(a.clock_in_at);
                clockIn = '<span class="text-emerald-600 font-medium">' + dt.toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit',second:'2-digit'}) + '</span>';
            } else if (a.check_in) {
                clockIn = '<span class="text-gray-400">' + escapeHtml(a.check_in) + '</span>';
            }

            let clockOut = '—';
            if (a.clock_out_at) {
                const dt = new Date(a.clock_out_at);
                clockOut = '<span class="text-red-500 font-medium">' + dt.toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit',second:'2-digit'}) + '</span>';
            } else if (a.clock_in_at) {
                clockOut = '<span class="text-amber-500 text-[10px] italic">Still working...</span>';
            } else if (a.check_out) {
                clockOut = '<span class="text-gray-400">' + escapeHtml(a.check_out) + '</span>';
            }

            const statusColors = {present: 'emerald', absent: 'red', late: 'amber', half_day: 'sky', remote: 'purple'};
            const sc = statusColors[a.status] || 'gray';
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
                actions += '<form method="POST" action="' + attClockOutUrl + '" class="inline"><input type="hidden" name="_token" value="' + csrfToken + '"><input type="hidden" name="employee_id" value="' + a.employee_id + '"><button title="Clock Out" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg></button></form>';
            }
            actions += '<button onclick="deleteAttendance(' + a.id + ')" title="Delete" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>';

            return '<tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">' +
                '<td class="px-5 py-3"><div class="flex items-center gap-2"><div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">' + escapeHtml(initials) + '</div><div><p class="text-xs font-medium text-gray-900">' + escapeHtml(fullName) + '</p><p class="text-[10px] text-gray-400">' + escapeHtml(emp.employee_id || '') + '</p></div></div></td>' +
                '<td class="px-5 py-3 text-xs text-gray-500">' + dateStr + '</td>' +
                '<td class="px-5 py-3 text-xs">' + clockIn + '</td>' +
                '<td class="px-5 py-3 text-xs">' + clockOut + '</td>' +
                '<td class="px-5 py-3 text-xs text-right font-medium text-gray-700">' + workHrs + '</td>' +
                '<td class="px-5 py-3 text-xs text-right ' + (a.overtime_hours > 0 ? 'text-amber-600 font-medium' : 'text-gray-300') + '">' + otHrs + '</td>' +
                '<td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-' + sc + '-50 text-' + sc + '-700 border border-' + sc + '-100">' + statusLabel + '</span></td>' +
                '<td class="px-5 py-3 text-right"><div class="flex items-center justify-end gap-1">' + actions + '</div></td>' +
                '</tr>';
        }).join('');
    }

    document.getElementById('attInfo').textContent = res.total ? ('Showing ' + (res.from || 0) + '–' + (res.to || 0) + ' of ' + res.total) : '';
    document.getElementById('attPagination').innerHTML = res.links || '';
}

function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

function exportAttendancePdf() {
    const search = document.getElementById('attSearch').value;
    const date = document.getElementById('attDateFilter').value;
    const empId = document.getElementById('attEmpFilter').value;
    const status = document.getElementById('attStatusFilter').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (date) params.set('date', date);
    if (empId) params.set('employee_id', empId);
    if (status) params.set('status', status);
    params.set('per_page', 'all');
    fetch(attDataUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            const records = res.data || [];
            if (records.length === 0) { Swal.fire('No Data', 'No attendance records to export.', 'info'); return; }
            const w = window.open('', '_blank');
            w.document.write('<html><head><title>Attendance Export</title><style>body{font-family:Arial,sans-serif;padding:20px}table{width:100%;border-collapse:collapse;font-size:11px}th,td{border:1px solid #ddd;padding:6px 8px;text-align:left}th{background:#f5f5f5;font-weight:bold}.right{text-align:right}</style></head><body>');
            w.document.write('<h2>Attendance Report — ' + (date || 'Today') + '</h2><p>Generated: ' + new Date().toLocaleString() + '</p>');
            w.document.write('<table><thead><tr><th>Employee</th><th>Emp ID</th><th>Date</th><th>Clock In</th><th>Clock Out</th><th>Work Hrs</th><th>OT</th><th>Status</th></tr></thead><tbody>');
            records.forEach(a => {
                const emp = a.employee || {};
                const dt = a.date ? new Date(a.date).toLocaleDateString() : '—';
                const ci = a.clock_in_at ? new Date(a.clock_in_at).toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit'}) : (a.check_in || '—');
                const co = a.clock_out_at ? new Date(a.clock_out_at).toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit'}) : (a.check_out || '—');
                const wh = a.work_hours > 0 ? a.work_hours + 'h' : '—';
                const oh = a.overtime_hours > 0 ? a.overtime_hours + 'h' : '—';
                w.document.write('<tr><td>' + escapeHtml((emp.first_name||'')+' '+(emp.last_name||'')) + '</td><td>' + escapeHtml(emp.employee_id||'') + '</td><td>' + dt + '</td><td>' + ci + '</td><td>' + co + '</td><td class="right">' + wh + '</td><td class="right">' + oh + '</td><td>' + capitalize((a.status||'').replace(/_/g,' ')) + '</td></tr>');
            });
            w.document.write('</tbody></table></body></html>');
            w.document.close();
            w.print();
        });
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
    if (!empId) { alert('Please select an employee first.'); return false; }
    document.getElementById(hiddenId).value = empId;
    return true;
}

// Event listeners
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
    const url = new URL(link.href);
    const page = url.searchParams.get('page') || 1;
    loadAttendance(page);
});

// Live clock
setInterval(function() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('liveClock');
    if (el) el.textContent = h + ':' + m + ':' + s;
}, 1000);

// Initial load
loadAttendance(1);
</script>
@endpush
@endsection
