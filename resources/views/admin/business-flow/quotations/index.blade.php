@extends('layouts.admin')
@section('title', 'Quotations - ' . config('app.name'))
@section('page_title', 'Quotations / Proposals')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-xs text-gray-400">Create quotations before converting leads to deals</p>
    <button type="button" onclick="openQuoDrawer()" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Quotation
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
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Quotation No.</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Client</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Lead</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Date</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Total</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($quotations as $q)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.quotations.show', $q) }}" class="text-xs font-medium font-mono text-gray-900 hover:underline">{{ $q->quotation_number }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-700">{{ $q->client_name }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $q->lead?->full_name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $q->quotation_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-900">{{ number_format($q->total) }} <span class="text-gray-400">TZS</span></td>
            <td class="px-5 py-3.5">
                @php $qd=['draft'=>'#9ca3af','sent'=>'#3b82f6','accepted'=>'#10b981','rejected'=>'#ef4444','expired'=>'#ef4444'][$q->status] ?? '#9ca3af'; @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $qd }}"></span>{{ ucfirst($q->status) }}</span>
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.quotations.show', $q) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <a href="{{ route('admin.quotations.pdf', $q) }}" target="_blank" class="text-xs font-medium text-gray-600 hover:text-gray-900">PDF</a>
                    <form method="POST" action="{{ route('admin.quotations.destroy', $q) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Quotation?', text:'{{ $q->quotation_number }} will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No quotations found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($quotations->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $quotations->links() }}</div>
    @endif
</div>

{{-- ═══ Create Quotation Drawer (slides from left) ═══ --}}
<div id="quoDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeQuoDrawer()"></div>
    <div id="quoDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Quotation</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Create a quotation / proposal</p>
            </div>
            <button type="button" onclick="closeQuoDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.quotations.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Client</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Lead</label>
                    <select name="lead_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        <option value="">— No Lead —</option>
                        @foreach($leads as $l)
                        <option value="{{ $l->id }}">{{ $l->full_name }} ({{ $l->company ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Client Name <span class="text-red-500">*</span></label>
                    <input name="client_name" required placeholder="e.g. Acme Corp" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Client Email</label>
                    <input name="client_email" type="email" placeholder="client@company.co.tz" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Quotation Date <span class="text-red-500">*</span></label>
                        <input name="quotation_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Valid Until</label>
                        <input name="valid_until" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 mb-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Items <span class="text-red-500">*</span></p>
                    <button type="button" onclick="addQuoItem()" class="text-xs font-medium text-gray-500 hover:text-gray-900">+ Add Item</button>
                </div>
                <div id="quoItems" class="space-y-2"></div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">Terms</label>
                <textarea name="terms" rows="2" placeholder="Payment terms, delivery, validity..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Quotation</button>
                <button type="button" onclick="closeQuoDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openQuoDrawer() {
    const d = document.getElementById('quoDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('quoDrawerPanel').classList.remove('-translate-x-full'));
}
function closeQuoDrawer() {
    const p = document.getElementById('quoDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('quoDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeQuoDrawer(); });

let quoItemIdx = 0;
function addQuoItem() {
    const html = `
    <div class="border border-gray-100 rounded p-2.5 space-y-2" id="quoitem-${quoItemIdx}">
        <div class="flex items-center gap-2">
            <input name="items[${quoItemIdx}][description]" placeholder="Item description" required class="flex-1 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
            <button type="button" onclick="document.getElementById('quoitem-${quoItemIdx}').remove()" class="text-gray-300 hover:text-red-500 flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="grid grid-cols-5 gap-2">
            <input name="items[${quoItemIdx}][quantity]" type="number" step="0.01" placeholder="Qty" required class="px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
            <input name="items[${quoItemIdx}][unit]" placeholder="Unit" class="px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
            <input name="items[${quoItemIdx}][unit_price]" type="number" step="0.01" placeholder="Price" required class="px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
            <input name="items[${quoItemIdx}][discount_amount]" type="number" step="0.01" placeholder="Disc." value="0" class="px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
            <input name="items[${quoItemIdx}][tax_percentage]" type="number" step="0.01" placeholder="Tax %" value="0" class="px-2 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400">
        </div>
    </div>`;
    document.getElementById('quoItems').insertAdjacentHTML('beforeend', html);
    quoItemIdx++;
}
addQuoItem();
</script>
@endsection
