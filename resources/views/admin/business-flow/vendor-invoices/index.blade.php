@extends('layouts.admin')
@section('title', 'Vendor Invoices - ' . config('app.name'))
@section('page_title', 'Vendor Invoices')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-xs text-gray-400">Track vendor invoices and payments</p>
    <button type="button" onclick="openVinvDrawer()" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Vendor Invoice
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
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Invoice No.</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Supplier</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Project</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Total</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Paid</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Balance</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($invoices as $v)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.vendor-invoices.show', $v) }}" class="text-xs font-medium font-mono text-gray-900 hover:underline">{{ $v->vendor_invoice_number }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-700">{{ $v->supplier?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $v->project?->title ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-900">{{ number_format($v->total) }}</td>
            <td class="px-5 py-3.5 text-xs text-right text-emerald-600">{{ number_format($v->amount_paid) }}</td>
            <td class="px-5 py-3.5 text-xs text-right {{ $v->balance > 0 ? 'text-red-500' : 'text-gray-400' }}">{{ number_format($v->balance) }}</td>
            <td class="px-5 py-3.5">
                @php $vd=['paid'=>'#10b981','partially_paid'=>'#f59e0b','unpaid'=>'#ef4444'][$v->status] ?? '#9ca3af'; @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $vd }}"></span>{{ ucfirst(str_replace('_',' ',$v->status)) }}</span>
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.vendor-invoices.show', $v) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <form method="POST" action="{{ route('admin.vendor-invoices.destroy', $v) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Invoice?', text:'{{ $v->vendor_invoice_number }} will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No vendor invoices found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($invoices->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $invoices->links() }}</div>
    @endif
</div>

{{-- ═══ Add Vendor Invoice Drawer (slides from left) ═══ --}}
<div id="vinvDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeVinvDrawer()"></div>
    <div id="vinvDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Vendor Invoice</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Record a supplier invoice</p>
            </div>
            <button type="button" onclick="closeVinvDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.vendor-invoices.store') }}" class="flex-1 overflow-y-auto p-5">
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
                    <label class="block text-xs font-medium text-gray-600 mb-1">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        <option value="">— No Project —</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Supplier Invoice Ref</label>
                    <input name="supplier_invoice_ref" placeholder="Supplier's own invoice number" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
            </div>

            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Amounts (TZS)</p>
            <div class="space-y-3 mb-5">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Invoice Date <span class="text-red-500">*</span></label>
                        <input name="invoice_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label>
                        <input name="due_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Subtotal <span class="text-red-500">*</span></label>
                        <input name="subtotal" type="number" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tax Amount</label>
                        <input name="tax_amount" type="number" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Total <span class="text-red-500">*</span></label>
                    <input name="total" type="number" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                <textarea name="notes" rows="2" placeholder="Payment notes, references..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Invoice</button>
                <button type="button" onclick="closeVinvDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openVinvDrawer() {
    const d = document.getElementById('vinvDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('vinvDrawerPanel').classList.remove('-translate-x-full'));
}
function closeVinvDrawer() {
    const p = document.getElementById('vinvDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('vinvDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeVinvDrawer(); });
</script>
@endsection
