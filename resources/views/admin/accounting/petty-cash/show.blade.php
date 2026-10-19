@extends('layouts.admin')
@section('title', $account->name . ' - ' . config('app.name'))
@section('page_title', $account->name)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.petty-cash.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Petty Cash</a>
</div>

<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4 flex items-center justify-between flex-wrap gap-4">
    <div>
        <div class="flex items-center gap-2">
            <span class="font-mono text-sm font-bold text-gray-900">{{ $account->code }}</span>
            <span class="text-xs text-gray-500">{{ $account->name }}</span>
        </div>
        <p class="text-xs text-gray-400 mt-0.5">Custodian: {{ $account->custodian?->name ?? 'Unassigned' }}</p>
    </div>
    <div class="text-right">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Current Balance</p>
        <p class="text-xl font-bold text-gray-900">TZS {{ number_format($account->current_balance, 2) }}</p>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 px-5 py-3.5 mb-4 flex items-center gap-3">
    <button type="button" onclick="openDrawer('topupDrawer','topupDrawerPanel')" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Request Top-up</button>
    <button type="button" onclick="openDrawer('expenseDrawer','expenseDrawerPanel')" class="px-4 py-2 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Record Expense</button>
</div>

@if($account->topupRequests->where('status', 'pending')->count())
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Pending Top-up Requests</h3></div>
    <table class="w-full text-sm">
        <tbody class="divide-y divide-gray-100">
        @foreach($account->topupRequests->where('status', 'pending') as $t)
        <tr>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $t->requester?->name }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $t->purpose }}</td>
            <td class="px-5 py-3 text-xs font-medium font-mono text-right">{{ number_format($t->amount, 2) }}</td>
            <td class="px-5 py-3 text-xs text-right"><span class="inline-flex items-center gap-1.5 text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:#f59e0b"></span>Awaiting Approval</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Transaction Ledger</h3></div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100"><th class="px-5 py-2.5 font-semibold">Date</th><th class="px-5 py-2.5 font-semibold">Description</th><th class="px-5 py-2.5 font-semibold">Category</th><th class="px-5 py-2.5 font-semibold text-right">Credit</th><th class="px-5 py-2.5 font-semibold text-right">Debit</th><th class="px-5 py-2.5 font-semibold text-right">Balance</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($transactions as $t)
        <tr>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $t->transaction_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $t->description }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $t->category }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono {{ $t->type === 'credit' ? 'text-emerald-600' : 'text-gray-300' }}">{{ $t->type === 'credit' ? number_format($t->amount, 2) : '—' }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono {{ $t->type === 'debit' ? 'text-red-500' : 'text-gray-300' }}">{{ $t->type === 'debit' ? number_format($t->amount, 2) : '—' }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono text-gray-700">{{ number_format($t->balance_after, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-xs text-gray-400">No transactions yet</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($transactions->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $transactions->links() }}</div>
    @endif
</div>

{{-- Top-up Drawer --}}
<div id="topupDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('topupDrawer','topupDrawerPanel')"></div>
    <div id="topupDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Request Top-up</h3><p class="text-[11px] text-slate-400 mt-0.5">{{ $account->name }}</p></div>
            <button type="button" onclick="closeDrawer('topupDrawer','topupDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.petty-cash.topup', $account) }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" min="1" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Purpose</label><textarea name="purpose" rows="3" placeholder="Why is a top-up needed..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <p class="text-[11px] text-gray-400 leading-relaxed">Creates an approval request — funds are credited automatically once approved.</p>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Submit Request</button>
                <button type="button" onclick="closeDrawer('topupDrawer','topupDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Expense Drawer --}}
<div id="expenseDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('expenseDrawer','expenseDrawerPanel')"></div>
    <div id="expenseDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Record Expense</h3><p class="text-[11px] text-slate-400 mt-0.5">Balance: TZS {{ number_format($account->current_balance, 2) }}</p></div>
            <button type="button" onclick="closeDrawer('expenseDrawer','expenseDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.petty-cash.expense', $account) }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" min="1" max="{{ $account->current_balance }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description <span class="text-red-500">*</span></label><input name="description" required placeholder="e.g. Office tea supplies" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Record</button>
                <button type="button" onclick="closeDrawer('expenseDrawer','expenseDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer(drawerId, panelId) {
    document.getElementById(drawerId).classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById(panelId).classList.remove('-translate-x-full'));
}
function closeDrawer(drawerId, panelId) {
    document.getElementById(panelId).classList.add('-translate-x-full');
    setTimeout(() => document.getElementById(drawerId).classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeDrawer('topupDrawer','topupDrawerPanel'); closeDrawer('expenseDrawer','expenseDrawerPanel'); } });
</script>
@endpush
