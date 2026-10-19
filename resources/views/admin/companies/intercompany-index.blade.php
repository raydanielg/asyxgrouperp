@extends('layouts.admin')
@section('title', 'Intercompany Transactions - ' . config('app.name'))
@section('page_title', 'Intercompany Transactions')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-xs text-gray-400">Track transactions between group companies</p>
    <button type="button" onclick="openIctDrawer()" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Transaction
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
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Number</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">From → To</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Type</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Amount</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Date</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($transactions as $ict)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.intercompany.show', $ict) }}" class="text-xs font-medium font-mono text-gray-900 hover:underline">{{ $ict->transaction_number }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-500 font-mono">{{ $ict->fromCompany?->short_code ?? '—' }} <span class="text-gray-300">→</span> {{ $ict->toCompany?->short_code ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-600 capitalize">{{ str_replace('_', ' ', $ict->type) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-900">{{ number_format($ict->amount, 0) }} <span class="text-gray-400">{{ $ict->currency }}</span></td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $ict->transaction_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5">
                @php $dot = $ict->status === 'completed' ? '#10b981' : ($ict->status === 'eliminated' ? '#9ca3af' : '#f59e0b'); @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-700"><span style="width:6px;height:6px;border-radius:99px;background:{{ $dot }}"></span>{{ ucfirst($ict->status) }}</span>
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.intercompany.show', $ict) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    @if($ict->status === 'completed')
                    <form method="POST" action="{{ route('admin.intercompany.eliminate', $ict) }}" class="inline" onsubmit="return confirmAction(this, {title:'Eliminate Transaction?', text:'{{ $ict->transaction_number }} will be marked as eliminated for consolidation.', icon:'question', confirmText:'Yes, eliminate', color:'#d97706'})">@csrf<button class="text-xs font-medium text-amber-600 hover:text-amber-800">Eliminate</button></form>
                    @endif
                    <form method="POST" action="{{ route('admin.intercompany.destroy', $ict) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Transaction?', text:'{{ $ict->transaction_number }} will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No intercompany transactions yet</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($transactions->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $transactions->links() }}</div>
    @endif
</div>

{{-- ═══ New Transaction Drawer (slides from left) ═══ --}}
<div id="ictDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeIctDrawer()"></div>
    <div id="ictDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Transaction</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Between ASYX group companies</p>
            </div>
            <button type="button" onclick="closeIctDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.intercompany.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">From Company <span class="text-red-500">*</span></label>
                    <select name="from_company_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        <option value="">— Select —</option>
                        @foreach($companies as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">To Company <span class="text-red-500">*</span></label>
                    <select name="to_company_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        <option value="">— Select —</option>
                        @foreach($companies as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        <option value="invoice">Invoice</option>
                        <option value="transfer">Transfer</option>
                        <option value="shared_service">Shared Service</option>
                        <option value="shared_staff">Shared Staff</option>
                        <option value="loan">Loan</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" required min="0" step="0.01" placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Currency <span class="text-red-500">*</span></label>
                        <input type="text" name="currency" value="TZS" required maxlength="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Transaction Date <span class="text-red-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="What is this transaction for?" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 mb-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Line Items (optional)</p>
                    <button type="button" onclick="addIctLine()" class="text-xs font-medium text-gray-500 hover:text-gray-900">+ Add Line</button>
                </div>
                <div id="ictLines" class="space-y-2"></div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Transaction</button>
                <button type="button" onclick="closeIctDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openIctDrawer() {
    const d = document.getElementById('ictDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('ictDrawerPanel').classList.remove('-translate-x-full'));
}
function closeIctDrawer() {
    const p = document.getElementById('ictDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('ictDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeIctDrawer(); });

let ictLineIndex = 0;
function addIctLine() {
    ictLineIndex++;
    const html = `
    <div class="grid grid-cols-12 gap-2 items-start" id="ictline-${ictLineIndex}">
        <input type="text" name="lines[${ictLineIndex}][description]" placeholder="Description" class="col-span-5 px-2 py-1.5 text-xs border border-gray-200 rounded outline-none focus:border-gray-400">
        <input type="number" name="lines[${ictLineIndex}][quantity]" placeholder="Qty" step="0.01" class="col-span-2 px-2 py-1.5 text-xs border border-gray-200 rounded outline-none focus:border-gray-400">
        <input type="number" name="lines[${ictLineIndex}][unit_price]" placeholder="Unit price" step="0.01" class="col-span-3 px-2 py-1.5 text-xs border border-gray-200 rounded outline-none focus:border-gray-400">
        <button type="button" onclick="document.getElementById('ictline-${ictLineIndex}').remove()" class="col-span-2 text-xs text-gray-400 hover:text-red-600 pt-1.5">Remove</button>
    </div>`;
    document.getElementById('ictLines').insertAdjacentHTML('beforeend', html);
}
</script>
@endsection
