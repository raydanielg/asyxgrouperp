@extends('layouts.admin')
@section('title', 'Expenses - ' . config('app.name'))
@section('page_title', 'Expenses')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Record and track business expenses</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Expense
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Expense #</th><th class="px-5 py-3 font-medium">Category</th><th class="px-5 py-3 font-medium">Payee</th><th class="px-5 py-3 font-medium">Amount</th><th class="px-5 py-3 font-medium">Cost Allocation</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($expenses as $e)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $e->expense_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $e->category ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $e->payee ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-red-600">TZS {{ number_format($e->amount) }}</td>
            <td class="px-5 py-3 text-xs">
                @php $allocTotal = $e->costAllocations->sum('amount'); @endphp
                @if($allocTotal > 0)
                <span class="text-emerald-600 font-medium">TZS {{ number_format($allocTotal) }}</span>
                <div class="text-[10px] text-gray-400">
                @foreach($e->costAllocations as $ca)
                <div>{{ $ca->costCenter?->name ?? '?' }}: {{ number_format($ca->percentage, 0) }}%</div>
                @endforeach
                </div>
                @else
                <span class="text-amber-500 text-[10px]">Not allocated</span>
                @endif
            </td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $e->expense_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs">
                <button onclick="showAllocateModal({{ $e->id }}, '{{ $e->expense_number }}', {{ $e->amount }})" class="text-emerald-600 hover:text-emerald-700 font-medium mr-2">Allocate</button>
                <form id="del-exp-{{ $e->id }}" method="POST" action="{{ route('admin.expenses.destroy', $e) }}" class="inline">@csrf @method('DELETE')<button type="button" onclick="confirmDelete('del-exp-{{ $e->id }}')" class="text-red-500 hover:text-red-700">Delete</button></form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No expenses found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $expenses->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Expense</h3>
        <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><input name="category" placeholder="e.g. Rent, Utilities, Supplies" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" required value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Expense Date *</label><input name="expense_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Bank Account</label><select name="bank_account_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">None</option>
        @foreach($accounts as $a)
        <option value="{{ $a->id }}">{{ $a->account_name }}</option>
        @endforeach
        </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label><select name="payment_method" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="cash">Cash</option><option value="bank">Bank Transfer</option><option value="card">Card</option><option value="cheque">Cheque</option><option value="mobile">Mobile Money</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Payee</label><input name="payee" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>

{{-- Cost Allocation Modal --}}
<div id="allocateModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-1">Allocate Costs</h3>
        <p class="text-xs text-gray-500 mb-4" id="alloc_info">Expense: <span id="alloc_expense_no"></span> | Amount: TZS <span id="alloc_amount"></span></p>
        <form method="POST" action="{{ route('admin.cost-centers.allocate') }}" class="space-y-3">@csrf
            <input type="hidden" name="expense_id" id="alloc_expense_id">

            <div id="alloc_rows" class="space-y-2">
                <div class="alloc-row flex items-center gap-2">
                    <select name="allocations[0][cost_center_id]" required class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        <option value="">Select cost center...</option>
                        @foreach($costCenters as $cc)
                        <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="allocations[0][amount]" placeholder="Amount" step="0.01" min="0" required class="w-28 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <input type="number" name="allocations[0][percentage]" placeholder="%" step="0.01" min="0" max="100" class="w-16 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <button type="button" onclick="this.closest('.alloc-row').remove()" class="text-red-400 hover:text-red-600">&times;</button>
                </div>
            </div>

            <button type="button" onclick="addAllocRow()" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">+ Add another cost center</button>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('allocateModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Allocations</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allocRowIndex = 1;
const costCenters = @json($costCenters->pluck('name', 'id'));

function showAllocateModal(id, expenseNo, amount) {
    document.getElementById('alloc_expense_id').value = id;
    document.getElementById('alloc_expense_no').textContent = expenseNo;
    document.getElementById('alloc_amount').textContent = Number(amount).toLocaleString();
    // Reset to one row
    const container = document.getElementById('alloc_rows');
    container.innerHTML = '';
    addAllocRow();
    document.getElementById('allocateModal').classList.remove('hidden');
}

function addAllocRow() {
    const container = document.getElementById('alloc_rows');
    const idx = allocRowIndex++;
    const div = document.createElement('div');
    div.className = 'alloc-row flex items-center gap-2';
    let opts = '<option value="">Select cost center...</option>';
    @foreach($costCenters as $cc)
    opts += '<option value="{{ $cc->id }}">{{ $cc->name }}</option>';
    @endforeach
    div.innerHTML = `
        <select name="allocations[${idx}][cost_center_id]" required class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            ${opts}
        </select>
        <input type="number" name="allocations[${idx}][amount]" placeholder="Amount" step="0.01" min="0" required class="w-28 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        <input type="number" name="allocations[${idx}][percentage]" placeholder="%" step="0.01" min="0" max="100" class="w-16 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        <button type="button" onclick="this.closest('.alloc-row').remove()" class="text-red-400 hover:text-red-600">&times;</button>
    `;
    container.appendChild(div);
}
</script>
@endpush
