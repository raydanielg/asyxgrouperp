@extends('layouts.admin')
@section('title', $account->name . ' - ' . config('app.name'))
@section('page_title', $account->name)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.petty-cash.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Petty Cash</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded-xl border p-5 md:col-span-1">
        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Current Balance</p>
        <p class="text-2xl font-bold text-emerald-700">TZS {{ number_format($account->current_balance, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Custodian: {{ $account->custodian?->name ?? 'Unassigned' }}</p>
        <p class="text-[10px] font-mono text-gray-400 mt-1">{{ $account->code }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5 md:col-span-2 flex items-center gap-3">
        <button onclick="document.getElementById('topupModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Request Top-up</button>
        <button onclick="document.getElementById('expenseModal').classList.remove('hidden')" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">Record Expense</button>
    </div>
</div>

@if($account->topupRequests->where('status', 'pending')->count())
<div class="bg-white rounded-xl border overflow-hidden mb-4">
    <div class="px-5 py-3 border-b bg-amber-50/50"><h3 class="text-sm font-bold text-amber-800">Pending Top-up Requests</h3></div>
    <table class="w-full text-sm">
        <tbody>
        @foreach($account->topupRequests->where('status', 'pending') as $t)
        <tr class="border-t border-gray-100"><td class="px-5 py-2 text-xs text-gray-700">{{ $t->requester?->name }}</td><td class="px-5 py-2 text-xs text-gray-500">{{ $t->purpose }}</td><td class="px-5 py-2 text-xs font-semibold text-right">TZS {{ number_format($t->amount, 2) }}</td><td class="px-5 py-2 text-xs text-right"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700">Awaiting Approval</span></td></tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-3 border-b bg-gray-50/50"><h3 class="text-sm font-bold text-gray-800">Transaction Ledger</h3></div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500"><th class="px-5 py-2 font-medium">Date</th><th class="px-5 py-2 font-medium">Description</th><th class="px-5 py-2 font-medium">Category</th><th class="px-5 py-2 font-medium text-right">Credit</th><th class="px-5 py-2 font-medium text-right">Debit</th><th class="px-5 py-2 font-medium text-right">Balance</th></tr></thead>
        <tbody>
        @forelse($transactions as $t)
        <tr class="border-t border-gray-100">
            <td class="px-5 py-2 text-xs text-gray-500">{{ $t->transaction_date->format('d M Y') }}</td>
            <td class="px-5 py-2 text-xs text-gray-700">{{ $t->description }}</td>
            <td class="px-5 py-2 text-xs text-gray-400">{{ $t->category }}</td>
            <td class="px-5 py-2 text-xs text-right font-semibold text-emerald-700">{{ $t->type === 'credit' ? number_format($t->amount, 2) : '' }}</td>
            <td class="px-5 py-2 text-xs text-right font-semibold text-red-600">{{ $t->type === 'debit' ? number_format($t->amount, 2) : '' }}</td>
            <td class="px-5 py-2 text-xs text-right text-gray-700">{{ number_format($t->balance_after, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No transactions yet</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $transactions->links() }}</div>
</div>

<div id="topupModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Request Top-up</h3>
        <form method="POST" action="{{ route('admin.petty-cash.topup', $account) }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" min="1" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Purpose</label><textarea name="purpose" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <p class="text-[11px] text-gray-400">This creates an approval request. Funds are credited automatically once approved.</p>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('topupModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Submit Request</button></div>
        </form>
    </div>
</div>

<div id="expenseModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Record Expense</h3>
        <form method="POST" action="{{ route('admin.petty-cash.expense', $account) }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" min="1" max="{{ $account->current_balance }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description *</label><input name="description" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('expenseModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">Record</button></div>
        </form>
    </div>
</div>
@endsection
