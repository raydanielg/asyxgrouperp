@extends('layouts.admin')
@section('title', 'CRM Contracts - ' . config('app.name'))
@section('page_title', 'Contracts')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    {{-- Toolbar --}}
    <div class="px-5 py-3.5 border-b border-gray-200 flex flex-wrap items-center gap-2">
        <input id="contractSearch" type="text" placeholder="Search contracts..." class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white w-52">
        <select id="contractStatusFilter" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="active">Active</option>
            <option value="expired">Expired</option>
            <option value="terminated">Terminated</option>
        </select>
        <div class="flex items-center gap-2 ml-auto">
            <select id="contractPerPage" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="15">15 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
            <button onclick="exportContractsPdf()" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50">Export PDF</button>
            <button type="button" onclick="openContractDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Contract</button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Contract #</th>
                    <th class="px-5 py-3 font-semibold">Title</th>
                    <th class="px-5 py-3 font-semibold">Client</th>
                    <th class="px-5 py-3 font-semibold text-right">Value</th>
                    <th class="px-5 py-3 font-semibold">Period</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="contractsTableBody" class="divide-y divide-gray-100">
                <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">Loading contracts...</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
        <div id="contractsInfo" class="text-[11px] text-gray-400"></div>
        <div id="contractsPagination"></div>
    </div>
</div>

{{-- New Contract Drawer --}}
<div id="contractDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeContractDrawer()"></div>
    <div id="contractDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Contract</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Client agreement</p>
            </div>
            <button type="button" onclick="closeContractDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.crm-contracts.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="e.g. Annual supply contract" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Deal</label>
                    <select name="deal_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— None —</option>
                        @foreach($deals as $d)
                        <option value="{{ $d->id }}">{{ $d->title }} ({{ $d->deal_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Client Name <span class="text-red-500">*</span></label><input name="client_name" required placeholder="e.g. CRDB Bank" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Value (TZS) <span class="text-red-500">*</span></label><input name="value" type="number" step="0.01" min="0" required value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input name="start_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input name="end_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="draft">Draft</option><option value="active">Active</option><option value="expired">Expired</option><option value="terminated">Terminated</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Terms</label><textarea name="terms" rows="4" placeholder="Contract terms and conditions..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Contract</button>
                <button type="button" onclick="closeContractDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const contractsDataUrl = '{{ route("admin.crm-contracts.data") }}';
const contractDestroyUrl = '{{ route("admin.crm-contracts.destroy", "__ID__") }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
let contractSearchTimer = null;

const CONTRACT_COLORS = { draft: '#9ca3af', active: '#10b981', expired: '#ef4444', terminated: '#ef4444' };

function openContractDrawer() {
    document.getElementById('contractDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('contractDrawerPanel').classList.remove('-translate-x-full'));
}
function closeContractDrawer() {
    document.getElementById('contractDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('contractDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeContractDrawer(); });

function loadContracts(page = 1) {
    const search = document.getElementById('contractSearch').value;
    const status = document.getElementById('contractStatusFilter').value;
    const perPage = document.getElementById('contractPerPage').value;

    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    params.set('per_page', perPage);
    params.set('page', page);

    fetch(contractsDataUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(res => renderContracts(res))
    .catch(() => {
        document.getElementById('contractsTableBody').innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center text-xs text-red-400">Failed to load contracts.</td></tr>';
    });
}

function renderContracts(res) {
    const tbody = document.getElementById('contractsTableBody');
    const contracts = res.data || [];

    if (contracts.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No contracts found</td></tr>';
    } else {
        tbody.innerHTML = contracts.map(c => {
            const sc = CONTRACT_COLORS[c.status] || '#9ca3af';
            const startDate = c.start_date ? new Date(c.start_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';
            const endDate = c.end_date ? new Date(c.end_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';
            let actions = '<button onclick="deleteContract(' + c.id + ',\'' + escapeQuotes(c.contract_number || '') + '\')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>';

            return '<tr class="hover:bg-gray-50/60">' +
                '<td class="px-5 py-3.5 text-xs font-mono text-gray-700">' + escapeHtml(c.contract_number || '') + '</td>' +
                '<td class="px-5 py-3.5 text-xs font-medium text-gray-900">' + escapeHtml(c.title || '') + '</td>' +
                '<td class="px-5 py-3.5 text-xs text-gray-700">' + escapeHtml(c.client_name || '') + '</td>' +
                '<td class="px-5 py-3.5 text-xs font-medium font-mono text-gray-900 text-right">' + numberFormat(c.value || 0) + '</td>' +
                '<td class="px-5 py-3.5 text-xs text-gray-400">' + startDate + ' – ' + endDate + '</td>' +
                '<td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:' + sc + '"></span>' + capitalize(c.status || '') + '</span></td>' +
                '<td class="px-5 py-3.5"><div class="flex items-center justify-end gap-4">' + actions + '</div></td>' +
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
        confirmButtonText: 'Yes, delete', cancelButtonText: 'Cancel', reverseButtons: true
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

loadContracts(1);
</script>
@endpush
@endsection
