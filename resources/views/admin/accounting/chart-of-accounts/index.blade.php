@extends('layouts.admin')
@section('title', 'Chart of Accounts - ' . config('app.name'))
@section('page_title', 'Chart of Accounts')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-xs text-gray-400">General ledger account structure used for Trial Balance and Profit &amp; Loss</p>
    <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Account</button>
</div>

@foreach(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'revenue' => 'Revenue', 'expense' => 'Expenses'] as $type => $label)
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ $label }}</h3></div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100"><th class="px-5 py-2.5 font-semibold">Code</th><th class="px-5 py-2.5 font-semibold">Name</th><th class="px-5 py-2.5 font-semibold">Subtype</th><th class="px-5 py-2.5 font-semibold">Normal Balance</th><th class="px-5 py-2.5 font-semibold">Status</th><th class="px-5 py-2.5 font-semibold text-right">Actions</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse(($accounts[$type] ?? collect()) as $a)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-2.5 text-xs font-mono text-gray-700">{{ $a->code }}</td>
            <td class="px-5 py-2.5 text-xs text-gray-800">{{ $a->parent_id ? '— ' : '' }}{{ $a->name }}</td>
            <td class="px-5 py-2.5 text-xs text-gray-500">{{ $a->subtype ?? '—' }}</td>
            <td class="px-5 py-2.5 text-xs text-gray-500 capitalize">{{ $a->normal_balance }}</td>
            <td class="px-5 py-2.5">
                @if($a->is_system)<span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:#3b82f6"></span>System</span>
                @else<span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:#10b981"></span>Custom</span>@endif
            </td>
            <td class="px-5 py-2.5 text-right">
                @unless($a->is_system)
                <form id="del-coa-{{ $a->id }}" method="POST" action="{{ route('admin.chart-of-accounts.destroy', $a) }}" class="inline">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-coa-{{ $a->id }}', 'Delete this account?')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
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

{{-- New Account Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Account</h3><p class="text-[11px] text-slate-400 mt-0.5">Ledger account</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.chart-of-accounts.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Code <span class="text-red-500">*</span></label><input name="code" required placeholder="e.g. 1010" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 font-mono"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" required placeholder="e.g. Cash" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            <option value="asset">Asset</option><option value="liability">Liability</option><option value="equity">Equity</option><option value="revenue">Revenue</option><option value="expense">Expense</option>
                        </select>
                    </div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Normal Balance <span class="text-red-500">*</span></label>
                        <select name="normal_balance" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            <option value="debit">Debit</option><option value="credit">Credit</option>
                        </select>
                    </div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Subtype</label><input name="subtype" placeholder="e.g. bank, petty_cash, office_expense" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Parent Account</label>
                    <select name="parent_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— None —</option>
                        @foreach($accounts->flatten() as $p)
                        <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="3" placeholder="Optional..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer() {
    document.getElementById('createDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('createDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDrawer() {
    document.getElementById('createDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('createDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
</script>
@endpush
