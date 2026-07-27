@extends('layouts.admin')
@section('title', 'CRM Deals - ' . config('app.name'))
@section('page_title', 'Deals')
@section('content')

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    {{-- Toolbar --}}
    <div class="px-5 py-4 border-b bg-gray-50/40 flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="dealSearch" type="text" placeholder="Search deals..." class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
            </div>
            <select id="dealStatusFilter" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="">All Status</option>
                <option value="open">Open</option>
                <option value="won">Won</option>
                <option value="lost">Lost</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select id="dealStageFilter" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="">All Stages</option>
                <option value="prospecting">Prospecting</option>
                <option value="qualification">Qualification</option>
                <option value="negotiation">Negotiation</option>
                <option value="proposal">Proposal</option>
                <option value="closed_won">Closed Won</option>
                <option value="closed_lost">Closed Lost</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <select id="dealPerPage" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="15">15 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
            <button onclick="exportDealsPdf()" class="px-3 py-2 bg-violet-50 text-violet-700 border border-violet-200 text-xs font-semibold rounded-lg hover:bg-violet-100 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF
            </button>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Deal
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] text-gray-500 uppercase tracking-wider bg-gray-50/50 border-b">
                    <th class="px-5 py-3 font-semibold">Deal #</th>
                    <th class="px-5 py-3 font-semibold">Title</th>
                    <th class="px-5 py-3 font-semibold">Lead</th>
                    <th class="px-5 py-3 font-semibold text-right">Value</th>
                    <th class="px-5 py-3 font-semibold">Stage</th>
                    <th class="px-5 py-3 font-semibold">Expected Close</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="dealsTableBody">
                <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">Loading deals...</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t bg-gray-50/30 flex items-center justify-between flex-wrap gap-2">
        <div id="dealsInfo" class="text-[10px] text-gray-500 font-medium"></div>
        <div id="dealsPagination"></div>
    </div>
</div>

{{-- Create Modal --}}
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Deal</h3>
        <form method="POST" action="{{ route('admin.crm-deals.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Lead</label><select name="lead_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">None</option>
            @foreach($leads as $l)
            <option value="{{ $l->id }}">{{ $l->full_name }} ({{ $l->company ?? 'N/A' }})</option>
            @endforeach
            </select></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Value *</label><input name="value" type="number" step="0.01" required value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Stage</label><select name="stage" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="prospecting">Prospecting</option><option value="qualification">Qualification</option><option value="negotiation">Negotiation</option><option value="proposal">Proposal</option><option value="closed_won">Closed Won</option><option value="closed_lost">Closed Lost</option></select></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Expected Close</label><input name="expected_close_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label><select name="assigned_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">Unassigned</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
                </select></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Deal</h3>
        <form id="editDealForm" method="POST" action="" class="space-y-3">@csrf @method('PATCH')
            <input type="hidden" name="deal_id" id="editDealId">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" id="editTitle" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Lead</label><select name="lead_id" id="editLeadId" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">None</option>
            @foreach($leads as $l)
            <option value="{{ $l->id }}">{{ $l->full_name }}</option>
            @endforeach
            </select></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Value *</label><input name="value" id="editValue" type="number" step="0.01" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Stage</label><select name="stage" id="editStage" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="prospecting">Prospecting</option><option value="qualification">Qualification</option><option value="negotiation">Negotiation</option><option value="proposal">Proposal</option><option value="closed_won">Closed Won</option><option value="closed_lost">Closed Lost</option></select></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Expected Close</label><input name="expected_close_date" id="editCloseDate" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" id="editStatus" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="open">Open</option><option value="won">Won</option><option value="lost">Lost</option><option value="cancelled">Cancelled</option></select></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label><select name="assigned_to" id="editAssignedTo" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">Unassigned</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
            </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" id="editNotes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update Deal</button></div>
        </form>
    </div>
</div>

{{-- View Modal --}}
<div id="viewModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900" id="viewDealNumber"></h3>
            <span id="viewDealStatus" class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium"></span>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div><span class="text-[10px] text-gray-400 uppercase">Title</span><p id="viewDealTitle" class="font-medium text-gray-900 mt-0.5"></p></div>
            <div><span class="text-[10px] text-gray-400 uppercase">Lead</span><p id="viewDealLead" class="font-medium text-gray-900 mt-0.5"></p></div>
            <div><span class="text-[10px] text-gray-400 uppercase">Value</span><p id="viewDealValue" class="font-bold text-emerald-700 mt-0.5"></p></div>
            <div><span class="text-[10px] text-gray-400 uppercase">Stage</span><p id="viewDealStage" class="font-medium text-gray-900 mt-0.5"></p></div>
            <div><span class="text-[10px] text-gray-400 uppercase">Expected Close</span><p id="viewDealClose" class="font-medium text-gray-900 mt-0.5"></p></div>
        </div>
        <div id="viewDealNotesWrap" class="hidden mb-4">
            <span class="text-[10px] text-gray-400 uppercase">Notes</span>
            <p id="viewDealNotes" class="text-sm text-gray-700 mt-1 bg-gray-50 border rounded-lg p-3"></p>
        </div>
        <button onclick="document.getElementById('viewModal').classList.add('hidden')" class="w-full px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Close</button>
    </div>
</div>

@push('scripts')
<script>
const dealsDataUrl = '{{ route("admin.crm-deals.data") }}';
const dealUpdateUrl = '{{ route("admin.crm-deals.update", "__ID__") }}';
const dealPdfUrl = '{{ route("admin.crm-deals.pdf", "__ID__") }}';
const dealDestroyUrl = '{{ route("admin.crm-deals.destroy", "__ID__") }}';
const dealConvertUrl = '{{ route("admin.crm-deals.convert-to-project", "__ID__") }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
let searchTimer = null;

function loadDeals(page = 1) {
    const search = document.getElementById('dealSearch').value;
    const status = document.getElementById('dealStatusFilter').value;
    const stage = document.getElementById('dealStageFilter').value;
    const perPage = document.getElementById('dealPerPage').value;

    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    if (stage) params.set('stage', stage);
    params.set('per_page', perPage);
    params.set('page', page);

    fetch(dealsDataUrl + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => renderDeals(res))
    .catch(() => {
        document.getElementById('dealsTableBody').innerHTML = '<tr><td colspan="8" class="px-5 py-12 text-center text-sm text-red-400">Failed to load deals.</td></tr>';
    });
}

function renderDeals(res) {
    const tbody = document.getElementById('dealsTableBody');
    const deals = res.data || [];

    if (deals.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-12 text-center"><svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg><p class="text-sm text-gray-400">No deals found</p></td></tr>';
    } else {
        tbody.innerHTML = deals.map(d => {
            const stageLabel = (d.stage || '').replace(/_/g, ' ');
            const statusColors = {open: 'sky', won: 'emerald', lost: 'red', cancelled: 'gray'};
            const sc = statusColors[d.status] || 'gray';
            const closeDate = d.expected_close_date ? new Date(d.expected_close_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';
            const isOverdue = d.expected_close_date && d.status === 'open' && new Date(d.expected_close_date) < new Date();
            const leadName = d.lead ? (d.lead.full_name || 'N/A') : 'N/A';
            let actions = '';
            actions += '<button onclick="viewDeal(' + d.id + ')" title="View" class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>';
            actions += '<button onclick="editDeal(' + d.id + ')" title="Edit" class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>';
            actions += '<button onclick="downloadDealPdf(' + d.id + ')" title="PDF" class="w-7 h-7 rounded-lg bg-violet-50 text-violet-600 hover:bg-violet-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></button>';
            if (d.status === 'open' && !d.project_id) {
                actions += '<button onclick="convertDeal(' + d.id + ',\'' + (d.deal_number||'') + '\',\'' + escapeQuotes(d.title||'') + '\')" title="Convert to Project" class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></button>';
            }
            actions += '<button onclick="deleteDeal(' + d.id + ',\'' + (d.deal_number||'') + '\')" title="Delete" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>';

            return '<tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">' +
                '<td class="px-5 py-3 text-xs font-mono text-gray-700 font-medium">' + (d.deal_number || '') + '</td>' +
                '<td class="px-5 py-3 text-xs font-medium text-gray-900">' + escapeHtml(d.title || '') + '</td>' +
                '<td class="px-5 py-3 text-xs text-gray-500">' + escapeHtml(leadName) + '</td>' +
                '<td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TZS ' + numberFormat(d.value || 0) + '</td>' +
                '<td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">' + capitalize(stageLabel) + '</span></td>' +
                '<td class="px-5 py-3 text-xs ' + (isOverdue ? 'text-rose-600 font-medium' : 'text-gray-400') + '">' + closeDate + '</td>' +
                '<td class="px-5 py-3"><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-' + sc + '-50 text-' + sc + '-700 border border-' + sc + '-100">' + capitalize(d.status || '') + '</span></td>' +
                '<td class="px-5 py-3 text-right"><div class="flex items-center justify-end gap-1">' + actions + '</div></td>' +
                '</tr>';
        }).join('');
    }

    document.getElementById('dealsInfo').textContent = res.total ? ('Showing ' + (res.from || 0) + '–' + (res.to || 0) + ' of ' + res.total) : '';
    document.getElementById('dealsPagination').innerHTML = res.links || '';
}

function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function escapeQuotes(s) { return s.replace(/'/g, "\\'"); }
function numberFormat(n) { return Number(n).toLocaleString('en-US'); }
function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

function downloadDealPdf(id) {
    window.open(dealPdfUrl.replace('__ID__', id), '_blank');
}

function exportDealsPdf() {
    const search = document.getElementById('dealSearch').value;
    const status = document.getElementById('dealStatusFilter').value;
    const stage = document.getElementById('dealStageFilter').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    if (stage) params.set('stage', stage);
    params.set('per_page', 'all');
    fetch(dealsDataUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            const deals = res.data || [];
            if (deals.length === 0) { Swal.fire('No Data', 'No deals to export.', 'info'); return; }
            const w = window.open('', '_blank');
            w.document.write('<html><head><title>Deals Export</title><style>body{font-family:Arial,sans-serif;padding:20px}table{width:100%;border-collapse:collapse;font-size:11px}th,td{border:1px solid #ddd;padding:6px 8px;text-align:left}th{background:#f5f5f5;font-weight:bold}.right{text-align:right}</style></head><body>');
            w.document.write('<h2>CRM Deals Report</h2><p>Generated: ' + new Date().toLocaleString() + '</p>');
            w.document.write('<table><thead><tr><th>Deal #</th><th>Title</th><th>Lead</th><th>Value</th><th>Stage</th><th>Close Date</th><th>Status</th></tr></thead><tbody>');
            deals.forEach(d => {
                w.document.write('<tr><td>' + (d.deal_number||'') + '</td><td>' + escapeHtml(d.title||'') + '</td><td>' + escapeHtml(d.lead?d.lead.full_name:'N/A') + '</td><td class="right">TZS ' + numberFormat(d.value||0) + '</td><td>' + capitalize((d.stage||'').replace(/_/g,' ')) + '</td><td>' + (d.expected_close_date?new Date(d.expected_close_date).toLocaleDateString():'—') + '</td><td>' + capitalize(d.status||'') + '</td></tr>');
            });
            w.document.write('</tbody></table></body></html>');
            w.document.close();
            w.print();
        });
}

function convertDeal(id, num, title) {
    Swal.fire({
        title: 'Convert to Project?',
        text: 'Deal ' + num + ' (' + title + ') will be converted into a project.',
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#059669', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, convert', cancelButtonText: 'Cancel', reverseButtons: true
    }).then((r) => {
        if (r.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = dealConvertUrl.replace('__ID__', id);
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = csrfToken;
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function deleteDeal(id, num) {
    Swal.fire({
        title: 'Delete Deal?', text: 'Deal ' + num + ' will be permanently removed.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel', reverseButtons: true
    }).then((r) => {
        if (r.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = dealDestroyUrl.replace('__ID__', id);
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

function viewDeal(id) {
    fetch(dealsDataUrl + '?per_page=all&page=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            const d = (res.data || []).find(x => x.id === id);
            if (!d) return;
            document.getElementById('viewDealNumber').textContent = d.deal_number || '';
            document.getElementById('viewDealTitle').textContent = d.title || '';
            document.getElementById('viewDealLead').textContent = d.lead ? (d.lead.full_name || 'N/A') : 'N/A';
            document.getElementById('viewDealValue').textContent = 'TZS ' + numberFormat(d.value || 0);
            document.getElementById('viewDealStage').textContent = capitalize((d.stage || '').replace(/_/g, ' '));
            document.getElementById('viewDealClose').textContent = d.expected_close_date ? new Date(d.expected_close_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';
            var s = document.getElementById('viewDealStatus');
            s.textContent = capitalize(d.status || '');
            const sc = {won:'emerald', lost:'red', cancelled:'gray', open:'sky'}[d.status] || 'gray';
            s.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border bg-' + sc + '-50 text-' + sc + '-700 border-' + sc + '-200';
            var w = document.getElementById('viewDealNotesWrap');
            if (d.notes) { w.classList.remove('hidden'); document.getElementById('viewDealNotes').textContent = d.notes; }
            else { w.classList.add('hidden'); }
            document.getElementById('viewModal').classList.remove('hidden');
        });
}

function editDeal(id) {
    fetch(dealsDataUrl + '?per_page=all&page=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            const d = (res.data || []).find(x => x.id === id);
            if (!d) return;
            document.getElementById('editDealId').value = d.id;
            document.getElementById('editTitle').value = d.title || '';
            if (d.lead_id) document.getElementById('editLeadId').value = d.lead_id;
            document.getElementById('editValue').value = d.value || 0;
            document.getElementById('editStage').value = d.stage || 'prospecting';
            document.getElementById('editCloseDate').value = d.expected_close_date ? d.expected_close_date.substring(0, 10) : '';
            if (d.assigned_to) document.getElementById('editAssignedTo').value = d.assigned_to;
            document.getElementById('editNotes').value = d.notes || '';
            document.getElementById('editStatus').value = d.status || 'open';
            document.getElementById('editDealForm').action = dealUpdateUrl.replace('__ID__', id);
            document.getElementById('editModal').classList.remove('hidden');
        });
}

// Event listeners
document.getElementById('dealSearch').addEventListener('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadDeals(1), 300);
});
document.getElementById('dealStatusFilter').addEventListener('change', () => loadDeals(1));
document.getElementById('dealStageFilter').addEventListener('change', () => loadDeals(1));
document.getElementById('dealPerPage').addEventListener('change', () => loadDeals(1));
document.getElementById('dealsPagination').addEventListener('click', function(e) {
    e.preventDefault();
    const link = e.target.closest('a');
    if (!link) return;
    const url = new URL(link.href);
    const page = url.searchParams.get('page') || 1;
    loadDeals(page);
});

// Initial load
loadDeals(1);
</script>
@endpush
@endsection
