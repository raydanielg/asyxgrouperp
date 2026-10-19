@extends('layouts.admin')
@section('title', 'Bank Accounts - ' . config('app.name'))
@section('page_title', 'Bank Accounts')
@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded bg-emerald-50 text-emerald-700 text-xs border border-emerald-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded bg-red-50 text-red-700 text-xs border border-red-100">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $accounts->total() }} accounts</span>
        <button type="button" onclick="openDrawer('createDrawer','createDrawerPanel')" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Account</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Account</th>
                    <th class="px-5 py-3 font-semibold">Bank</th>
                    <th class="px-5 py-3 font-semibold">Number</th>
                    <th class="px-5 py-3 font-semibold">Currency</th>
                    <th class="px-5 py-3 font-semibold text-right">Balance</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($accounts as $a)
            <tr class="hover:bg-gray-50/60 bank-row"
                data-id="{{ $a->id }}"
                data-name="{{ $a->account_name }}"
                data-bank="{{ $a->bank_name }}"
                data-number="{{ $a->account_number }}"
                data-branch="{{ $a->branch }}"
                data-balance="{{ $a->current_balance }}"
                data-currency="{{ $a->currency }}"
                data-active="{{ $a->is_active ? 1 : 0 }}">
                <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $a->account_name }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-600">{{ $a->bank_name }}{{ $a->branch ? ' · ' . $a->branch : '' }}</td>
                <td class="px-5 py-3.5 text-xs font-mono text-gray-500">•••• {{ substr($a->account_number, -4) }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $a->currency ?? 'USD' }}</td>
                <td class="px-5 py-3.5 text-xs font-mono text-gray-900 text-right balance-cell" data-balance="{{ $a->current_balance }}" onclick="toggleBalance(this)" style="cursor:pointer;" title="Click to reveal/hide">
                    <span class="hidden-amount">TZS {{ number_format($a->current_balance) }}</span>
                </td>
                <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $a->is_active ? '#10b981' : '#9ca3af' }}"></span>{{ $a->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-4">
                        <button onclick="openDeposit(this)" class="text-xs font-medium text-gray-600 hover:text-gray-900">Deposit</button>
                        <button onclick="openWithdraw(this)" class="text-xs font-medium text-amber-600 hover:text-amber-800">Withdraw</button>
                        <button onclick="openHistory(this)" class="text-xs font-medium text-gray-600 hover:text-gray-900">History</button>
                        <button onclick="openEdit(this)" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</button>
                        <button onclick="deleteAccount(this)" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No bank accounts yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($accounts->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $accounts->links() }}</div>
    @endif
</div>

{{-- Create Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('createDrawer','createDrawerPanel')"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Add Bank Account</h3><p class="text-[11px] text-slate-400 mt-0.5">Company account</p></div>
            <button type="button" onclick="closeDrawer('createDrawer','createDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.bank-accounts.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Account Name <span class="text-red-500">*</span></label><input name="account_name" required placeholder="e.g. Operations Account" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Account Number <span class="text-red-500">*</span></label><input name="account_number" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 font-mono"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Bank Name <span class="text-red-500">*</span></label><input name="bank_name" required placeholder="e.g. NMB" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Branch</label><input name="branch" placeholder="e.g. Corporate" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><input name="currency" value="TZS" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Opening Balance</label><input name="opening_balance" type="number" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Current Balance</label><input name="current_balance" type="number" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="flex items-center gap-2"><input type="checkbox" name="is_active" checked class="w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400"><label class="text-xs text-gray-600">Active</label></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create</button>
                <button type="button" onclick="closeDrawer('createDrawer','createDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Deposit Drawer --}}
<div id="depositDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('depositDrawer','depositDrawerPanel')"></div>
    <div id="depositDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Deposit</h3><p class="text-[11px] text-slate-400 mt-0.5" id="depositAccountName"></p></div>
            <button type="button" onclick="closeDrawer('depositDrawer','depositDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="addBalanceForm" method="POST" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" min="0.01" required placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><input name="description" placeholder="Optional note..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors">Deposit</button>
                <button type="button" onclick="closeDrawer('depositDrawer','depositDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Withdraw Drawer --}}
<div id="withdrawDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('withdrawDrawer','withdrawDrawerPanel')"></div>
    <div id="withdrawDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Withdraw</h3><p class="text-[11px] text-slate-400 mt-0.5" id="withdrawAccountName"></p></div>
            <button type="button" onclick="closeDrawer('withdrawDrawer','withdrawDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="deductBalanceForm" method="POST" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" min="0.01" required placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><input name="description" placeholder="Optional note..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-amber-600 text-white text-sm font-medium rounded hover:bg-amber-700 transition-colors">Withdraw</button>
                <button type="button" onclick="closeDrawer('withdrawDrawer','withdrawDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Drawer --}}
<div id="editDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('editDrawer','editDrawerPanel')"></div>
    <div id="editDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Edit Bank Account</h3><p class="text-[11px] text-slate-400 mt-0.5" id="editAccountName"></p></div>
            <button type="button" onclick="closeDrawer('editDrawer','editDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editForm" method="POST" class="flex-1 overflow-y-auto p-5">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Account Name <span class="text-red-500">*</span></label><input name="account_name" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Account Number <span class="text-red-500">*</span></label><input name="account_number" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 font-mono"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Bank Name <span class="text-red-500">*</span></label><input name="bank_name" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Branch</label><input name="branch" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><input name="currency" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Current Balance</label><input name="current_balance" type="number" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="flex items-center gap-2"><input type="checkbox" name="is_active" class="w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400"><label class="text-xs text-gray-600">Active</label></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save Changes</button>
                <button type="button" onclick="closeDrawer('editDrawer','editDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- History Drawer --}}
<div id="historyDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('historyDrawer','historyDrawerPanel')"></div>
    <div id="historyDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Transaction History</h3><p class="text-[11px] text-slate-400 mt-0.5" id="txnAccountName"></p></div>
            <button type="button" onclick="closeDrawer('historyDrawer','historyDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="txnBody" class="flex-1 overflow-y-auto p-5"><p class="text-center text-xs text-gray-400 py-8">Loading transactions...</p></div>
    </div>
</div>

@push('scripts')
<script>
const addBalanceUrl = '{{ route("admin.bank-accounts.add-balance", "__ID__") }}';
const deductBalanceUrl = '{{ route("admin.bank-accounts.deduct-balance", "__ID__") }}';
const transactionsUrl = '{{ route("admin.bank-accounts.transactions", "__ID__") }}';
const updateUrl = '{{ route("admin.bank-accounts.update", "__ID__") }}';
const destroyUrl = '{{ route("admin.bank-accounts.destroy", "__ID__") }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

function openDrawer(drawerId, panelId) {
    document.getElementById(drawerId).classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById(panelId).classList.remove('-translate-x-full'));
}
function closeDrawer(drawerId, panelId) {
    document.getElementById(panelId).classList.add('-translate-x-full');
    setTimeout(() => document.getElementById(drawerId).classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') {
    ['create','deposit','withdraw','edit','history'].forEach(p => closeDrawer(p + 'Drawer', p + 'DrawerPanel'));
}});

function rowData(btn) {
    return btn.closest('.bank-row').dataset;
}

function openDeposit(btn) {
    const d = rowData(btn);
    document.getElementById('depositAccountName').textContent = d.name + ' — ' + d.bank + ' (•••• ' + d.number.slice(-4) + ')';
    document.getElementById('addBalanceForm').action = addBalanceUrl.replace('__ID__', d.id);
    openDrawer('depositDrawer', 'depositDrawerPanel');
}

function openWithdraw(btn) {
    const d = rowData(btn);
    document.getElementById('withdrawAccountName').textContent = d.name + ' — ' + d.bank + ' — Balance: TZS ' + Number(d.balance).toLocaleString();
    document.getElementById('deductBalanceForm').action = deductBalanceUrl.replace('__ID__', d.id);
    openDrawer('withdrawDrawer', 'withdrawDrawerPanel');
}

function openHistory(btn) {
    const d = rowData(btn);
    document.getElementById('txnAccountName').textContent = d.name + ' — ' + d.bank;
    document.getElementById('txnBody').innerHTML = '<p class="text-center text-xs text-gray-400 py-8">Loading transactions...</p>';
    openDrawer('historyDrawer', 'historyDrawerPanel');
    fetch(transactionsUrl.replace('__ID__', d.id), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json()).then(res => renderTransactions(res))
        .catch(() => { document.getElementById('txnBody').innerHTML = '<p class="text-center text-xs text-red-400 py-8">Failed to load transactions.</p>'; });
}

function openEdit(btn) {
    const d = rowData(btn);
    document.getElementById('editAccountName').textContent = d.name + ' — ' + d.bank;
    const form = document.getElementById('editForm');
    form.action = updateUrl.replace('__ID__', d.id);
    form.querySelector('[name="account_name"]').value = d.name;
    form.querySelector('[name="bank_name"]').value = d.bank;
    form.querySelector('[name="account_number"]').value = d.number;
    form.querySelector('[name="branch"]').value = d.branch || '';
    form.querySelector('[name="current_balance"]').value = d.balance;
    form.querySelector('[name="currency"]').value = d.currency;
    form.querySelector('[name="is_active"]').checked = d.active === '1';
    openDrawer('editDrawer', 'editDrawerPanel');
}

function deleteAccount(btn) {
    const d = rowData(btn);
    Swal.fire({
        title: 'Delete Account?', text: d.name + ' (' + d.bank + ') will be permanently removed.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete', cancelButtonText: 'Cancel', reverseButtons: true
    }).then((r) => {
        if (r.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST'; form.action = destroyUrl.replace('__ID__', d.id);
            const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = csrfToken;
            const method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
            form.appendChild(csrf); form.appendChild(method); document.body.appendChild(form); form.submit();
        }
    });
}

function toggleBalance(el) {
    const span = el.querySelector('.hidden-amount');
    if (span.dataset.hidden === '1') {
        span.textContent = 'TZS ' + Number(el.dataset.balance).toLocaleString();
        span.dataset.hidden = '0';
    } else {
        span.textContent = '••••••••';
        span.dataset.hidden = '1';
    }
}

document.querySelectorAll('.balance-cell').forEach(el => {
    const span = el.querySelector('.hidden-amount');
    span.textContent = '••••••••';
    span.dataset.hidden = '1';
});

function renderTransactions(res) {
    const txns = res.transactions || [];
    const account = res.account || {};
    const body = document.getElementById('txnBody');
    if (txns.length === 0) {
        body.innerHTML = '<p class="text-center text-xs text-gray-400 py-10">No transactions found for this account.</p>';
        return;
    }
    let html = '<div class="mb-4 pb-3 border-b border-gray-100 flex items-center justify-between"><div><p class="text-[10px] text-gray-400 uppercase font-bold">Current Balance</p><p class="text-base font-bold text-gray-900">TZS ' + Number(account.current_balance || 0).toLocaleString() + '</p></div><div class="text-right"><p class="text-[10px] text-gray-400 uppercase font-bold">Transactions</p><p class="text-base font-bold text-gray-700">' + txns.length + '</p></div></div>';
    html += '<div class="space-y-1">';
    txns.forEach(t => {
        const isCredit = t.type === 'credit';
        const color = isCredit ? 'emerald' : 'red';
        const sign = isCredit ? '+' : '−';
        html += '<div class="flex items-center justify-between py-2.5 border-b border-gray-50"><div><p class="text-xs font-medium text-gray-900">' + escapeHtml(t.label) + '</p><p class="text-[10px] text-gray-400">' + escapeHtml(t.description || '') + (t.description ? ' · ' : '') + t.date + '</p></div><p class="text-xs font-bold text-' + color + '-600">' + sign + ' TZS ' + Number(t.amount).toLocaleString() + '</p></div>';
    });
    html += '</div>';
    body.innerHTML = html;
}

function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

document.getElementById('addBalanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(this.action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, body: new FormData(this) })
    .then(r => r.json()).then(res => {
        if (res.success) { Swal.fire({ title: 'Success!', text: res.message, icon: 'success', timer: 1500, showConfirmButton: false }); closeDrawer('depositDrawer','depositDrawerPanel'); setTimeout(() => location.reload(), 1000); }
        else Swal.fire('Error', res.message || 'Something went wrong.', 'error');
    }).catch(() => Swal.fire('Error', 'Failed to add balance.', 'error'));
});

document.getElementById('deductBalanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(this.action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, body: new FormData(this) })
    .then(r => r.json()).then(res => {
        if (res.success) { Swal.fire({ title: 'Success!', text: res.message, icon: 'success', timer: 1500, showConfirmButton: false }); closeDrawer('withdrawDrawer','withdrawDrawerPanel'); setTimeout(() => location.reload(), 1000); }
        else Swal.fire('Error', res.message || 'Something went wrong.', 'error');
    }).catch(() => Swal.fire('Error', 'Failed to deduct balance.', 'error'));
});
</script>
@endpush
@endsection
