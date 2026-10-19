@extends('layouts.admin')
@section('title', 'Employees - ' . config('app.name'))
@section('page_title', 'Employees')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    {{-- Toolbar --}}
    <div class="px-5 py-3.5 border-b border-gray-200 flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <input id="empSearch" type="text" placeholder="Search name, email, ID, phone..." class="flex-1 px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <select id="empDeptFilter" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                <option value="{{ $dept }}">{{ $dept }}</option>
                @endforeach
            </select>
            <select id="empStatusFilter" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="on_leave">On Leave</option>
                <option value="terminated">Terminated</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <select id="empPerPage" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="15">15 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
            <button onclick="exportEmployeesPdf()" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50 transition-colors">Export PDF</button>
            <a href="{{ route('admin.employees.create') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Employee</a>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
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
    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/40 flex items-center justify-between flex-wrap gap-2">
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
            const statusColors = {active: '#10b981', inactive: '#9ca3af', on_leave: '#f59e0b', terminated: '#ef4444'};
            const sc = statusColors[e.status] || '#9ca3af';
            const initials = (e.first_name || '').charAt(0).toUpperCase();
            const fullName = (e.first_name || '') + ' ' + (e.last_name || '');
            let actions = '';
            actions += '<a href="' + empShowUrl.replace('__ID__', e.id) + '" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>';
            actions += '<a href="' + empEditUrl.replace('__ID__', e.id) + '" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</a>';
            actions += '<button onclick="deleteEmployee(' + e.id + ',\'' + escapeQuotes(e.employee_id || '') + '\',\'' + escapeQuotes(fullName) + '\')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>';

            return '<tr class="hover:bg-gray-50/60">' +
                '<td class="px-5 py-3 text-xs font-mono text-gray-700 font-medium">' + escapeHtml(e.employee_id || '') + '</td>' +
                '<td class="px-5 py-3"><div class="flex items-center gap-2"><div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-[10px]">' + escapeHtml(initials) + '</div><div><p class="text-xs font-medium text-gray-900">' + escapeHtml(fullName) + '</p><p class="text-[10px] text-gray-400">' + escapeHtml(e.email || '') + '</p></div></div></td>' +
                '<td class="px-5 py-3"><span class="text-xs text-gray-600">' + escapeHtml(e.department || '—') + '</span></td>' +
                '<td class="px-5 py-3 text-xs text-gray-500">' + escapeHtml(e.designation || 'N/A') + '</td>' +
                '<td class="px-5 py-3 text-xs text-gray-500">' + escapeHtml(e.employment_type || '—') + '</td>' +
                '<td class="px-5 py-3 text-xs font-medium text-gray-900 text-right">' + numberFormat(e.salary || 0) + ' <span class="text-gray-400 font-normal">TZS</span></td>' +
                '<td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:' + sc + '"></span>' + capitalize((e.status || '').replace(/_/g, ' ')) + '</span></td>' +
                '<td class="px-5 py-3 text-right"><div class="flex items-center justify-end gap-4">' + actions + '</div></td>' +
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
