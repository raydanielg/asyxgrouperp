@extends('layouts.admin')
@section('title', 'Bank Accounts - ' . config('app.name'))
@section('page_title', 'Bank Accounts')
@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    {{ session('error') }}
</div>
@endif

<div class="flex items-center justify-between mb-5">
    <p class="text-sm text-gray-500">Manage your bank accounts — right-click a card for options</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Account
    </button>
</div>

<div id="cardsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($accounts as $a)
    @php
        $gradients = [
            ['from'=>'from-slate-700','to'=>'to-slate-900'],
            ['from'=>'from-emerald-600','to'=>'to-emerald-800'],
            ['from'=>'from-blue-600','to'=>'to-blue-800'],
            ['from'=>'from-purple-600','to'=>'to-purple-800'],
            ['from'=>'from-rose-600','to'=>'to-rose-800'],
            ['from'=>'from-amber-600','to'=>'to-amber-800'],
            ['from'=>'from-cyan-600','to'=>'to-cyan-800'],
            ['from'=>'from-indigo-600','to'=>'to-indigo-800'],
        ];
        $g = $gradients[$a->id % count($gradients)];
    @endphp
    <div class="bank-card relative rounded-2xl overflow-hidden shadow-lg cursor-pointer transition-transform hover:scale-[1.02] bg-gradient-to-br {{ $g['from'] }} {{ $g['to'] }} p-5 text-white min-h-[200px] flex flex-col justify-between"
         data-id="{{ $a->id }}"
         data-name="{{ $a->account_name }}"
         data-bank="{{ $a->bank_name }}"
         data-number="{{ $a->account_number }}"
         data-balance="{{ $a->current_balance }}"
         data-currency="{{ $a->currency }}"
         oncontextmenu="showCardMenu(event, {{ $a->id }}); return false;">
        <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-12 -left-8 w-28 h-28 rounded-full bg-white/5"></div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-[10px] uppercase tracking-widest text-white/60">{{ $a->currency ?? 'USD' }}</p>
                <h3 class="text-sm font-bold mt-0.5">{{ $a->bank_name }}</h3>
                @if($a->branch)<p class="text-[10px] text-white/50">{{ $a->branch }}</p>@endif
            </div>
            <div class="flex items-center gap-2">
                @if($a->is_active)<span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.8)]"></span>@else<span class="w-2 h-2 rounded-full bg-gray-400"></span>@endif
                <div class="w-8 h-6 rounded-md bg-gradient-to-br from-yellow-200 to-yellow-400 shadow-inner flex items-center justify-center"><div class="w-5 h-3 border border-yellow-600/30 rounded-sm"></div></div>
            </div>
        </div>
        <div class="relative mt-3">
            <p class="text-sm font-mono tracking-[0.2em] text-white/80">•••• •••• •••• {{ substr($a->account_number, -4) }}</p>
        </div>
        <div class="relative flex items-end justify-between mt-2">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-white/50">Account Holder</p>
                <p class="text-xs font-semibold">{{ $a->account_name }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] uppercase tracking-wider text-white/50">Balance</p>
                <p class="text-lg font-bold balance-text" data-balance="{{ $a->current_balance }}" onclick="toggleBalance(this); event.stopPropagation();">
                    <span class="hidden-amount">TZS {{ number_format($a->current_balance) }}</span>
                </p>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16">
        <svg class="w-14 h-14 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        <p class="text-sm text-gray-400">No bank accounts yet. Click "Add Account" to create one.</p>
    </div>
    @endforelse
</div>

<div class="px-1 mt-4">{{ $accounts->links() }}</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Bank Account</h3>
        <form method="POST" action="{{ route('admin.bank-accounts.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Account Name *</label><input name="account_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Account Number *</label><input name="account_number" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Bank Name *</label><input name="bank_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Branch</label><input name="branch" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><input name="currency" value="USD" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Opening Balance</label><input name="opening_balance" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Current Balance</label><input name="current_balance" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div></div>
            <div class="flex items-center gap-2"><input type="checkbox" name="is_active" checked class="rounded border-gray-300 text-emerald-600"><label class="text-xs text-gray-600">Active</label></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>

{{-- Right-click Context Menu --}}
<div id="cardMenu" class="hidden fixed z-[100] bg-white rounded-xl shadow-2xl border border-gray-100 py-2 w-52">
    <button onclick="cardMenuAction('addBalance')" class="w-full px-4 py-2.5 text-left text-xs font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 flex items-center gap-3 transition-colors">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Balance
    </button>
    <button onclick="cardMenuAction('deductBalance')" class="w-full px-4 py-2.5 text-left text-xs font-medium text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center gap-3 transition-colors">
        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
        Deduct Balance
    </button>
    <button onclick="cardMenuAction('transactions')" class="w-full px-4 py-2.5 text-left text-xs font-medium text-gray-700 hover:bg-sky-50 hover:text-sky-700 flex items-center gap-3 transition-colors">
        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        Transaction History
    </button>
    <div class="border-t border-gray-100 my-1"></div>
    <button onclick="cardMenuAction('edit')" class="w-full px-4 py-2.5 text-left text-xs font-medium text-gray-700 hover:bg-amber-50 hover:text-amber-700 flex items-center gap-3 transition-colors">
        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Account
    </button>
    <button onclick="cardMenuAction('delete')" class="w-full px-4 py-2.5 text-left text-xs font-medium text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center gap-3 transition-colors">
        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        Delete Account
    </button>
</div>

{{-- Add Balance Modal --}}
<div id="addBalanceModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-1">Add Balance</h3>
        <p class="text-xs text-gray-400 mb-4" id="addBalanceAccountName"></p>
        <form id="addBalanceForm" method="POST" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" min="0.01" required placeholder="0.00" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><input name="description" placeholder="Optional note..." class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('addBalanceModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Add</button></div>
        </form>
    </div>
</div>

{{-- Deduct Balance Modal --}}
<div id="deductBalanceModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-1">Deduct Balance</h3>
        <p class="text-xs text-gray-400 mb-4" id="deductBalanceAccountName"></p>
        <form id="deductBalanceForm" method="POST" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" min="0.01" required placeholder="0.00" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><input name="description" placeholder="Optional note..." class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('deductBalanceModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600">Deduct</button></div>
        </form>
    </div>
</div>

{{-- Edit Account Modal --}}
<div id="editModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Bank Account</h3>
        <form id="editForm" method="POST" class="space-y-3">@csrf @method('PUT')
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Account Name *</label><input name="account_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Account Number *</label><input name="account_number" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Bank Name *</label><input name="bank_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Branch</label><input name="branch" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><input name="currency" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Current Balance</label><input name="current_balance" type="number" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="flex items-center gap-2"><input type="checkbox" name="is_active" class="rounded border-gray-300 text-emerald-600"><label class="text-xs text-gray-600">Active</label></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update</button></div>
        </form>
    </div>
</div>

{{-- Transaction History Modal --}}
<div id="transactionsModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] flex flex-col">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <div><h3 class="text-lg font-bold text-gray-900">Transaction History</h3><p class="text-xs text-gray-400" id="txnAccountName"></p></div>
            <button onclick="document.getElementById('transactionsModal').classList.add('hidden')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center"><svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div id="txnBody" class="overflow-y-auto p-6 flex-1"><p class="text-center text-sm text-gray-400 py-8">Loading transactions...</p></div>
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
let selectedCardId = null;

function showCardMenu(e, cardId) {
    e.preventDefault();
    selectedCardId = cardId;
    const menu = document.getElementById('cardMenu');
    menu.classList.remove('hidden');
    let x = e.clientX, y = e.clientY;
    const rect = menu.getBoundingClientRect();
    if (x + rect.width > window.innerWidth) x = window.innerWidth - rect.width - 10;
    if (y + rect.height > window.innerHeight) y = window.innerHeight - rect.height - 10;
    menu.style.left = x + 'px';
    menu.style.top = y + 'px';
}
document.addEventListener('click', () => document.getElementById('cardMenu').classList.add('hidden'));

function cardMenuAction(action) {
    document.getElementById('cardMenu').classList.add('hidden');
    if (!selectedCardId) return;
    const card = document.querySelector('.bank-card[data-id="' + selectedCardId + '"]');
    if (!card) return;
    const name = card.dataset.name, bank = card.dataset.bank, number = card.dataset.number;
    const balance = parseFloat(card.dataset.balance), currency = card.dataset.currency;

    if (action === 'addBalance') {
        document.getElementById('addBalanceAccountName').textContent = name + ' — ' + bank + ' (•••• ' + number.slice(-4) + ')';
        document.getElementById('addBalanceForm').action = addBalanceUrl.replace('__ID__', selectedCardId);
        document.getElementById('addBalanceModal').classList.remove('hidden');
    } else if (action === 'deductBalance') {
        document.getElementById('deductBalanceAccountName').textContent = name + ' — ' + bank + ' (•••• ' + number.slice(-4) + ') — Current: TZS ' + balance.toLocaleString();
        document.getElementById('deductBalanceForm').action = deductBalanceUrl.replace('__ID__', selectedCardId);
        document.getElementById('deductBalanceModal').classList.remove('hidden');
    } else if (action === 'transactions') {
        document.getElementById('txnAccountName').textContent = name + ' — ' + bank;
        document.getElementById('txnBody').innerHTML = '<p class="text-center text-sm text-gray-400 py-8">Loading transactions...</p>';
        document.getElementById('transactionsModal').classList.remove('hidden');
        fetch(transactionsUrl.replace('__ID__', selectedCardId), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json()).then(res => renderTransactions(res))
            .catch(() => { document.getElementById('txnBody').innerHTML = '<p class="text-center text-sm text-red-400 py-8">Failed to load transactions.</p>'; });
    } else if (action === 'edit') {
        const form = document.getElementById('editForm');
        form.action = updateUrl.replace('__ID__', selectedCardId);
        form.querySelector('[name="account_name"]').value = name;
        form.querySelector('[name="bank_name"]').value = bank;
        form.querySelector('[name="account_number"]').value = number;
        form.querySelector('[name="current_balance"]').value = balance;
        form.querySelector('[name="currency"]').value = currency;
        form.querySelector('[name="is_active"]').checked = true;
        document.getElementById('editModal').classList.remove('hidden');
    } else if (action === 'delete') {
        Swal.fire({
            title: 'Delete Account?', text: name + ' (' + bank + ') will be permanently removed.',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
            confirmButtonText: 'Delete', cancelButtonText: 'Cancel', reverseButtons: true
        }).then((r) => {
            if (r.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST'; form.action = destroyUrl.replace('__ID__', selectedCardId);
                const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = csrfToken;
                const method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
                form.appendChild(csrf); form.appendChild(method); document.body.appendChild(form); form.submit();
            }
        });
    }
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

document.querySelectorAll('.balance-text').forEach(el => {
    const span = el.querySelector('.hidden-amount');
    span.textContent = '••••••••';
    span.dataset.hidden = '1';
    span.style.cursor = 'pointer';
    span.title = 'Click to reveal/hide balance';
});

function renderTransactions(res) {
    const txns = res.transactions || [];
    const account = res.account || {};
    const body = document.getElementById('txnBody');
    if (txns.length === 0) {
        body.innerHTML = '<div class="text-center py-12"><svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg><p class="text-sm text-gray-400">No transactions found for this account.</p></div>';
        return;
    }
    let html = '<div class="mb-4 p-4 bg-emerald-50 rounded-lg flex items-center justify-between"><div><p class="text-[10px] text-gray-500 uppercase">Current Balance</p><p class="text-lg font-bold text-emerald-700">TZS ' + Number(account.current_balance || 0).toLocaleString() + '</p></div><div class="text-right"><p class="text-[10px] text-gray-500 uppercase">Total Transactions</p><p class="text-lg font-bold text-gray-700">' + txns.length + '</p></div></div>';
    html += '<div class="space-y-2">';
    txns.forEach(t => {
        const isCredit = t.type === 'credit';
        const color = isCredit ? 'emerald' : 'red';
        const sign = isCredit ? '+' : '−';
        const icon = isCredit ? '<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>' : '<svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>';
        html += '<div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors"><div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-' + color + '-50 flex items-center justify-center">' + icon + '</div><div><p class="text-xs font-medium text-gray-900">' + escapeHtml(t.label) + '</p><p class="text-[10px] text-gray-400">' + escapeHtml(t.description || '') + ' · ' + t.date + '</p></div></div><p class="text-sm font-bold text-' + color + '-600">' + sign + ' TZS ' + Number(t.amount).toLocaleString() + '</p></div>';
    });
    html += '</div>';
    body.innerHTML = html;
}

function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

document.getElementById('addBalanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(this.action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, body: new FormData(this) })
    .then(r => r.json()).then(res => {
        if (res.success) { Swal.fire({ title: 'Success!', text: res.message, icon: 'success', timer: 1500, showConfirmButton: false }); document.getElementById('addBalanceModal').classList.add('hidden'); setTimeout(() => location.reload(), 1000); }
        else Swal.fire('Error', res.message || 'Something went wrong.', 'error');
    }).catch(() => Swal.fire('Error', 'Failed to add balance.', 'error'));
});

document.getElementById('deductBalanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(this.action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, body: new FormData(this) })
    .then(r => r.json()).then(res => {
        if (res.success) { Swal.fire({ title: 'Success!', text: res.message, icon: 'success', timer: 1500, showConfirmButton: false }); document.getElementById('deductBalanceModal').classList.add('hidden'); setTimeout(() => location.reload(), 1000); }
        else Swal.fire('Error', res.message || 'Something went wrong.', 'error');
    }).catch(() => Swal.fire('Error', 'Failed to deduct balance.', 'error'));
});
</script>
@endpush
@endsection
