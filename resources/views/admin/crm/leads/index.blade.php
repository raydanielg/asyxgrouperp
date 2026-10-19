@extends('layouts.admin')
@section('title', 'CRM Leads - ' . config('app.name'))
@section('page_title', 'Leads')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    {{-- Toolbar --}}
    <div class="px-5 py-3.5 border-b border-gray-200 flex flex-wrap items-center gap-2">
        <input id="leadSearch" type="text" placeholder="Search leads..." class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white w-52">
        <select id="leadStatusFilter" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Status</option>
            <option value="new">New</option>
            <option value="contacted">Contacted</option>
            <option value="qualified">Qualified</option>
            <option value="converted">Converted</option>
            <option value="lost">Lost</option>
        </select>
        <select id="leadSourceFilter" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Sources</option>
            <option value="website">Website</option>
            <option value="referral">Referral</option>
            <option value="social_media">Social Media</option>
            <option value="cold_call">Cold Call</option>
            <option value="email_campaign">Email Campaign</option>
            <option value="other">Other</option>
        </select>
        <div class="flex items-center gap-2 ml-auto">
            <select id="leadPerPage" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="15">15 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
            <button onclick="exportLeadsPdf()" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50">Export PDF</button>
            <button type="button" onclick="openLeadDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Lead</button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Lead #</th>
                    <th class="px-5 py-3 font-semibold">Name</th>
                    <th class="px-5 py-3 font-semibold">Company</th>
                    <th class="px-5 py-3 font-semibold">Source</th>
                    <th class="px-5 py-3 font-semibold">Assigned To</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="leadsTableBody" class="divide-y divide-gray-100">
                <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">Loading leads...</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
        <div id="leadsInfo" class="text-[11px] text-gray-400"></div>
        <div id="leadsPagination"></div>
    </div>
</div>

{{-- New Lead Drawer --}}
<div id="leadDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeLeadDrawer()"></div>
    <div id="leadDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Lead</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Capture a prospect</p>
            </div>
            <button type="button" onclick="closeLeadDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.crm-leads.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">First Name <span class="text-red-500">*</span></label><input name="first_name" required placeholder="e.g. John" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Last Name</label><input name="last_name" placeholder="e.g. Mushi" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" type="email" placeholder="name@company.com" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" placeholder="+255 700 000 000" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Company</label><input name="company" placeholder="e.g. Acme Ltd" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Source</label><select name="source" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="website">Website</option><option value="referral">Referral</option><option value="social_media">Social Media</option><option value="cold_call">Cold Call</option><option value="email_campaign">Email Campaign</option><option value="other">Other</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="new">New</option><option value="contacted">Contacted</option><option value="qualified">Qualified</option><option value="converted">Converted</option><option value="lost">Lost</option></select></div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label>
                    <select name="assigned_to" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— Unassigned —</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="3" placeholder="Lead details, interest, context..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Lead</button>
                <button type="button" onclick="closeLeadDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const leadsDataUrl = '{{ route("admin.crm-leads.data") }}';
const leadPdfUrl = '{{ route("admin.crm-leads.pdf", "__ID__") }}';
const leadDestroyUrl = '{{ route("admin.crm-leads.destroy", "__ID__") }}';
const leadConvertUrl = '{{ route("admin.crm-leads.convert-to-deal", "__ID__") }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
let leadSearchTimer = null;

const LEAD_COLORS = { new: '#3b82f6', contacted: '#f59e0b', qualified: '#10b981', converted: '#10b981', lost: '#ef4444' };

function openLeadDrawer() {
    document.getElementById('leadDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('leadDrawerPanel').classList.remove('-translate-x-full'));
}
function closeLeadDrawer() {
    document.getElementById('leadDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('leadDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLeadDrawer(); });

function loadLeads(page = 1) {
    const search = document.getElementById('leadSearch').value;
    const status = document.getElementById('leadStatusFilter').value;
    const source = document.getElementById('leadSourceFilter').value;
    const perPage = document.getElementById('leadPerPage').value;

    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    if (source) params.set('source', source);
    params.set('per_page', perPage);
    params.set('page', page);

    fetch(leadsDataUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(res => renderLeads(res))
    .catch(() => {
        document.getElementById('leadsTableBody').innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center text-xs text-red-400">Failed to load leads.</td></tr>';
    });
}

function renderLeads(res) {
    const tbody = document.getElementById('leadsTableBody');
    const leads = res.data || [];

    if (leads.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No leads found</td></tr>';
    } else {
        tbody.innerHTML = leads.map(l => {
            const sc = LEAD_COLORS[l.status] || '#9ca3af';
            const assigned = l.assigned_to ? (l.assigned_to?.name || 'Unassigned') : 'Unassigned';
            let actions = '';
            if (l.status !== 'converted' && (!l.deals || l.deals.length === 0)) {
                actions += '<button onclick="convertLead(' + l.id + ')" class="text-xs font-medium text-gray-600 hover:text-gray-900">→ Deal</button>';
            }
            actions += '<button onclick="downloadLeadPdf(' + l.id + ')" class="text-xs font-medium text-gray-600 hover:text-gray-900">PDF</button>';
            actions += '<button onclick="deleteLead(' + l.id + ',\'' + escapeQuotes(l.lead_number || '') + '\')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>';

            return '<tr class="hover:bg-gray-50/60">' +
                '<td class="px-5 py-3.5 text-xs font-mono text-gray-700">' + escapeHtml(l.lead_number || '') + '</td>' +
                '<td class="px-5 py-3.5 text-xs font-medium text-gray-900">' + escapeHtml((l.first_name || '') + ' ' + (l.last_name || '')) + '</td>' +
                '<td class="px-5 py-3.5 text-xs text-gray-500">' + escapeHtml(l.company || '—') + '</td>' +
                '<td class="px-5 py-3.5 text-xs text-gray-500">' + capitalize((l.source || '—').replace(/_/g, ' ')) + '</td>' +
                '<td class="px-5 py-3.5 text-xs text-gray-500">' + escapeHtml(assigned) + '</td>' +
                '<td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:' + sc + '"></span>' + capitalize(l.status || '') + '</span></td>' +
                '<td class="px-5 py-3.5"><div class="flex items-center justify-end gap-4">' + actions + '</div></td>' +
                '</tr>';
        }).join('');
    }

    document.getElementById('leadsInfo').textContent = res.total ? ('Showing ' + (res.from || 0) + '–' + (res.to || 0) + ' of ' + res.total) : '';
    document.getElementById('leadsPagination').innerHTML = res.links || '';
}

function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function escapeQuotes(s) { return s.replace(/'/g, "\\'"); }
function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

function downloadLeadPdf(id) { window.open(leadPdfUrl.replace('__ID__', id), '_blank'); }

function exportLeadsPdf() {
    const search = document.getElementById('leadSearch').value;
    const status = document.getElementById('leadStatusFilter').value;
    const source = document.getElementById('leadSourceFilter').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    if (source) params.set('source', source);
    params.set('per_page', 'all');
    fetch(leadsDataUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            const leads = res.data || [];
            if (leads.length === 0) { Swal.fire('No Data', 'No leads to export.', 'info'); return; }
            const w = window.open('', '_blank');
            w.document.write('<html><head><title>Leads Export</title><style>body{font-family:Arial,sans-serif;padding:20px}table{width:100%;border-collapse:collapse;font-size:11px}th,td{border:1px solid #ddd;padding:6px 8px;text-align:left}th{background:#f5f5f5;font-weight:bold}</style></head><body>');
            w.document.write('<h2>CRM Leads Report</h2><p>Generated: ' + new Date().toLocaleString() + '</p>');
            w.document.write('<table><thead><tr><th>Lead #</th><th>Name</th><th>Company</th><th>Source</th><th>Assigned To</th><th>Status</th></tr></thead><tbody>');
            leads.forEach(l => {
                w.document.write('<tr><td>' + (l.lead_number||'') + '</td><td>' + escapeHtml((l.first_name||'')+' '+(l.last_name||'')) + '</td><td>' + escapeHtml(l.company||'N/A') + '</td><td>' + capitalize((l.source||'').replace(/_/g,' ')) + '</td><td>' + escapeHtml(l.assigned_to?(l.assigned_to.name||'Unassigned'):'Unassigned') + '</td><td>' + capitalize(l.status||'') + '</td></tr>');
            });
            w.document.write('</tbody></table></body></html>');
            w.document.close();
            w.print();
        });
}

function convertLead(id) {
    Swal.fire({
        title: 'Convert to Deal?',
        text: 'This lead will be converted into a deal.',
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#059669', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, convert', cancelButtonText: 'Cancel', reverseButtons: true
    }).then((r) => {
        if (r.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = leadConvertUrl.replace('__ID__', id);
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = csrfToken;
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function deleteLead(id, num) {
    Swal.fire({
        title: 'Delete Lead?', text: 'Lead ' + num + ' will be permanently removed.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete', cancelButtonText: 'Cancel', reverseButtons: true
    }).then((r) => {
        if (r.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = leadDestroyUrl.replace('__ID__', id);
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

document.getElementById('leadSearch').addEventListener('input', function() {
    clearTimeout(leadSearchTimer);
    leadSearchTimer = setTimeout(() => loadLeads(1), 300);
});
document.getElementById('leadStatusFilter').addEventListener('change', () => loadLeads(1));
document.getElementById('leadSourceFilter').addEventListener('change', () => loadLeads(1));
document.getElementById('leadPerPage').addEventListener('change', () => loadLeads(1));
document.getElementById('leadsPagination').addEventListener('click', function(e) {
    e.preventDefault();
    const link = e.target.closest('a');
    if (!link) return;
    const url = new URL(link.href);
    const page = url.searchParams.get('page') || 1;
    loadLeads(page);
});

loadLeads(1);
</script>
@endpush
@endsection
