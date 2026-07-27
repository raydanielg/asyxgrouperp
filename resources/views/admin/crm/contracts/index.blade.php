@extends('layouts.admin')
@section('title', 'CRM Contracts - ' . config('app.name'))
@section('page_title', 'Contracts')
@section('content')

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    {{-- Toolbar --}}
    <div class="px-5 py-4 border-b bg-gray-50/40 flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="contractSearch" type="text" placeholder="Search contracts..." class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
            </div>
            <select id="contractStatusFilter" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="terminated">Terminated</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <select id="contractPerPage" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="15">15 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
            <button onclick="exportContractsPdf()" class="px-3 py-2 bg-violet-50 text-violet-700 border border-violet-200 text-xs font-semibold rounded-lg hover:bg-violet-100 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF
            </button>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Contract
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] text-gray-500 uppercase tracking-wider bg-gray-50/50 border-b">
                    <th class="px-5 py-3 font-semibold">Contract #</th>
                    <th class="px-5 py-3 font-semibold">Title</th>
                    <th class="px-5 py-3 font-semibold">Client</th>
                    <th class="px-5 py-3 font-semibold text-right">Value</th>
                    <th class="px-5 py-3 font-semibold">Period</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="contractsTableBody">
                <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">Loading contracts...</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t bg-gray-50/30 flex items-center justify-between flex-wrap gap-2">
        <div id="contractsInfo" class="text-[10px] text-gray-500 font-medium"></div>
        <div id="contractsPagination"></div>
    </div>
</div>

{{-- Create Modal --}}
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Contract</h3>
        <form method="POST" action="{{ route('admin.crm-contracts.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Deal</label><select name="deal_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">None</option>
            @foreach($deals as $d)
            <option value="{{ $d->id }}">{{ $d->title }} ({{ $d->deal_number }})</option>
            @endforeach
            </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Client Name *</label><input name="client_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Value *</label><input name="value" type="number" step="0.01" required value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input name="start_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input name="end_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Terms</label><textarea name="terms" rows="4" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="draft">Draft</option><option value="active">Active</option><option value="expired">Expired</option><option value="terminated">Terminated</option></select></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const contractsDataUrl = '{{ route("admin.crm-contracts.data") }}';
const contractDestroyUrl = '{{ route("admin.crm-contracts.destroy", "__ID__") }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
let contractSearchTimer = null;

function loadContracts(page = 1) {
    const search = document.getElementById('contractSearch').value;
    const status = document.getElementById('contractStatusFilter').value;
    const perPage = document.getElementById('contractPerPage').value;

    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    params.set('per_page', perPage);
    params.set('page', page);

    fetch(contractsDataUrl + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => renderContracts(res))
    .catch(() => {
        document.getElementById('contractsTableBody').innerHTML = '<tr><td colspan="7" class="px-5 py-12 text-center text-sm text-red-400">Failed to load contracts.</td></tr>';
    });
}

function renderContracts(res) {
    const tbody = document.getElementById('contractsTableBody');
    const contracts = res.data || [];

    if (contracts.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-12 text-center"><svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p class="text-sm text-gray-400">No contracts found</p></td></tr>';
    } else {
        tbody.innerHTML = contracts.map(c => {
            const statusColors = {draft: 'gray', active: 'emerald', expired: 'red', terminated: 'red'};
            const sc = statusColors[c.status] || 'gray';
            const startDate = c.start_date ? new Date(c.start_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';
            const endDate = c.end_date ? new Date(c.end_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';
            let actions = '';
            actions += '<button onclick="deleteContract(' + c.id + ',\'' + escapeQuotes(c.contract_number || '') + '\')" title="Delete" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>';

            return '<tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">' +
                '<td class="px-5 py-3 text-xs font-mono text-gray-700 font-medium">' + escapeHtml(c.contract_number || '') + '</td>' +
                '<td class="px-5 py-3 text-xs font-medium text-gray-900">' + escapeHtml(c.title || '') + '</td>' +
                '<td class="px-5 py-3 text-xs text-gray-700">' + escapeHtml(c.client_name || '') + '</td>' +
                '<td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TZS ' + numberFormat(c.value || 0) + '</td>' +
                '<td class="px-5 py-3 text-xs text-gray-400">' + startDate + ' - ' + endDate + '</td>' +
                '<td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-' + sc + '-50 text-' + sc + '-700 border border-' + sc + '-100">' + capitalize(c.status || '') + '</span></td>' +
                '<td class="px-5 py-3 text-right"><div class="flex items-center justify-end gap-1">' + actions + '</div></td>' +
                '</tr>';
        }).join('');
    }

    document.getElementById('contractsInfo').textContent = res.total ? ('Showing ' + (res.from || 0) + '–' + (res.to || 0) + ' of ' + res.total) : '';
    document.getElementById('contractsPagination').innerHTML = res.links || '';
}

function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function escapeQuotes(s) { return s.replace(/'/g, "\\'"); }
function numberFormat(n) { return Number(n).toLocaleString('en-US'); }
function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

function exportContractsPdf() {
    const search = document.getElementById('contractSearch').value;
    const status = document.getElementById('contractStatusFilter').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    params.set('per_page', 'all');
    fetch(contractsDataUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            const contracts = res.data || [];
            if (contracts.length === 0) { Swal.fire('No Data', 'No contracts to export.', 'info'); return; }
            const w = window.open('', '_blank');
            w.document.write('<html><head><title>Contracts Export</title><style>body{font-family:Arial,sans-serif;padding:20px}table{width:100%;border-collapse:collapse;font-size:11px}th,td{border:1px solid #ddd;padding:6px 8px;text-align:left}th{background:#f5f5f5;font-weight:bold}.right{text-align:right}</style></head><body>');
            w.document.write('<h2>CRM Contracts Report</h2><p>Generated: ' + new Date().toLocaleString() + '</p>');
            w.document.write('<table><thead><tr><th>Contract #</th><th>Title</th><th>Client</th><th>Value</th><th>Start</th><th>End</th><th>Status</th></tr></thead><tbody>');
            contracts.forEach(c => {
                const sd = c.start_date ? new Date(c.start_date).toLocaleDateString() : '—';
                const ed = c.end_date ? new Date(c.end_date).toLocaleDateString() : '—';
                w.document.write('<tr><td>' + (c.contract_number||'') + '</td><td>' + escapeHtml(c.title||'') + '</td><td>' + escapeHtml(c.client_name||'') + '</td><td class="right">TZS ' + numberFormat(c.value||0) + '</td><td>' + sd + '</td><td>' + ed + '</td><td>' + capitalize(c.status||'') + '</td></tr>');
            });
            w.document.write('</tbody></table></body></html>');
            w.document.close();
            w.print();
        });
}

function deleteContract(id, num) {
    Swal.fire({
        title: 'Delete Contract?', text: 'Contract ' + num + ' will be permanently removed.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel', reverseButtons: true
    }).then((r) => {
        if (r.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = contractDestroyUrl.replace('__ID__', id);
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
document.getElementById('contractSearch').addEventListener('input', function() {
    clearTimeout(contractSearchTimer);
    contractSearchTimer = setTimeout(() => loadContracts(1), 300);
});
document.getElementById('contractStatusFilter').addEventListener('change', () => loadContracts(1));
document.getElementById('contractPerPage').addEventListener('change', () => loadContracts(1));
document.getElementById('contractsPagination').addEventListener('click', function(e) {
    e.preventDefault();
    const link = e.target.closest('a');
    if (!link) return;
    const url = new URL(link.href);
    const page = url.searchParams.get('page') || 1;
    loadContracts(page);
});

// Initial load
loadContracts(1);
</script>
@endpush
@endsection
