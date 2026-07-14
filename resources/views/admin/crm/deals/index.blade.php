@extends('layouts.admin')
@section('title', 'CRM Deals - ' . config('app.name'))
@section('page_title', 'Deals Pipeline')
@section('content')
@php
$totalValue = $deals->sum('value');
$wonCount = $deals->where('status', 'won')->count();
$openCount = $deals->where('status', 'open')->count();
$lostCount = $deals->where('status', 'lost')->count();
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Pipeline Value</span>
        <p class="text-xl font-bold text-gray-900 mt-1">TZS {{ number_format($totalValue) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-emerald-200 p-4">
        <span class="text-[10px] font-medium text-emerald-600 uppercase tracking-wider">Won</span>
        <p class="text-xl font-bold text-emerald-700 mt-1">{{ $wonCount }}</p>
    </div>
    <div class="bg-white rounded-xl border border-sky-200 p-4">
        <span class="text-[10px] font-medium text-sky-600 uppercase tracking-wider">Open</span>
        <p class="text-xl font-bold text-sky-700 mt-1">{{ $openCount }}</p>
    </div>
    <div class="bg-white rounded-xl border border-rose-200 p-4">
        <span class="text-[10px] font-medium text-rose-600 uppercase tracking-wider">Lost</span>
        <p class="text-xl font-bold text-rose-700 mt-1">{{ $lostCount }}</p>
    </div>
</div>

<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2 text-xs text-gray-500">
        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-50 border">{{ $deals->total() ?? 0 }} Total</span>
    </div>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Deal
    </button>
</div>

<div class="bg-white rounded-xl border overflow-hidden shadow-sm">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Deal #</th>
            <th class="px-5 py-3 font-medium">Title</th>
            <th class="px-5 py-3 font-medium">Lead</th>
            <th class="px-5 py-3 font-medium text-right">Value</th>
            <th class="px-5 py-3 font-medium">Stage</th>
            <th class="px-5 py-3 font-medium">Expected Close</th>
            <th class="px-5 py-3 font-medium">Status</th>
            <th class="px-5 py-3 font-medium text-right">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($deals as $d)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
            <td class="px-5 py-3 text-xs font-mono text-gray-700 font-medium">{{ $d->deal_number }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $d->title }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $d->lead?->full_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TZS {{ number_format($d->value) }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">{{ ucfirst(str_replace('_', ' ', $d->stage)) }}</span></td>
            <td class="px-5 py-3 text-xs {{ $d->expected_close_date?->isPast() && $d->status === 'open' ? 'text-rose-600 font-medium' : 'text-gray-400' }}">{{ $d->expected_close_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3">
                @php $c=['open'=>'emerald','won'=>'emerald','lost'=>'red','cancelled'=>'gray']; @endphp
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$d->status] ?? 'gray' }}-50 text-{{ $c[$d->status] ?? 'gray' }}-700 border border-{{ $c[$d->status] ?? 'gray' }}-100">
                    @if($d->status === 'won')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>@endif
                    {{ ucfirst($d->status) }}
                </span>
            </td>
            <td class="px-5 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                    <button onclick="viewDeal({{ $d->id }}, '{{ $d->deal_number }}', '{{ addslashes($d->title) }}', '{{ addslashes($d->lead?->full_name ?? 'N/A') }}', '{{ number_format($d->value) }}', '{{ $d->stage }}', '{{ $d->expected_close_date?->format('d M Y') ?? '—' }}', '{{ $d->status }}', '{{ addslashes($d->notes ?? '') }}')" title="View" class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <button onclick="editDeal({{ $d->id }}, '{{ addslashes($d->title) }}', {{ $d->lead_id ?? 'null' }}, '{{ $d->value }}', '{{ $d->stage }}', '{{ $d->expected_close_date?->format('Y-m-d') ?? '' }}', {{ $d->assigned_to ?? 'null' }}, '{{ addslashes($d->notes ?? '') }}', '{{ $d->status }}')" title="Edit" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    @if($d->status==='open' && !$d->project_id)
                    <button onclick="convertDealToProject({{ $d->id }}, '{{ $d->deal_number }}', '{{ addslashes($d->title) }}')" title="Convert to Project" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </button>
                    <form id="convert-deal-{{ $d->id }}" method="POST" action="{{ route('admin.crm-deals.convert-to-project', $d) }}" class="hidden">@csrf</form>
                    @endif
                    <button onclick="downloadPdf('{{ route('admin.crm-deals.pdf', $d) }}', '{{ $d->deal_number }}')" title="Download PDF" class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 hover:bg-violet-100 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </button>
                    <form id="del-deal-{{ $d->id }}" method="POST" action="{{ route('admin.crm-deals.destroy', $d) }}" class="hidden">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-deal-{{ $d->id }}', 'Delete Deal?', 'Deal {{ $d->deal_number }} will be permanently removed.')" title="Delete" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-12 text-center">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <p class="text-sm text-gray-400">No deals found</p>
                <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Create your first deal →</button>
            </div>
        </td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t bg-gray-50/30">{{ $deals->links() }}</div>
</div>

{{── Create Modal ──}}
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

{{── Edit Modal ──}}
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

{{── View Modal ──}}
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
function downloadPdf(url, title) {
    window.open(url, '_blank');
}
function convertDealToProject(id, number, title) {
    Swal.fire({
        title: 'Convert to Project?',
        text: 'Deal ' + number + ' (' + title + ') will be converted into a project.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, convert',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((r) => { if (r.isConfirmed) document.getElementById('convert-deal-' + id).submit(); });
}
function viewDeal(id, num, title, lead, value, stage, close, status, notes) {
    document.getElementById('viewDealNumber').textContent = num;
    document.getElementById('viewDealTitle').textContent = title;
    document.getElementById('viewDealLead').textContent = lead;
    document.getElementById('viewDealValue').textContent = 'TZS ' + value;
    document.getElementById('viewDealStage').textContent = stage.replace(/_/g, ' ');
    document.getElementById('viewDealClose').textContent = close;
    var s = document.getElementById('viewDealStatus');
    s.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    s.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ' +
        (status === 'won' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' :
        status === 'lost' ? 'bg-red-50 text-red-700 border-red-200' :
        status === 'cancelled' ? 'bg-gray-50 text-gray-600 border-gray-200' :
        'bg-sky-50 text-sky-700 border-sky-200');
    var w = document.getElementById('viewDealNotesWrap');
    if (notes) {
        w.classList.remove('hidden');
        document.getElementById('viewDealNotes').textContent = notes;
    } else { w.classList.add('hidden'); }
    document.getElementById('viewModal').classList.remove('hidden');
}
function editDeal(id, title, leadId, value, stage, closeDate, assignedTo, notes, status) {
    document.getElementById('editDealId').value = id;
    document.getElementById('editTitle').value = title;
    if (leadId) document.getElementById('editLeadId').value = leadId;
    document.getElementById('editValue').value = value;
    document.getElementById('editStage').value = stage;
    document.getElementById('editCloseDate').value = closeDate;
    if (assignedTo) document.getElementById('editAssignedTo').value = assignedTo;
    document.getElementById('editNotes').value = notes;
    document.getElementById('editStatus').value = status;
    document.getElementById('editDealForm').action = '{{ route("admin.crm-deals.update", "") }}' + id;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endpush
@endsection
