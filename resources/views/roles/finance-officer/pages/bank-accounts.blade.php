@php
$title = 'Bank Accounts';
$description = 'Company bank accounts and balances.';
@endphp
@extends('layouts.admin')
@section('title', $title)
@section('page_title', $title)
@section('content')
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10">
        <h2 class="text-2xl font-bold">{{ $title }}</h2>
        <p class="text-emerald-100 text-sm mt-1">{{ $description }}</p>
    </div>
</div>

<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Total Balance</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $money($totalBalance ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Accounts</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $accounts->total() ?? 0 }}</p>
    </div>
</div>

@include('roles.finance-officer.pages._actions', ['module' => 'bank-accounts'])

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Account Name</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Bank</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Number</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Currency</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Balance</th>
                    <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($accounts as $account)
                <tr class="hover:bg-gray-50/50" id="accountRow{{ $account->id }}">
                    <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $account->account_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $account->bank_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $account->account_number ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $account->currency ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-900 text-right">{{ $money($account->current_balance ?? $account->balance ?? 0) }}</td>
                    <td class="px-4 py-3 text-xs text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $account->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-50 text-gray-600 border border-gray-200' }}">
                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openAccountEdit({{ $account->id }}, {{ json_encode($account->only(['account_name','bank_name','account_number','branch','currency','current_balance','is_active'])) }})" class="text-sky-500 hover:text-sky-700 p-1 rounded hover:bg-sky-50 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button onclick="deleteAccount({{ $account->id }})" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">No bank accounts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-xs text-gray-500">Total records: {{ $accounts->total() ?? 0 }}</span>
        <div class="flex items-center gap-2">{{ $accounts->links() }}</div>
    </div>
</div>

<!-- Edit Modal -->
<div id="accountEditModal" class="fixed inset-0 z-50 hidden" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="closeAccountEdit()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-emerald-700 px-4 py-3 sm:px-6">
                    <h3 class="text-base font-semibold text-white">Edit Bank Account</h3>
                </div>
                <form id="accountEditForm" onsubmit="saveAccountEdit(event)" class="px-4 py-5 sm:p-6">
                    <input type="hidden" id="editAccountId" name="account_id">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Account Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="account_name" id="editAccountName" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Bank Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="bank_name" id="editBankName" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Account Number <span class="text-rose-500">*</span></label>
                                <input type="text" name="account_number" id="editAccountNumber" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Branch</label>
                                <input type="text" name="branch" id="editBranch" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Currency</label>
                                <input type="text" name="currency" id="editCurrency" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Balance</label>
                                <input type="number" step="0.01" name="current_balance" id="editBalance" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Active</label>
                                <select name="is_active" id="editIsActive" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeAccountEdit()" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" id="saveAccountBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Save Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function openAccountEdit(id, data) {
    document.getElementById('editAccountId').value = id;
    document.getElementById('editAccountName').value = data.account_name || '';
    document.getElementById('editBankName').value = data.bank_name || '';
    document.getElementById('editAccountNumber').value = data.account_number || '';
    document.getElementById('editBranch').value = data.branch || '';
    document.getElementById('editCurrency').value = data.currency || '';
    document.getElementById('editBalance').value = data.current_balance ?? data.balance ?? 0;
    document.getElementById('editIsActive').value = data.is_active ? '1' : '0';
    document.getElementById('accountEditModal').classList.remove('hidden');
}

function closeAccountEdit() {
    document.getElementById('accountEditModal').classList.add('hidden');
}

function saveAccountEdit(e) {
    e.preventDefault();
    const form = document.getElementById('accountEditForm');
    const btn = document.getElementById('saveAccountBtn');
    const id = document.getElementById('editAccountId').value;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.is_active = formData.get('is_active') === '1';

    const url = '{{ route('admin.bank-accounts.update', ['bankAccount' => '__ID__']) }}'.replace('__ID__', id);
    btn.disabled = true;
    btn.textContent = 'Saving...';

    fetch(url, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.textContent = 'Save Account';
        if (res.success) {
            closeAccountEdit();
            Swal.fire({ icon: 'success', title: 'Updated!', text: res.message, timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1500);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to update.', confirmButtonColor: '#024938' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Save Account';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
}

function deleteAccount(id) {
    Swal.fire({
        title: 'Delete Account?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = '{{ route('admin.bank-accounts.destroy', ['bankAccount' => '__ID__']) }}'.replace('__ID__', id);
        fetch(url, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false });
                document.getElementById('accountRow' + id)?.remove();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to delete.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' }));
    });
}
</script>
@endpush
@endsection