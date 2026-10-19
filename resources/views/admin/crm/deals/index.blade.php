@extends('layouts.admin')
@section('title', 'CRM Deals - ' . config('app.name'))
@section('page_title', 'Deals')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    {{-- Toolbar --}}
    <div class="px-5 py-3.5 border-b border-gray-200 flex flex-wrap items-center gap-2">
        <input id="dealSearch" type="text" placeholder="Search deals..." class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white w-52">
        <select id="dealStatusFilter" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Status</option>
            <option value="open">Open</option>
            <option value="won">Won</option>
            <option value="lost">Lost</option>
            <option value="cancelled">Cancelled</option>
        </select>
        <select id="dealStageFilter" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Stages</option>
            <option value="prospecting">Prospecting</option>
            <option value="qualification">Qualification</option>
            <option value="negotiation">Negotiation</option>
            <option value="proposal">Proposal</option>
            <option value="closed_won">Closed Won</option>
            <option value="closed_lost">Closed Lost</option>
        </select>
        <div class="flex items-center gap-2 ml-auto">
            <select id="dealPerPage" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="15">15 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
            <button onclick="exportDealsPdf()" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50">Export PDF</button>
            <button type="button" onclick="openDealDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Deal</button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
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
            <tbody id="dealsTableBody" class="divide-y divide-gray-100">
                <tr><td colspan="8" class="px-5 py-10 text-center text-xs text-gray-400">Loading deals...</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
        <div id="dealsInfo" class="text-[11px] text-gray-400"></div>
        <div id="dealsPagination"></div>
    </div>
</div>

{{-- New Deal Drawer --}}
<div id="dealDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDealDrawer()"></div>
    <div id="dealDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Deal</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Sales opportunity</p>
            </div>
            <button type="button" onclick="closeDealDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.crm-deals.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="e.g. Supply of office furniture" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Lead</label>
                    <select name="lead_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— None —</option>
                        @foreach($leads as $l)
                        <option value="{{ $l->id }}">{{ $l->full_name }} ({{ $l->company ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Value (TZS) <span class="text-red-500">*</span></label><input name="value" type="number" step="0.01" min="0" required value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Stage</label><select name="stage" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="prospecting">Prospecting</option><option value="qualification">Qualification</option><option value="negotiation">Negotiation</option><option value="proposal">Proposal</option><option value="closed_won">Closed Won</option><option value="closed_lost">Closed Lost</option></select></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Expected Close</label><input name="expected_close_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label>
                        <select name="assigned_to" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            <option value="">— Unassigned —</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="3" placeholder="Deal context, next steps..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Deal</button>
                <button type="button" onclick="closeDealDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Deal Drawer --}}
<div id="editDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeEditDrawer()"></div>
    <div id="editDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">Edit Deal</h3>
                <p class="text-[11px] text-slate-400 mt-0.5" id="editDrawerNum"></p>
            </div>
            <button type="button" onclick="closeEditDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editDealForm" method="POST" action="" class="flex-1 overflow-y-auto p-5">
            @csrf @method('PATCH')
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" id="editTitle" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Lead</label>
                    <select name="lead_id" id="editLeadId" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— None —</option>
                        @foreach($leads as $l)
                        <option value="{{ $l->id }}">{{ $l->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Value (TZS) <span class="text-red-500">*</span></label><input name="value" id="editValue" type="number" step="0.01" min="0" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Stage</label><select name="stage" id="editStage" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="prospecting">Prospecting</option><option value="qualification">Qualification</option><option value="negotiation">Negotiation</option><option value="proposal">Proposal</option><option value="closed_won">Closed Won</option><option value="closed_lost">Closed Lost</option></select></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Expected Close</label><input name="expected_close_date" id="editCloseDate" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" id="editStatus" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="open">Open</option><option value="won">Won</option><option value="lost">Lost</option><option value="cancelled">Cancelled</option></select></div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label>
                    <select name="assigned_to" id="editAssignedTo" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— Unassigned —</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" id="editNotes" rows="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save Changes</button>
                <button type="button" onclick="closeEditDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- View Deal Drawer --}}
<div id="viewDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeViewDrawer()"></div>
    <div id="viewDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white" id="viewDealNumber"></h3>
                <p class="text-[11px] text-slate-400 mt-0.5"><span id="viewStatusDot"></span><span id="viewDealStatus"></span></p>
            </div>
            <button type="button" onclick="closeViewDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Deal</div>
                <div class="space-y-2.5">
                    <div class="flex justify-between"><span class="text-xs text-gray-400">Title</span><span id="viewDealTitle" class="text-xs font-medium text-gray-900 text-right"></span></div>
                    <div class="flex justify-between"><span class="text-xs text-gray-400">Lead</span><span id="viewDealLead" class="text-xs text-gray-700"></span></div>
                    <div class="flex justify-between"><span class="text-xs text-gray-400">Value</span><span id="viewDealValue" class="text-xs font-bold text-gray-900"></span></div>
                    <div class="flex justify-between"><span class="text-xs text-gray-400">Stage</span><span id="viewDealStage" class="text-xs text-gray-700"></span></div>
                    <div class="flex justify-between"><span class="text-xs text-gray-400">Expected Close</span><span id="viewDealClose" class="text-xs text-gray-700"></span></div>
                    <div class="flex justify-between"><span class="text-xs text-gray-400">Assigned To</span><span id="viewDealAssigned" class="text-xs text-gray-700"></span></div>
                </div>
            </div>
            <div id="viewDealNotesWrap" class="hidden">
                <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Notes</div>
                <p id="viewDealNotes" class="text-xs text-gray-600 leading-relaxed"></p>
            </div>
            <div class="pt-4 border-t border-gray-100 flex items-center gap-4">
                <button onclick="viewDealEdit()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Edit Deal</button>
                <button onclick="viewDealPdf()" class="px-4 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50">View PDF</button>
            </div>
        </div>
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
let currentDeal = null;

const DEAL_COLORS = { open: '#3b82f6', won: '#10b981', lost: '#ef4444', cancelled: '#9ca3af' };

function openDrawer(drawerId, panelId) {
    document.getElementById(drawerId).classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById(panelId).classList.remove('-translate-x-full'));
}
function closeDrawer(drawerId, panelId) {
    document.getElementById(panelId).classList.add('-translate-x-full');
    setTimeout(() => document.getElementById(drawerId).classList.add('hidden'), 300);
}
function openDealDrawer() { openDrawer('dealDrawer', 'dealDrawerPanel'); }
function closeDealDrawer() { closeDrawer('dealDrawer', 'dealDrawerPanel'); }
function closeEditDrawer() { closeDrawer('editDrawer', 'editDrawerPanel'); }
function closeViewDrawer() { closeDrawer('viewDrawer', 'viewDrawerPanel'); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeDealDrawer(); closeEditDrawer(); closeViewDrawer(); } });

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

    fetch(dealsDataUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(res => renderDeals(res))
    .catch(() => {
        document.getElementById('dealsTableBody').innerHTML = '<tr><td colspan="8" class="px-5 py-10 text-center text-xs text-red-400">Failed to load deals.</td></tr>';
    });
}

function renderDeals(res) {
    const tbody = document.getElementById('dealsTableBody');
    const deals = res.data || [];

    if (deals.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-10 text-center text-xs text-gray-400">No deals found</td></tr>';
    } else {
        tbody.innerHTML = deals.map(d => {
            const stageLabel = (d.stage || '').replace(/_/g, ' ');
            const sc = DEAL_COLORS[d.status] || '#9ca3af';
            const closeDate = d.expected_close_date ? new Date(d.expected_close_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';
            const isOverdue = d.expected_close_date && d.status === 'open' && new Date(d.expected_close_date) < new Date();
            const leadName = d.lead ? (d.lead.full_name || '—') : '—';
            let actions = '<button onclick="viewDeal(' + d.id + ')" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</button>';
            actions += '<button onclick="editDeal(' + d.id + ')" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</button>';
            actions += '<button onclick="downloadDealPdf(' + d.id + ')" class="text-xs font-medium text-gray-600 hover:text-gray-900">PDF</button>';
            if (d.status === 'open' && !d.project_id) {
                actions += '<button onclick="convertDeal(' + d.id + ',\'' + (d.deal_number||'') + '\',\'' + escapeQuotes(d.title||'') + '\')" class="text-xs font-medium text-emerald-600 hover:text-emerald-800">→ Project</button>';
            }
            actions += '<button onclick="deleteDeal(' + d.id + ',\'' + (d.deal_number||'') + '\')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>';

            return '<tr class="hover:bg-gray-50/60">' +
                '<td class="px-5 py-3.5 text-xs font-mono text-gray-700">' + (d.deal_number || '') + '</td>' +
                '<td class="px-5 py-3.5 text-xs font-medium text-gray-900">' + escapeHtml(d.title || '') + '</td>' +
                '<td class="px-5 py-3.5 text-xs text-gray-500">' + escapeHtml(leadName) + '</td>' +
                '<td class="px-5 py-3.5 text-xs font-medium font-mono text-gray-900 text-right">' + numberFormat(d.value || 0) + '</td>' +
                '<td class="px-5 py-3.5 text-xs text-gray-600">' + capitalize(stageLabel) + '</td>' +
                '<td class="px-5 py-3.5 text-xs ' + (isOverdue ? 'text-red-500 font-medium' : 'text-gray-400') + '">' + closeDate + '</td>' +
                '<td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:' + sc + '"></span>' + capitalize(d.status || '') + '</span></td>' +
                '<td class="px-5 py-3.5"><div class="flex items-center justify-end gap-4">' + actions + '</div></td>' +
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

function downloadDealPdf(id) { window.open(dealPdfUrl.replace('__ID__', id), '_blank'); }

function fetchDeal(id, cb) {
    fetch(dealsDataUrl + '?per_page=all&page=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            const d = (res.data || []).find(x => x.id === id);
            if (d) { currentDeal = d; cb(d); }
        });
}

function viewDeal(id) {
    fetchDeal(id, d => {
        document.getElementById('viewDealNumber').textContent = d.deal_number || 'Deal';
        document.getElementById('viewDealTitle').textContent = d.title || '';
        document.getElementById('viewDealLead').textContent = d.lead ? (d.lead.full_name || '—') : '—';
        document.getElementById('viewDealValue').textContent = 'TZS ' + numberFormat(d.value || 0);
        document.getElementById('viewDealStage').textContent = capitalize((d.stage || '').replace(/_/g, ' '));
        document.getElementById('viewDealClose').textContent = d.expected_close_date ? new Date(d.expected_close_date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';
        document.getElementById('viewDealAssigned').textContent = d.assigned_to_name || (d.assigned_to ? 'User #' + d.assigned_to : 'Unassigned');
        const sc = DEAL_COLORS[d.status] || '#9ca3af';
        document.getElementById('viewStatusDot').innerHTML = '<span style="display:inline-block;width:6px;height:6px;border-radius:99px;background:' + sc + ';margin-right:6px;vertical-align:1px;"></span>';
        document.getElementById('viewDealStatus').textContent = capitalize(d.status || '');
        var w = document.getElementById('viewDealNotesWrap');
        if (d.notes) { w.classList.remove('hidden'); document.getElementById('viewDealNotes').textContent = d.notes; }
        else { w.classList.add('hidden'); }
        openDrawer('viewDrawer', 'viewDrawerPanel');
    });
}

function viewDealEdit() { if (currentDeal) { closeViewDrawer(); setTimeout(() => editDeal(currentDeal.id), 300); } }
function viewDealPdf() { if (currentDeal) downloadDealPdf(currentDeal.id); }

function editDeal(id) {
    fetchDeal(id, d => {
        document.getElementById('editDrawerNum').textContent = d.deal_number || '';
        document.getElementById('editTitle').value = d.title || '';
        document.getElementById('editLeadId').value = d.lead_id || '';
        document.getElementById('editValue').value = d.value || 0;
        document.getElementById('editStage').value = d.stage || 'prospecting';
        document.getElementById('editCloseDate').value = d.expected_close_date ? d.expected_close_date.substring(0, 10) : '';
        document.getElementById('editAssignedTo').value = d.assigned_to || '';
        document.getElementById('editNotes').value = d.notes || '';
        document.getElementById('editStatus').value = d.status || 'open';
        document.getElementById('editDealForm').action = dealUpdateUrl.replace('__ID__', id);
        openDrawer('editDrawer', 'editDrawerPanel');
    });
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
        confirmButtonText: 'Yes, delete', cancelButtonText: 'Cancel', reverseButtons: true
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

loadDeals(1);
</script>
@endpush
@endsection
