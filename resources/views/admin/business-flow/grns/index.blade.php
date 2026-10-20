@extends('layouts.admin')
@section('title', 'GRNs - ' . config('app.name'))
@section('page_title', 'Goods Received Notes (GRN)')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-xs text-gray-400">Verify goods received against LPOs</p>
    <button type="button" onclick="openGrnDrawer()" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create GRN
    </button>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left border-b border-gray-200 bg-gray-50/50">
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">GRN No.</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">LPO</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Supplier</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Received</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">DN Ref</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($grns as $g)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.grns.show', $g) }}" class="text-xs font-medium font-mono text-gray-900 hover:underline">{{ $g->grn_number }}</a></td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $g->lpo?->lpo_number ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-700">{{ $g->supplier?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $g->received_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-400">{{ $g->delivery_note_number ?? '—' }}</td>
            <td class="px-5 py-3.5">
                @php $gd=['received'=>'#10b981','discrepant'=>'#f59e0b','pending'=>'#f59e0b','rejected'=>'#ef4444'][$g->status] ?? '#9ca3af'; @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $gd }}"></span>{{ ucfirst($g->status) }}</span>
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    @php $gReq = $g->status === 'pending' ? \App\Models\ApprovalRequest::where('module','grn')->where('module_id',$g->id)->latest('id')->first() : null; @endphp
                    @if($gReq?->status === 'approved' && $g->created_by !== auth()->id())
                    <form method="POST" action="{{ route('admin.grns.receive', $g) }}" class="inline" onsubmit="return confirmAction(this, {title:'Finalize GRN?', text:'Received quantities will be applied to the LPO.', confirmText:'Finalize', color:'#059669'})">@csrf
                        <button class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Finalize</button>
                    </form>
                    @elseif($g->status === 'pending')
                    <span class="text-[10px] text-amber-600 font-medium">Awaiting approval</span>
                    @endif
                    <a href="{{ route('admin.grns.show', $g) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    @unless(in_array($g->status, ['received','discrepant']))
                    <form method="POST" action="{{ route('admin.grns.destroy', $g) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete GRN?', text:'{{ $g->grn_number }} will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                    @endunless
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No GRNs found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($grns->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $grns->links() }}</div>
    @endif
</div>

{{-- ═══ Create GRN Drawer (slides from left) ═══ --}}
<div id="grnDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeGrnDrawer()"></div>
    <div id="grnDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New GRN</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Record goods received against an LPO</p>
            </div>
            <button type="button" onclick="closeGrnDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.grns.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Reference</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">LPO</label>
                    <select name="lpo_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        <option value="">— Select LPO —</option>
                        @foreach($lpos as $l)
                        <option value="{{ $l->id }}">{{ $l->lpo_number }} ({{ $l->supplier?->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label>
                    <select name="supplier_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        <option value="">— Select —</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Received Date <span class="text-red-500">*</span></label>
                        <input name="received_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Delivery Note No.</label>
                        <input name="delivery_note_number" placeholder="DN-..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 mb-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Items <span class="text-red-500">*</span></p>
                    <button type="button" onclick="addGrnItem()" class="text-xs font-medium text-gray-500 hover:text-gray-900">+ Add Item</button>
                </div>
                <div id="grnItems" class="space-y-2"></div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                <textarea name="notes" rows="2" placeholder="Condition of goods, discrepancies..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create GRN</button>
                <button type="button" onclick="closeGrnDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openGrnDrawer() {
    const d = document.getElementById('grnDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('grnDrawerPanel').classList.remove('-translate-x-full'));
}
function closeGrnDrawer() {
    const p = document.getElementById('grnDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('grnDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeGrnDrawer(); });

let grnItemIdx = 0;
function addGrnItem() {
    const html = `
    <div class="border border-gray-100 rounded p-2.5 space-y-2" id="grnitem-${grnItemIdx}">
        <div class="flex items-center gap-2">
            <input name="items[${grnItemIdx}][description]" placeholder="Item description" required class="flex-1 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
            <button type="button" onclick="document.getElementById('grnitem-${grnItemIdx}').remove()" class="text-gray-300 hover:text-red-500 flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <input name="items[${grnItemIdx}][quantity_expected]" type="number" step="0.01" placeholder="Expected" class="px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
            <input name="items[${grnItemIdx}][quantity_received]" type="number" step="0.01" placeholder="Received" required class="px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
            <input name="items[${grnItemIdx}][unit]" placeholder="Unit" class="px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
        </div>
        <input name="items[${grnItemIdx}][remarks]" placeholder="Remarks (optional)" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
    </div>`;
    document.getElementById('grnItems').insertAdjacentHTML('beforeend', html);
    grnItemIdx++;
}
addGrnItem();
</script>
@endsection
