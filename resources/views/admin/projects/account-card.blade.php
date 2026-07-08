@extends('layouts.admin')
@section('title', $project->title . ' - Account Card - ' . config('app.name'))
@section('page_title', 'Project Account Card')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.projects.show', $project) }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to {{ $project->title }}</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded-xl border p-5">
        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Account Card Balance</p>
        <p class="text-2xl font-bold text-emerald-700">TZS {{ number_format($account->current_balance, 2) }}</p>
        <p class="text-[10px] font-mono text-gray-400 mt-1">{{ $account->code }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Total Funded</p>
        <p class="text-xl font-bold text-gray-800">TZS {{ number_format($project->totalCashCredited(), 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Total Spent</p>
        <p class="text-xl font-bold text-red-600">TZS {{ number_format($project->totalCashDebited(), 2) }}</p>
    </div>
</div>

<div class="flex items-center gap-3 mb-4">
    <button onclick="document.getElementById('topupModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Request Cash</button>
    <button onclick="document.getElementById('expenseModal').classList.remove('hidden')" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">Record Expense</button>
</div>

@if($account->topupRequests->where('status', 'pending')->count())
<div class="bg-white rounded-xl border overflow-hidden mb-4">
    <div class="px-5 py-3 border-b bg-amber-50/50"><h3 class="text-sm font-bold text-amber-800">Pending Cash Requests</h3></div>
    <table class="w-full text-sm">
        <tbody>
        @foreach($account->topupRequests->where('status', 'pending') as $t)
        <tr class="border-t border-gray-100"><td class="px-5 py-2 text-xs text-gray-700">{{ $t->requester?->name }}</td><td class="px-5 py-2 text-xs text-gray-500">{{ $t->purpose }}</td><td class="px-5 py-2 text-xs font-semibold text-right">TZS {{ number_format($t->amount, 2) }}</td><td class="px-5 py-2 text-xs text-right"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700">Awaiting Approval</span></td></tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h3 class="text-sm font-bold text-gray-800">Account Card Ledger</h3></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Date</th><th class="px-4 py-2 font-medium">Description</th><th class="px-4 py-2 font-medium text-right">Credit</th><th class="px-4 py-2 font-medium text-right">Debit</th></tr></thead>
            <tbody>
            @forelse($transactions as $t)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs text-gray-500">{{ $t->transaction_date->format('d M Y') }}</td>
                <td class="px-4 py-2 text-xs text-gray-700">{{ $t->description }}</td>
                <td class="px-4 py-2 text-xs text-right font-semibold text-emerald-700">{{ $t->type === 'credit' ? number_format($t->amount, 2) : '' }}</td>
                <td class="px-4 py-2 text-xs text-right font-semibold text-red-600">{{ $t->type === 'debit' ? number_format($t->amount, 2) : '' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No transactions yet</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="px-4 py-3 border-t">{{ $transactions->links() }}</div>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h3 class="text-sm font-bold text-gray-800">Project Expenses</h3></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Date</th><th class="px-4 py-2 font-medium">Category</th><th class="px-4 py-2 font-medium">Notes</th><th class="px-4 py-2 font-medium text-right">Amount</th></tr></thead>
            <tbody>
            @forelse($expenses as $e)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs text-gray-500">{{ $e->expense_date->format('d M Y') }}</td>
                <td class="px-4 py-2 text-xs text-gray-700">{{ $e->category ?? '—' }}</td>
                <td class="px-4 py-2 text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($e->notes, 40) }}</td>
                <td class="px-4 py-2 text-xs text-right font-semibold text-red-600">{{ number_format($e->amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No expenses recorded for this project yet</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
</div>

<div id="topupModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Request Cash for {{ $project->title }}</h3>
        <form method="POST" action="{{ route('admin.projects.account.topup', $project) }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" min="1" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Purpose</label><textarea name="purpose" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <p class="text-[11px] text-gray-400">This creates an approval request. Funds are credited to the project's account card automatically once approved.</p>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('topupModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Submit Request</button></div>
        </form>
    </div>
</div>

<div id="expenseModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Record Expense</h3>
        <form method="POST" action="{{ route('admin.projects.account.expense', $project) }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" min="1" max="{{ $account->current_balance }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><input name="category" placeholder="e.g. Site Materials, Transport" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description *</label><input name="description" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <p class="text-[11px] text-gray-400">This deducts from the account card balance and appears in Project Expenses and the Expenses report.</p>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('expenseModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">Record</button></div>
        </form>
    </div>
</div>
@endsection
