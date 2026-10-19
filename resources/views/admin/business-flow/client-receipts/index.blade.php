@extends('layouts.admin')
@section('title', 'Client Receipts - ' . config('app.name'))
@section('page_title', 'Client Payment Receipts')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-xs text-gray-400">Record payments received from clients</p>
    <button type="button" onclick="openCrDrawer()" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Receipt
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
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Receipt No.</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Client</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Project</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Amount</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Date</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Method</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Reference</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($receipts as $r)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><button type="button" onclick="openViewDrawer({{ $r->id }})" class="text-xs font-medium font-mono text-gray-900 hover:underline">{{ $r->receipt_number }}</button></td>
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $r->client_name }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $r->project?->title ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-emerald-600">+{{ number_format($r->amount) }} <span class="text-gray-400 font-normal">TZS</span></td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $r->receipt_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-600">{{ ucfirst(str_replace('_',' ',$r->payment_method)) }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-400">{{ $r->reference_number ?? '—' }}</td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <button type="button" onclick="openViewDrawer({{ $r->id }})" class="text-xs font-medium text-gray-600 hover:text-gray-900">Receipt</button>
                    <form method="POST" action="{{ route('admin.client-receipts.destroy', $r) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Receipt?', text:'{{ $r->receipt_number }} will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No client receipts found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($receipts->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $receipts->links() }}</div>
    @endif
</div>

{{-- ═══ Add Receipt Drawer (slides from left) ═══ --}}
<div id="crDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeCrDrawer()"></div>
    <div id="crDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Client Receipt</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Record a payment received</p>
            </div>
            <button type="button" onclick="closeCrDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.client-receipts.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Client</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Client Name <span class="text-red-500">*</span></label>
                    <input name="client_name" required placeholder="e.g. Mushi General Traders" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        <option value="">— No Project —</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Payment</p>
            <div class="space-y-3 mb-5">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Amount (TZS) <span class="text-red-500">*</span></label>
                        <input name="amount" type="number" required min="0" step="0.01" placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Receipt Date <span class="text-red-500">*</span></label>
                        <input name="receipt_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Method</label>
                        <select name="payment_method" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                            @foreach(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','mobile_money'=>'Mobile Money'] as $k=>$v)
                            <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Reference No.</label>
                        <input name="reference_number" placeholder="Transaction ref" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Invoice Reference</label>
                    <input name="invoice_reference" placeholder="e.g. INV-0001" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                    <textarea name="notes" rows="2" placeholder="Payment notes..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Receipt</button>
                <button type="button" onclick="closeCrDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ View Receipt Drawer (slides from left) ═══ --}}
@php
$crMap = $receipts->mapWithKeys(function ($r) {
    return [$r->id => [
        'num' => $r->receipt_number,
        'client' => $r->client_name,
        'project' => $r->project?->title ?? '—',
        'amount' => number_format($r->amount) . ' TZS',
        'date' => $r->receipt_date->format('d M Y'),
        'method' => ucfirst(str_replace('_',' ',$r->payment_method)),
        'ref' => $r->reference_number ?? '—',
        'invref' => $r->invoice_reference ?? '—',
        'receivedBy' => $r->receivedBy?->name ?? '—',
        'notes' => $r->notes ?? '—',
    ]];
})->toArray();
@endphp
<div id="viewDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeViewDrawer()"></div>
    <div id="viewDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">Payment Receipt</h3>
                <p class="text-[11px] text-slate-400 mt-0.5" id="cr-num">—</p>
            </div>
            <button type="button" onclick="closeViewDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5">
            <div class="border border-gray-200 rounded-md">
                <div class="px-5 py-4 border-b border-gray-200 text-center">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Received</p>
                    <p class="text-xl font-bold text-emerald-600 mt-1" id="cr-amount">—</p>
                    <p class="text-[11px] text-gray-500 mt-0.5" id="cr-client">—</p>
                </div>
                <dl class="p-5 space-y-3">
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Receipt No.</dt><dd class="text-xs font-mono text-gray-800" id="cr-rnum">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Date</dt><dd class="text-xs text-gray-800" id="cr-date">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Project</dt><dd class="text-xs text-gray-800 text-right" id="cr-project">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Method</dt><dd class="text-xs text-gray-800" id="cr-method">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Reference</dt><dd class="text-xs font-mono text-gray-800" id="cr-ref">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Invoice Ref</dt><dd class="text-xs font-mono text-gray-800" id="cr-invref">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Received by</dt><dd class="text-xs text-gray-800" id="cr-by">—</dd></div>
                </dl>
                <div class="px-5 py-3 border-t border-dashed border-gray-200">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Notes</p>
                    <p class="text-xs text-gray-600" id="cr-notes">—</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const crMap = @json($crMap);

function openCrDrawer() {
    const d = document.getElementById('crDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('crDrawerPanel').classList.remove('-translate-x-full'));
}
function closeCrDrawer() {
    const p = document.getElementById('crDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('crDrawer').classList.add('hidden'), 300);
}

function openViewDrawer(id) {
    const r = crMap[id];
    if (!r) return;
    document.getElementById('cr-num').textContent = r.num;
    document.getElementById('cr-amount').textContent = r.amount;
    document.getElementById('cr-client').textContent = r.client;
    document.getElementById('cr-rnum').textContent = r.num;
    document.getElementById('cr-date').textContent = r.date;
    document.getElementById('cr-project').textContent = r.project;
    document.getElementById('cr-method').textContent = r.method;
    document.getElementById('cr-ref').textContent = r.ref;
    document.getElementById('cr-invref').textContent = r.invref;
    document.getElementById('cr-by').textContent = r.receivedBy;
    document.getElementById('cr-notes').textContent = r.notes;
    const d = document.getElementById('viewDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('viewDrawerPanel').classList.remove('-translate-x-full'));
}
function closeViewDrawer() {
    const p = document.getElementById('viewDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('viewDrawer').classList.add('hidden'), 300);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeCrDrawer(); closeViewDrawer(); }
});
</script>
@endsection
