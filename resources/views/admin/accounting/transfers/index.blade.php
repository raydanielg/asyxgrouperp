@extends('layouts.admin')
@section('title', 'Transfers - ' . config('app.name'))
@section('page_title', 'Bank Transfers')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Record transfers between bank accounts</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Transfer
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Transfer #</th><th class="px-5 py-3 font-medium">From</th><th class="px-5 py-3 font-medium">To</th><th class="px-5 py-3 font-medium">Amount</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($transfers as $t)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $t->transfer_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $t->fromAccount?->account_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $t->toAccount?->account_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($t->amount) }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $t->transfer_date->format('d M Y') }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700">{{ ucfirst($t->status) }}</span></td>
            <td class="px-5 py-3"><form id="del-trf-{{ $t->id }}" method="POST" action="{{ route('admin.acc-transfers.destroy', $t) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-trf-{{ $t->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        </tr>@empty<tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No transfers found</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $transfers->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Bank Transfer</h3>
        <form method="POST" action="{{ route('admin.acc-transfers.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">From Account *</label><select name="from_account_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->account_name }} (TZS {{ number_format($a->current_balance) }})</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">To Account *</label><select name="to_account_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->account_name }}</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" required value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Transfer Date *</label><input name="transfer_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Transfer</button></div>
        </form>
    </div>
</div>
@endsection
