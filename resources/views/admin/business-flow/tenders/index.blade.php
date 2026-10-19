@extends('layouts.admin')
@section('title', 'Tenders - ' . config('app.name'))
@section('page_title', 'Tenders')
@section('content')
<div class="mb-4 flex items-center justify-between gap-3">
    <form method="GET" class="flex items-center gap-2 flex-1 max-w-md">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tenders..." class="w-full px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400">
        <select name="status" class="px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400">
            <option value="">All statuses</option>
            @foreach(['received'=>'Received','under_review'=>'Under Review','converted'=>'Converted','rejected'=>'Rejected'] as $k=>$v)
            <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
            @endforeach
        </select>
        <button class="px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Search</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.tenders.index') }}" class="text-xs text-gray-400 hover:text-gray-600">Clear</a>
        @endif
    </form>
    <button type="button" onclick="openTenderDrawer()" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Tender
    </button>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left border-b border-gray-200 bg-gray-50/50">
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Tender No.</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Title</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Client</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Est. Value</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Closing</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($tenders as $t)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $t->tender_number }}</td>
            <td class="px-5 py-3.5"><a href="{{ route('admin.tenders.show', $t) }}" class="text-xs font-medium text-gray-900 hover:underline">{{ $t->title }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $t->client_name }}@if($t->client_organization)<br><span class="text-[10px] text-gray-400">{{ $t->client_organization }}</span>@endif</td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-900">{{ number_format($t->estimated_value) }} <span class="text-gray-400">TZS</span></td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $t->closing_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3.5">
                @php $sd=['received'=>'#f59e0b','under_review'=>'#3b82f6','converted'=>'#10b981','rejected'=>'#ef4444'][$t->status] ?? '#9ca3af'; @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sd }}"></span>{{ ucfirst(str_replace('_',' ',$t->status)) }}</span>
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <button type="button" onclick="openTenderView({{ $t->id }})" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</button>
                    @if($t->status==='received' || $t->status==='under_review')
                    <form method="POST" action="{{ route('admin.tenders.convert-to-lead', $t) }}" class="inline" onsubmit="return confirmAction(this, {title:'Convert to Lead?', text:'{{ $t->tender_number }} will be converted to a CRM lead.', icon:'question', confirmText:'Yes, convert', color:'#059669'})">@csrf<button class="text-xs font-medium text-gray-600 hover:text-gray-900">→ Lead</button></form>
                    @endif
                    <form method="POST" action="{{ route('admin.tenders.destroy', $t) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Tender?', text:'{{ $t->tender_number }} will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No tenders found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($tenders->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $tenders->links() }}</div>
    @endif
</div>

{{-- ═══ Add Tender Drawer (slides from left) ═══ --}}
<div id="tenderDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeTenderDrawer()"></div>
    <div id="tenderDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Tender</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Register an incoming tender</p>
            </div>
            <button type="button" onclick="closeTenderDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.tenders.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Tender</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                    <input name="title" required placeholder="e.g. Supply & Installation of CCTV System" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Submission Date</label>
                        <input name="submission_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Closing Date</label>
                        <input name="closing_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Estimated Value (TZS)</label>
                    <input name="estimated_value" type="number" min="0" step="0.01" placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
            </div>

            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Client</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Client Name <span class="text-red-500">*</span></label>
                    <input name="client_name" required placeholder="e.g. John Mwakalinga" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Organization</label>
                    <input name="client_organization" placeholder="e.g. TANESCO" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                        <input name="client_email" type="email" placeholder="client@org.co.tz" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                        <input name="client_phone" placeholder="+255 ..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
            </div>

            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Details</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Scope of the tender..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Requirements</label>
                    <textarea name="requirements" rows="3" placeholder="Eligibility, documents, technical requirements..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Tender</button>
                <button type="button" onclick="closeTenderDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ Tender Detail Drawer (slides from left) ═══ --}}
<div id="tenderViewDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeTenderView()"></div>
    <div id="tenderViewPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-start justify-between flex-shrink-0">
            <div class="min-w-0">
                <p class="text-[10px] font-mono text-gold-400" id="tvNumber"></p>
                <h3 class="text-sm font-bold text-white mt-0.5 leading-snug" id="tvTitle"></h3>
                <p class="text-[11px] mt-1.5 inline-flex items-center gap-1.5" id="tvStatus"></p>
            </div>
            <button type="button" onclick="closeTenderView()" class="text-slate-400 hover:text-white transition-colors flex-shrink-0 ml-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Client</p>
            <div class="space-y-2.5 mb-5 text-sm">
                <div class="flex justify-between gap-4"><span class="text-xs text-gray-400">Name</span><span class="text-xs font-medium text-gray-800 text-right" id="tvClient"></span></div>
                <div class="flex justify-between gap-4" id="tvOrgRow"><span class="text-xs text-gray-400">Organization</span><span class="text-xs text-gray-800 text-right" id="tvOrg"></span></div>
                <div class="flex justify-between gap-4" id="tvEmailRow"><span class="text-xs text-gray-400">Email</span><span class="text-xs text-gray-800 text-right" id="tvEmail"></span></div>
                <div class="flex justify-between gap-4" id="tvPhoneRow"><span class="text-xs text-gray-400">Phone</span><span class="text-xs text-gray-800 text-right" id="tvPhone"></span></div>
            </div>

            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Tender</p>
            <div class="space-y-2.5 mb-5 text-sm">
                <div class="flex justify-between gap-4"><span class="text-xs text-gray-400">Est. Value</span><span class="text-xs font-semibold text-gray-900 text-right" id="tvValue"></span></div>
                <div class="flex justify-between gap-4"><span class="text-xs text-gray-400">Submission</span><span class="text-xs text-gray-800 text-right" id="tvSubmission"></span></div>
                <div class="flex justify-between gap-4"><span class="text-xs text-gray-400">Closing</span><span class="text-xs text-gray-800 text-right" id="tvClosing"></span></div>
                <div class="flex justify-between gap-4" id="tvLeadRow"><span class="text-xs text-gray-400">Converted To</span><span class="text-xs text-emerald-600 font-medium text-right" id="tvLead"></span></div>
            </div>

            <div id="tvDescRow" class="mb-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Description</p>
                <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line" id="tvDesc"></p>
            </div>
            <div id="tvReqRow" class="mb-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Requirements</p>
                <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line" id="tvReq"></p>
            </div>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 flex items-center gap-4 flex-shrink-0">
            <form method="POST" id="tvConvertForm" class="inline" onsubmit="return confirmAction(this, {title:'Convert to Lead?', text:'This tender will be converted to a CRM lead.', icon:'question', confirmText:'Yes, convert', color:'#059669'})">@csrf
                <button class="text-xs font-semibold text-gray-600 hover:text-gray-900">→ Convert to Lead</button>
            </form>
            <form method="POST" id="tvDeleteForm" class="inline ml-auto" onsubmit="return confirmAction(this, {title:'Delete Tender?', text:'This tender will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
                <button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
@php
    $tvTenders = $tenders->keyBy('id')->map(fn($t) => [
        'id' => $t->id,
        'number' => $t->tender_number,
        'title' => $t->title,
        'status' => ucfirst(str_replace('_',' ',$t->status)),
        'raw_status' => $t->status,
        'client' => $t->client_name,
        'org' => $t->client_organization,
        'email' => $t->client_email,
        'phone' => $t->client_phone,
        'value' => 'TZS ' . number_format($t->estimated_value),
        'submission' => $t->submission_date?->format('d M Y'),
        'closing' => $t->closing_date?->format('d M Y'),
        'desc' => $t->description,
        'req' => $t->requirements,
        'lead' => $t->lead?->lead_number,
    ]);
@endphp
const tendersData = @json($tvTenders);
const stDots = { 'Received': '#f59e0b', 'Under Review': '#3b82f6', 'Converted': '#10b981', 'Rejected': '#ef4444' };

function openTenderView(id) {
    const c = tendersData[id];
    if (!c) return;

    tvNumber.textContent = c.number;
    tvTitle.textContent = c.title;
    tvStatus.innerHTML = '<span style="width:7px;height:7px;border-radius:99px;background:' + (stDots[c.status] || '#9ca3af') + '"></span><span class="text-slate-300">' + c.status + '</span>';
    tvClient.textContent = c.client || '—';
    tvValue.textContent = c.value;
    tvSubmission.textContent = c.submission || '—';
    tvClosing.textContent = c.closing || '—';

    document.getElementById('tvOrgRow').classList.toggle('hidden', !c.org); tvOrg.textContent = c.org || '';
    document.getElementById('tvEmailRow').classList.toggle('hidden', !c.email); tvEmail.textContent = c.email || '';
    document.getElementById('tvPhoneRow').classList.toggle('hidden', !c.phone); tvPhone.textContent = c.phone || '';
    document.getElementById('tvDescRow').classList.toggle('hidden', !c.desc); tvDesc.textContent = c.desc || '';
    document.getElementById('tvReqRow').classList.toggle('hidden', !c.req); tvReq.textContent = c.req || '';
    document.getElementById('tvLeadRow').classList.toggle('hidden', !c.lead); tvLead.textContent = c.lead || '';

    const conv = document.getElementById('tvConvertForm');
    conv.classList.toggle('hidden', !(c.raw_status === 'received' || c.raw_status === 'under_review'));
    conv.action = '{{ url("/admin/tenders") }}/' + id + '/convert-to-lead';
    document.getElementById('tvDeleteForm').action = '{{ url("/admin/tenders") }}/' + id;

    const d = document.getElementById('tenderViewDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('tenderViewPanel').classList.remove('-translate-x-full'));
}
function closeTenderView() {
    const p = document.getElementById('tenderViewPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('tenderViewDrawer').classList.add('hidden'), 300);
}

function openTenderDrawer() {
    const d = document.getElementById('tenderDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('tenderDrawerPanel').classList.remove('-translate-x-full'));
}
function closeTenderDrawer() {
    const p = document.getElementById('tenderDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('tenderDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeTenderDrawer(); closeTenderView(); } });
</script>
@endsection
