@extends('layouts.admin')
@section('title', 'Edit Sales Invoice - ' . config('app.name'))
@section('page_title', 'Edit Sales Invoice')
@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.sales-invoices.update', $salesInvoice) }}" class="space-y-6">
        @csrf @method('PATCH')
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Invoice Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Invoice Number *</label><input name="invoice_number" value="{{ old('invoice_number', $salesInvoice->invoice_number) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm font-mono focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Invoice Date *</label><input name="invoice_date" type="date" value="{{ old('invoice_date', $salesInvoice->invoice_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date *</label><input name="due_date" type="date" value="{{ old('due_date', $salesInvoice->due_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Type *</label><select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="product" {{ old('type', $salesInvoice->type) === 'product' ? 'selected' : '' }}>Product</option><option value="service" {{ old('type', $salesInvoice->type) === 'service' ? 'selected' : '' }}>Service</option></select></div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Customer *</label>
                    <div class="flex gap-2">
                        <select id="customerSelect" name="customer_id" required class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id', $salesInvoice->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} - {{ $c->email }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="openCustomerModal()" class="px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-1" title="Add New Customer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Warehouse</label><select name="warehouse_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">None</option>
        @foreach($warehouses as $w)
        <option value="{{ $w->id }}" {{ old('warehouse_id', $salesInvoice->warehouse_id) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
        @endforeach
        </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Terms</label><input name="payment_terms" value="{{ old('payment_terms', $salesInvoice->payment_terms) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-600 mb-2">Bank Accounts (select one or more for payment)</label>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                    @php $selectedBanks = $salesInvoice->bankAccounts->pluck('id')->toArray(); @endphp
                    @foreach($bankAccounts as $ba)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 hover:border-emerald-300 cursor-pointer transition-colors has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                        <input type="checkbox" name="bank_account_ids[]" value="{{ $ba->id }}" {{ in_array($ba->id, $selectedBanks) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                        <div><span class="text-xs font-medium text-gray-900">{{ $ba->account_name }}</span><p class="text-[10px] text-gray-400">{{ $ba->bank_name }} - {{ $ba->account_number }}</p></div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Summary</h3>
            <div class="grid grid-cols-4 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Subtotal *</label><input name="subtotal" type="number" step="0.01" value="{{ old('subtotal', $salesInvoice->subtotal) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Tax</label><input name="tax_amount" type="number" step="0.01" value="{{ old('tax_amount', $salesInvoice->tax_amount) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Discount</label><input name="discount_amount" type="number" step="0.01" value="{{ old('discount_amount', $salesInvoice->discount_amount) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Total *</label><input name="total_amount" type="number" step="0.01" value="{{ old('total_amount', $salesInvoice->total_amount) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-6 space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">{{ old('notes', $salesInvoice->notes) }}</textarea>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-medium text-gray-600">Terms & Conditions</label>
                    <button type="button" onclick="setDefaultTerms()" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">Load Default Terms</button>
                </div>
                <textarea id="termsField" name="terms_and_conditions" rows="4" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">{{ old('terms_and_conditions', $salesInvoice->terms_and_conditions) }}</textarea>
                <p class="text-[10px] text-gray-400 mt-1">Click “Load Default Terms” to auto-fill standard terms.</p>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.sales-invoices.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update Invoice</button>
        </div>
    </form>
</div>
<div id="customerModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-sm font-bold text-gray-900">Add New Customer</h3>
            <button type="button" onclick="closeCustomerModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form id="customerForm" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">First Name *</label><input name="first_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Last Name</label><input name="last_name" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" type="email" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Company</label><input name="company" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeCustomerModal()" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Customer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCustomerModal() {
    document.getElementById('customerModal').classList.remove('hidden');
}
function closeCustomerModal() {
    document.getElementById('customerModal').classList.add('hidden');
    document.getElementById('customerForm').reset();
}

document.getElementById('customerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    fetch('{{ route('admin.crm-contacts.store') }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.contact) {
            const select = document.getElementById('customerSelect');
            const option = document.createElement('option');
            const name = (data.contact.first_name + ' ' + (data.contact.last_name || '')).trim();
            option.value = data.contact.id;
            option.text = name + (data.contact.email ? ' - ' + data.contact.email : '');
            select.add(option);
            select.value = data.contact.id;
            closeCustomerModal();
            Swal.fire({ icon: 'success', title: 'Customer Added', text: name + ' has been added.', confirmButtonColor: '#024938', timer: 2000 });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Could not add customer.', confirmButtonColor: '#024938' });
        }
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.', confirmButtonColor: '#024938' });
    });
});

function setDefaultTerms() {
    const defaultTerms = `TERMS & CONDITIONS:\n1. Prices Are Quoted in TZS\n2. Prices are subject to change without prior notice\n3. Payment terms must be strictly observed\n4. Goods remain property of {{ config('app.name') }} until fully paid\n\nThank You For Your Business.`;
    document.getElementById('termsField').value = defaultTerms;
}
</script>
@endsection
