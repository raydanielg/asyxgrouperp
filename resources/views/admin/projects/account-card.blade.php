@extends('layouts.admin')
@section('title', $project->title . ' - Account Card - ' . config('app.name'))
@section('page_title', 'Project Account Card')
@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.projects.show', $project) }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Project</a>
    <div class="flex items-center gap-2">
        <button type="button" onclick="openDrawer('topupDrawer','topupDrawerPanel')" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Request Cash</button>
        <button type="button" onclick="openDrawer('expenseDrawer','expenseDrawerPanel')" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Record Expense</button>
        <button type="button" onclick="openDrawer('transferDrawer','transferDrawerPanel')" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Transfer to Project</button>
    </div>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4 flex items-center justify-between flex-wrap gap-4">
    <div>
        <div class="flex items-center gap-2">
            <span class="font-mono text-sm font-bold text-gray-900">{{ $account->code }}</span>
            <span class="text-xs text-gray-500">{{ $project->title }}</span>
        </div>
        <p class="text-[11px] text-gray-400 mt-0.5">Project cash float — fund requests, expenses, transfers</p>
    </div>
    <div class="text-right">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Card Balance</p>
        <p class="text-xl font-bold text-gray-900">TZS {{ number_format($account->current_balance, 2) }}</p>
    </div>
</div>

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="grid grid-cols-3 divide-x divide-gray-100">
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Funded</p><p class="text-lg font-bold text-emerald-600 mt-1">TZS {{ number_format($project->totalCashCredited(), 2) }}</p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Spent</p><p class="text-lg font-bold text-red-500 mt-1">TZS {{ number_format($project->totalCashDebited(), 2) }}</p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Available</p><p class="text-lg font-bold text-gray-900 mt-1">TZS {{ number_format($account->current_balance, 2) }}</p></div>
    </div>
</div>

@if($account->topupRequests->where('status', 'pending')->count())
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Pending Cash Requests</h3></div>
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

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Account Card Ledger</h3></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100"><th class="px-5 py-2.5 font-semibold">Date</th><th class="px-5 py-2.5 font-semibold">Description</th><th class="px-5 py-2.5 font-semibold text-right">Credit</th><th class="px-5 py-2.5 font-semibold text-right">Debit</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($transactions as $t)
            <tr>
                <td class="px-5 py-3 text-xs text-gray-400">{{ $t->transaction_date->format('d M Y') }}</td>
                <td class="px-5 py-3 text-xs text-gray-700">{{ $t->description }}</td>
                <td class="px-5 py-3 text-xs text-right font-mono {{ $t->type === 'credit' ? 'text-emerald-600' : 'text-gray-300' }}">{{ $t->type === 'credit' ? '+' . number_format($t->amount, 2) : '—' }}</td>
                <td class="px-5 py-3 text-xs text-right font-mono {{ $t->type === 'debit' ? 'text-red-500' : 'text-gray-300' }}">{{ $t->type === 'debit' ? '-' . number_format($t->amount, 2) : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No transactions yet</td></tr>
            @endforelse
            </tbody>
        </table></div>
        @if($transactions->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">{{ $transactions->links() }}</div>
        @endif
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Project Expenses</h3></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100"><th class="px-5 py-2.5 font-semibold">Date</th><th class="px-5 py-2.5 font-semibold">Category</th><th class="px-5 py-2.5 font-semibold">Notes</th><th class="px-5 py-2.5 font-semibold text-right">Amount</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($expenses as $e)
            <tr>
                <td class="px-5 py-3 text-xs text-gray-400">{{ $e->expense_date->format('d M Y') }}</td>
                <td class="px-5 py-3 text-xs text-gray-700">{{ $e->category ?? '—' }}</td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($e->notes, 40) }}</td>
                <td class="px-5 py-3 text-xs text-right font-mono text-red-500">{{ number_format($e->amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No expenses recorded yet</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
</div>

{{-- Request Cash Drawer --}}
<div id="topupDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('topupDrawer','topupDrawerPanel')"></div>
    <div id="topupDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Request Cash</h3><p class="text-[11px] text-slate-400 mt-0.5">{{ $project->title }}</p></div>
            <button type="button" onclick="closeDrawer('topupDrawer','topupDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.projects.account.topup', $project) }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" min="1" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Purpose</label><textarea name="purpose" rows="3" placeholder="Why is cash needed..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <p class="text-[11px] text-gray-400 leading-relaxed">Creates an approval request — funds are credited automatically once approved.</p>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Submit Request</button>
                <button type="button" onclick="closeDrawer('topupDrawer','topupDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Record Expense Drawer --}}
<div id="expenseDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('expenseDrawer','expenseDrawerPanel')"></div>
    <div id="expenseDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Record Expense</h3><p class="text-[11px] text-slate-400 mt-0.5">Balance: TZS {{ number_format($account->current_balance, 2) }}</p></div>
            <button type="button" onclick="closeDrawer('expenseDrawer','expenseDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.projects.account.expense', $project) }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" min="1" max="{{ $account->current_balance }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><input name="category" placeholder="e.g. Site Materials, Transport" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description <span class="text-red-500">*</span></label><input name="description" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <p class="text-[11px] text-gray-400 leading-relaxed">Deducts from the card balance and appears in Project Expenses.</p>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Record</button>
                <button type="button" onclick="closeDrawer('expenseDrawer','expenseDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Transfer Drawer --}}
<div id="transferDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('transferDrawer','transferDrawerPanel')"></div>
    <div id="transferDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Transfer to Project</h3><p class="text-[11px] text-slate-400 mt-0.5">From: {{ $project->title }}</p></div>
            <button type="button" onclick="closeDrawer('transferDrawer','transferDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.projects.account.transfer', $project) }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Destination Project <span class="text-red-500">*</span></label>
                    <select name="destination_project_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select project…</option>
                        @foreach(\App\Models\Project::where('id', '!=', $project->id)->orderBy('title')->get() as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" min="1" max="{{ $account->current_balance }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><input name="notes" placeholder="Reason for transfer..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <p class="text-[11px] text-gray-400 leading-relaxed">Balance will be debited from <strong>{{ $project->title }}</strong> and credited to the destination project's card.</p>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Transfer</button>
                <button type="button" onclick="closeDrawer('transferDrawer','transferDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
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
document.addEventListener('keydown', e => { if (e.key === 'Escape') { ['topupDrawer','expenseDrawer','transferDrawer'].forEach(id => closeDrawer(id, id + 'Panel')); } });
</script>
@endpush
