@extends('layouts.admin')
@section('title', 'Chart of Accounts - ' . config('app.name'))
@section('page_title', 'Chart of Accounts')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">General ledger account structure used for Trial Balance and Profit &amp; Loss</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Account
    </button>
</div>

@foreach(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'revenue' => 'Revenue', 'expense' => 'Expenses'] as $type => $label)
<div class="bg-white rounded-xl border overflow-hidden mb-4">
    <div class="px-5 py-3 border-b bg-gray-50/50"><h3 class="text-sm font-bold text-gray-800">{{ $label }}</h3></div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500"><th class="px-5 py-2 font-medium">Code</th><th class="px-5 py-2 font-medium">Name</th><th class="px-5 py-2 font-medium">Subtype</th><th class="px-5 py-2 font-medium">Normal Balance</th><th class="px-5 py-2 font-medium">Status</th><th class="px-5 py-2 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse(($accounts[$type] ?? collect()) as $a)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-2 text-xs font-mono text-gray-700">{{ $a->code }}</td>
            <td class="px-5 py-2 text-xs text-gray-800">{{ $a->parent_id ? '— ' : '' }}{{ $a->name }}</td>
            <td class="px-5 py-2 text-xs text-gray-500">{{ $a->subtype ?? '—' }}</td>
            <td class="px-5 py-2 text-xs text-gray-500 capitalize">{{ $a->normal_balance }}</td>
            <td class="px-5 py-2">
                @if($a->is_system)<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700">System</span>
                @else<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700">Custom</span>@endif
            </td>
            <td class="px-5 py-2">
                @unless($a->is_system)
                <form id="del-coa-{{ $a->id }}" method="POST" action="{{ route('admin.chart-of-accounts.destroy', $a) }}">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-coa-{{ $a->id }}', 'Delete this account?')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                @endunless
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-6 text-center text-gray-400 text-xs">No {{ strtolower($label) }} accounts yet</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endforeach

<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Account</h3>
        <form method="POST" action="{{ route('admin.chart-of-accounts.store') }}" class="space-y-3">@csrf
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Code *</label><input name="code" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Type *</label>
                    <select name="type" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        <option value="asset">Asset</option><option value="liability">Liability</option><option value="equity">Equity</option><option value="revenue">Revenue</option><option value="expense">Expense</option>
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Normal Balance *</label>
                    <select name="normal_balance" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        <option value="debit">Debit</option><option value="credit">Credit</option>
                    </select>
                </div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Subtype</label><input name="subtype" placeholder="e.g. bank, petty_cash, office_expense" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Parent Account</label>
                <select name="parent_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="">None</option>
                    @foreach($accounts->flatten() as $p)
                    <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
