@extends('layouts.admin')
@section('title', 'CRM Leads - ' . config('app.name'))
@section('page_title', 'Leads')
@section('content')

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    {{-- Toolbar --}}
    <div class="px-5 py-4 border-b bg-gray-50/40 flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="leadSearch" type="text" placeholder="Search leads..." class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
            </div>
            <select id="leadStatusFilter" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="">All Status</option>
                <option value="new">New</option>
                <option value="contacted">Contacted</option>
                <option value="qualified">Qualified</option>
                <option value="converted">Converted</option>
                <option value="lost">Lost</option>
            </select>
            <select id="leadSourceFilter" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="">All Sources</option>
                <option value="website">Website</option>
                <option value="referral">Referral</option>
                <option value="social_media">Social Media</option>
                <option value="cold_call">Cold Call</option>
                <option value="email_campaign">Email Campaign</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <select id="leadPerPage" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                <option value="15">15 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
            <button onclick="exportLeadsPdf()" class="px-3 py-2 bg-violet-50 text-violet-700 border border-violet-200 text-xs font-semibold rounded-lg hover:bg-violet-100 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF
            </button>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Lead
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] text-gray-500 uppercase tracking-wider bg-gray-50/50 border-b">
                    <th class="px-5 py-3 font-semibold">Lead #</th>
                    <th class="px-5 py-3 font-semibold">Name</th>
                    <th class="px-5 py-3 font-semibold">Company</th>
                    <th class="px-5 py-3 font-semibold">Source</th>
                    <th class="px-5 py-3 font-semibold">Assigned To</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="leadsTableBody">
                <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">Loading leads...</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t bg-gray-50/30 flex items-center justify-between flex-wrap gap-2">
        <div id="leadsInfo" class="text-[10px] text-gray-500 font-medium"></div>
        <div id="leadsPagination"></div>
    </div>
</div>

{{-- Create Modal --}}
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Lead</h3>
        <form method="POST" action="{{ route('admin.crm-leads.store') }}" class="space-y-3">@csrf
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">First Name *</label><input name="first_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Last Name</label><input name="last_name" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" type="email" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Company</label><input name="company" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Source</label><select name="source" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="website">Website</option><option value="referral">Referral</option><option value="social_media">Social Media</option><option value="cold_call">Cold Call</option><option value="email_campaign">Email Campaign</option><option value="other">Other</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label><select name="assigned_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">Unassigned</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
            </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="new">New</option><option value="contacted">Contacted</option><option value="qualified">Qualified</option><option value="converted">Converted</option><option value="lost">Lost</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
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

    fetch(leadsDataUrl + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => renderLeads(res))
    .catch(() => {
        document.getElementById('leadsTableBody').innerHTML = '<tr><td colspan="7" class="px-5 py-12 text-center text-sm text-red-400">Failed to load leads.</td></tr>';
    });
}

function renderLeads(res) {
    const tbody = document.getElementById('leadsTableBody');
    const leads = res.data || [];

    if (leads.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-12 text-center"><svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg><p class="text-sm text-gray-400">No leads found</p></td></tr>';
    } else {
        tbody.innerHTML = leads.map(l => {
            const statusColors = {new: 'sky', contacted: 'amber', qualified: 'emerald', lost: 'red', converted: 'emerald'};
            const sc = statusColors[l.status] || 'gray';
            const assigned = l.assigned_to ? (l.assigned_to?.name || 'Unassigned') : 'Unassigned';
            let actions = '';
            if (l.status !== 'converted' && (!l.deals || l.deals.length === 0)) {
                actions += '<button onclick="convertLead(' + l.id + ')" title="Convert to Deal" class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg></button>';
            }
            actions += '<button onclick="downloadLeadPdf(' + l.id + ')" title="PDF" class="w-7 h-7 rounded-lg bg-violet-50 text-violet-600 hover:bg-violet-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></button>';
            actions += '<button onclick="deleteLead(' + l.id + ',\'' + escapeQuotes(l.lead_number || '') + '\')" title="Delete" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>';

            return '<tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">' +
                '<td class="px-5 py-3 text-xs font-mono text-gray-700 font-medium">' + escapeHtml(l.lead_number || '') + '</td>' +
                '<td class="px-5 py-3 text-xs font-medium text-gray-900">' + escapeHtml((l.first_name || '') + ' ' + (l.last_name || '')) + '</td>' +
                '<td class="px-5 py-3 text-xs text-gray-500">' + escapeHtml(l.company || 'N/A') + '</td>' +
                '<td class="px-5 py-3 text-xs text-gray-500">' + escapeHtml((l.source || 'N/A').replace(/_/g, ' ')) + '</td>' +
                '<td class="px-5 py-3 text-xs text-gray-500">' + escapeHtml(assigned) + '</td>' +
                '<td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-' + sc + '-50 text-' + sc + '-700 border border-' + sc + '-100">' + capitalize(l.status || '') + '</span></td>' +
                '<td class="px-5 py-3 text-right"><div class="flex items-center justify-end gap-1">' + actions + '</div></td>' +
                '</tr>';
        }).join('');
    }

    document.getElementById('leadsInfo').textContent = res.total ? ('Showing ' + (res.from || 0) + '–' + (res.to || 0) + ' of ' + res.total) : '';
    document.getElementById('leadsPagination').innerHTML = res.links || '';
}

function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function escapeQuotes(s) { return s.replace(/'/g, "\\'"); }
function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

function downloadLeadPdf(id) {
    window.open(leadPdfUrl.replace('__ID__', id), '_blank');
}

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
        confirmButtonText: 'Delete', cancelButtonText: 'Cancel', reverseButtons: true
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

// Event listeners
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

// Initial load
loadLeads(1);
</script>
@endpush
@endsection
