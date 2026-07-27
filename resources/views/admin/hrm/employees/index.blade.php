@extends('layouts.admin')
@section('title', 'Employees - ' . config('app.name'))
@section('page_title', 'Employees')
@section('content')

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    {{-- Toolbar --}}
    <div class="px-5 py-4 border-b bg-gray-50/40 flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="empSearch" type="text" placeholder="Search name, email, ID, phone..." class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
            </div>
            <select id="empDeptFilter" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                <option value="{{ $dept }}">{{ $dept }}</option>
                @endforeach
            </select>
            <select id="empStatusFilter" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="on_leave">On Leave</option>
                <option value="terminated">Terminated</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <select id="empPerPage" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="15">15 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
            <button onclick="exportEmployeesPdf()" class="px-3 py-2 bg-violet-50 text-violet-700 border border-violet-200 text-xs font-semibold rounded-lg hover:bg-violet-100 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF
            </button>
            <a href="{{ route('admin.employees.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Employee
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] text-gray-500 uppercase tracking-wider bg-gray-50/50 border-b">
                    <th class="px-5 py-3 font-semibold">Employee ID</th>
                    <th class="px-5 py-3 font-semibold">Name</th>
                    <th class="px-5 py-3 font-semibold">Department</th>
                    <th class="px-5 py-3 font-semibold">Designation</th>
                    <th class="px-5 py-3 font-semibold">Type</th>
                    <th class="px-5 py-3 font-semibold text-right">Salary</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="employeesTableBody">
                <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">Loading employees...</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t bg-gray-50/30 flex items-center justify-between flex-wrap gap-2">
        <div id="employeesInfo" class="text-[10px] text-gray-500 font-medium"></div>
        <div id="employeesPagination"></div>
    </div>
</div>

@push('scripts')
<script>
const empDataUrl = '{{ route("admin.employees.data") }}';
const empShowUrl = '{{ route("admin.employees.show", "__ID__") }}';
const empEditUrl = '{{ route("admin.employees.edit", "__ID__") }}';
const empDestroyUrl = '{{ route("admin.employees.destroy", "__ID__") }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
let empSearchTimer = null;

function loadEmployees(page = 1) {
    const search = document.getElementById('empSearch').value;
    const dept = document.getElementById('empDeptFilter').value;
    const status = document.getElementById('empStatusFilter').value;
    const perPage = document.getElementById('empPerPage').value;

    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (dept) params.set('department', dept);
    if (status) params.set('status', status);
    params.set('per_page', perPage);
    params.set('page', page);

    fetch(empDataUrl + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => renderEmployees(res))
    .catch(() => {
        document.getElementById('employeesTableBody').innerHTML = '<tr><td colspan="8" class="px-5 py-12 text-center text-sm text-red-400">Failed to load employees.</td></tr>';
    });
}

function renderEmployees(res) {
    const tbody = document.getElementById('employeesTableBody');
    const employees = res.data || [];

    if (employees.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-12 text-center"><svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg><p class="text-sm text-gray-400">No employees found</p></td></tr>';
    } else {
        tbody.innerHTML = employees.map(e => {
            const statusColors = {active: 'emerald', inactive: 'gray', on_leave: 'amber', terminated: 'red'};
            const sc = statusColors[e.status] || 'gray';
            const initials = (e.first_name || '').charAt(0).toUpperCase();
            const fullName = (e.first_name || '') + ' ' + (e.last_name || '');
            let actions = '';
            actions += '<a href="' + empShowUrl.replace('__ID__', e.id) + '" title="View" class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>';
            actions += '<a href="' + empEditUrl.replace('__ID__', e.id) + '" title="Edit" class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>';
            actions += '<button onclick="deleteEmployee(' + e.id + ',\'' + escapeQuotes(e.employee_id || '') + '\',\'' + escapeQuotes(fullName) + '\')" title="Delete" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>';

            return '<tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">' +
                '<td class="px-5 py-3 text-xs font-mono text-gray-700 font-medium">' + escapeHtml(e.employee_id || '') + '</td>' +
                '<td class="px-5 py-3"><div class="flex items-center gap-2"><div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-[10px]">' + escapeHtml(initials) + '</div><div><p class="text-xs font-medium text-gray-900">' + escapeHtml(fullName) + '</p><p class="text-[10px] text-gray-400">' + escapeHtml(e.email || '') + '</p></div></div></td>' +
                '<td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700">' + escapeHtml(e.department || 'N/A') + '</span></td>' +
                '<td class="px-5 py-3 text-xs text-gray-500">' + escapeHtml(e.designation || 'N/A') + '</td>' +
                '<td class="px-5 py-3 text-xs text-gray-500">' + escapeHtml(e.employment_type || '—') + '</td>' +
                '<td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TZS ' + numberFormat(e.salary || 0) + '</td>' +
                '<td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-' + sc + '-50 text-' + sc + '-700 border border-' + sc + '-100">' + capitalize((e.status || '').replace(/_/g, ' ')) + '</span></td>' +
                '<td class="px-5 py-3 text-right"><div class="flex items-center justify-end gap-1">' + actions + '</div></td>' +
                '</tr>';
        }).join('');
    }

    document.getElementById('employeesInfo').textContent = res.total ? ('Showing ' + (res.from || 0) + '–' + (res.to || 0) + ' of ' + res.total) : '';
    document.getElementById('employeesPagination').innerHTML = res.links || '';
}

function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function escapeQuotes(s) { return s.replace(/'/g, "\\'"); }
function numberFormat(n) { return Number(n).toLocaleString('en-US'); }
function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

function exportEmployeesPdf() {
    const search = document.getElementById('empSearch').value;
    const dept = document.getElementById('empDeptFilter').value;
    const status = document.getElementById('empStatusFilter').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (dept) params.set('department', dept);
    if (status) params.set('status', status);
    params.set('per_page', 'all');
    fetch(empDataUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            const employees = res.data || [];
            if (employees.length === 0) { Swal.fire('No Data', 'No employees to export.', 'info'); return; }
            const w = window.open('', '_blank');
            w.document.write('<html><head><title>Employees Export</title><style>body{font-family:Arial,sans-serif;padding:20px}table{width:100%;border-collapse:collapse;font-size:11px}th,td{border:1px solid #ddd;padding:6px 8px;text-align:left}th{background:#f5f5f5;font-weight:bold}.right{text-align:right}</style></head><body>');
            w.document.write('<h2>Employees Report</h2><p>Generated: ' + new Date().toLocaleString() + '</p>');
            w.document.write('<table><thead><tr><th>Emp ID</th><th>Name</th><th>Email</th><th>Department</th><th>Designation</th><th>Type</th><th>Salary</th><th>Status</th></tr></thead><tbody>');
            employees.forEach(e => {
                w.document.write('<tr><td>' + (e.employee_id||'') + '</td><td>' + escapeHtml((e.first_name||'')+' '+(e.last_name||'')) + '</td><td>' + escapeHtml(e.email||'') + '</td><td>' + escapeHtml(e.department||'N/A') + '</td><td>' + escapeHtml(e.designation||'N/A') + '</td><td>' + escapeHtml(e.employment_type||'—') + '</td><td class="right">TZS ' + numberFormat(e.salary||0) + '</td><td>' + capitalize((e.status||'').replace(/_/g,' ')) + '</td></tr>');
            });
            w.document.write('</tbody></table></body></html>');
            w.document.close();
            w.print();
        });
}

function deleteEmployee(id, empId, name) {
    Swal.fire({
        title: 'Delete Employee?', text: empId + ' (' + name + ') will be permanently removed.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel', reverseButtons: true
    }).then((r) => {
        if (r.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = empDestroyUrl.replace('__ID__', id);
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

// Event listeners
document.getElementById('empSearch').addEventListener('input', function() {
    clearTimeout(empSearchTimer);
    empSearchTimer = setTimeout(() => loadEmployees(1), 300);
});
document.getElementById('empDeptFilter').addEventListener('change', () => loadEmployees(1));
document.getElementById('empStatusFilter').addEventListener('change', () => loadEmployees(1));
document.getElementById('empPerPage').addEventListener('change', () => loadEmployees(1));
document.getElementById('employeesPagination').addEventListener('click', function(e) {
    e.preventDefault();
    const link = e.target.closest('a');
    if (!link) return;
    const url = new URL(link.href);
    const page = url.searchParams.get('page') || 1;
    loadEmployees(page);
});

// Initial load
loadEmployees(1);
</script>
@endpush
@endsection
