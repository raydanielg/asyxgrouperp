@extends('layouts.admin')
@section('title', 'Delivery Notes - ' . config('app.name'))
@section('page_title', 'Delivery Notes')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-xs text-gray-400">Track delivery notes from vendors</p>
    <button type="button" onclick="openDnDrawer()" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Delivery Note
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
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">DN No.</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">LPO</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Supplier</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Delivery Date</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Delivered By</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Vehicle</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($deliveryNotes as $d)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.delivery-notes.show', $d) }}" class="text-xs font-medium font-mono text-gray-900 hover:underline">{{ $d->delivery_note_number }}</a></td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $d->lpo?->lpo_number ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-700">{{ $d->supplier?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $d->delivery_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $d->delivered_by ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-400">{{ $d->vehicle_number ?? '—' }}</td>
            <td class="px-5 py-3.5">
                @php $dd=['verified'=>'#10b981','pending_verification'=>'#f59e0b','delivered'=>'#3b82f6'][$d->status] ?? '#9ca3af'; @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $dd }}"></span>{{ ucfirst(str_replace('_',' ',$d->status)) }}</span>
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.delivery-notes.show', $d) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <form method="POST" action="{{ route('admin.delivery-notes.destroy', $d) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Delivery Note?', text:'{{ $d->delivery_note_number }} will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No delivery notes found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($deliveryNotes->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $deliveryNotes->links() }}</div>
    @endif
</div>

{{-- ═══ Add Delivery Note Drawer (slides from left) ═══ --}}
<div id="dnDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDnDrawer()"></div>
    <div id="dnDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Delivery Note</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Record a vendor delivery</p>
            </div>
            <button type="button" onclick="closeDnDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.delivery-notes.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Reference</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">LPO</label>
                    <select name="lpo_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        <option value="">— No LPO —</option>
                        @foreach($lpos as $l)
                        <option value="{{ $l->id }}">{{ $l->lpo_number }}</option>
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
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">GRN</label>
                    <select name="grn_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"><option value="">— No GRN —</option></select>
                </div>
            </div>

            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Delivery</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Delivery Date <span class="text-red-500">*</span></label>
                    <input name="delivery_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Delivered By</label>
                        <input name="delivered_by" placeholder="Driver / courier" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Received By</label>
                        <input name="received_by" placeholder="Storekeeper" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Vehicle Number</label>
                    <input name="vehicle_number" placeholder="e.g. T 123 ABC" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                <textarea name="notes" rows="2" placeholder="Condition, remarks..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Delivery Note</button>
                <button type="button" onclick="closeDnDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDnDrawer() {
    const d = document.getElementById('dnDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('dnDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDnDrawer() {
    const p = document.getElementById('dnDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('dnDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDnDrawer(); });
</script>
@endsection
