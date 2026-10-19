@extends('layouts.admin')
@section('title', 'Office Expenses - ' . config('app.name'))
@section('page_title', 'Office Expenses')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-xs text-gray-400">Submit and approve office expenses — amounts under TZS 100,000 are auto-approved</p>
    <button type="button" onclick="openExpDrawer()" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Submit Expense
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
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Expense No.</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Description</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Project</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Category</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Amount</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Date</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Requested By</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($expenses as $e)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $e->expense_number }}</td>
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $e->description }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $e->project?->title ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ ucfirst($e->category ?? '—') }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-900">{{ number_format($e->amount) }} <span class="text-gray-400">TZS</span></td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $e->expense_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $e->requestedBy?->name ?? '—' }}</td>
            <td class="px-5 py-3.5">
                @php $ed=['approved'=>'#10b981','rejected'=>'#ef4444','disbursed'=>'#3b82f6','pending'=>'#f59e0b'][$e->status] ?? '#9ca3af'; @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $ed }}"></span>{{ ucfirst($e->status) }}</span>
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                @if($e->status==='pending')
                <form method="POST" action="{{ route('admin.office-expenses.approve', $e) }}" class="inline" onsubmit="return confirmAction(this, {title:'Approve Expense?', text:'{{ $e->expense_number }} (TZS {{ number_format($e->amount) }}) will be approved.', icon:'question', confirmText:'Yes, approve', color:'#059669'})">@csrf<button class="text-xs font-medium text-gray-600 hover:text-gray-900">Approve</button></form>
                <form method="POST" action="{{ route('admin.office-expenses.reject', $e) }}" class="inline" onsubmit="return confirmAction(this, {title:'Reject Expense?', text:'{{ $e->expense_number }} will be marked as rejected.', icon:'warning', confirmText:'Yes, reject', color:'#d97706'})">@csrf<button class="text-xs font-medium text-amber-600 hover:text-amber-800">Reject</button></form>
                @endif
                <form method="POST" action="{{ route('admin.office-expenses.destroy', $e) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Expense?', text:'{{ $e->expense_number }} will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-5 py-10 text-center text-gray-400 text-xs">No office expenses found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($expenses->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $expenses->links() }}</div>
    @endif
</div>

{{-- ═══ Submit Expense Drawer (slides from left) ═══ --}}
<div id="expDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeExpDrawer()"></div>
    <div id="expDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">Submit Office Expense</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Under TZS 100,000 → auto-approved</p>
            </div>
            <button type="button" onclick="closeExpDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.office-expenses.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Expense</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description <span class="text-red-500">*</span></label>
                    <input name="description" required placeholder="e.g. Office supplies — printer cartridges" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
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
                        <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                        <select name="category" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                            @foreach(['transport'=>'Transport','supplies'=>'Supplies','meals'=>'Meals','utilities'=>'Utilities','misc'=>'Miscellaneous'] as $k=>$v)
                            <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
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
                        <label class="block text-xs font-medium text-gray-600 mb-1">Expense Date <span class="text-red-500">*</span></label>
                        <input name="expense_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                            @foreach(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','mobile_money'=>'Mobile Money'] as $k=>$v)
                            <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Receipt Number</label>
                        <input name="receipt_number" placeholder="e.g. RCP-001" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                    <textarea name="notes" rows="2" placeholder="Additional details..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Submit Expense</button>
                <button type="button" onclick="closeExpDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openExpDrawer() {
    const d = document.getElementById('expDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('expDrawerPanel').classList.remove('-translate-x-full'));
}
function closeExpDrawer() {
    const p = document.getElementById('expDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('expDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeExpDrawer(); });
</script>
@endsection
