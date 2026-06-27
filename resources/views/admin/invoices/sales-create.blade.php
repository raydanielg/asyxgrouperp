@extends('layouts.admin')
@section('title', 'Create Sales Invoice - ' . config('app.name'))
@section('page_title', 'Create Sales Invoice')
@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.sales-invoices.store') }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Invoice Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Invoice Number *</label><input name="invoice_number" value="INV-S-{{ date('Ymd') }}-{{ strtoupper(\Illuminate\Support\Str::random(4)) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm font-mono focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Invoice Date *</label><input name="invoice_date" type="date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date *</label><input name="due_date" type="date" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Type *</label><select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="product">Product</option><option value="service">Service</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer *</label><select name="customer_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }} - {{ $c->email }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Warehouse</label><select name="warehouse_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">None</option>@foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Terms</label><input name="payment_terms" placeholder="e.g., Net 30" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Invoice Items</h3>
                <button type="button" onclick="addRow()" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700">+ Add Item</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="itemsTable">
                    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                        <th class="px-3 py-2 font-medium">Product Name</th>
                        <th class="px-3 py-2 font-medium w-20">Qty</th>
                        <th class="px-3 py-2 font-medium w-28">Unit Price</th>
                        <th class="px-3 py-2 font-medium w-28">Discount</th>
                        <th class="px-3 py-2 font-medium w-28">Tax %</th>
                        <th class="px-3 py-2 font-medium w-28">Total</th>
                        <th class="px-3 py-2 w-10"></th>
                    </tr></thead>
                    <tbody id="itemsBody">
                        <tr class="border-t border-gray-100">
                            <td class="px-3 py-2"><input name="items[0][product_name]" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                            <td class="px-3 py-2"><input name="items[0][quantity]" type="number" step="0.01" value="1" oninput="calcRow(this)" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                            <td class="px-3 py-2"><input name="items[0][unit_price]" type="number" step="0.01" value="0" oninput="calcRow(this)" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                            <td class="px-3 py-2"><input name="items[0][discount_amount]" type="number" step="0.01" value="0" oninput="calcRow(this)" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                            <td class="px-3 py-2"><input name="items[0][tax_percentage]" type="number" step="0.01" value="0" oninput="calcRow(this)" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                            <td class="px-3 py-2 text-xs font-semibold text-gray-900" id="rowTotal-0">$0.00</td>
                            <td class="px-3 py-2"><button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-xs">&times;</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end">
                <div class="w-72 bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span class="font-medium" id="subtotal">$0.00</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Discount</span><span class="font-medium text-red-600" id="discount">$0.00</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Tax</span><span class="font-medium" id="tax">$0.00</span></div>
                    <div class="border-t pt-2 flex justify-between"><span class="font-semibold">Total</span><span class="font-bold text-lg" id="total">$0.00</span></div>
                </div>
            </div>
            <input type="hidden" name="subtotal" id="subtotalInput" value="0">
            <input type="hidden" name="tax_amount" id="taxInput" value="0">
            <input type="hidden" name="discount_amount" id="discountInput" value="0">
            <input type="hidden" name="total_amount" id="totalInput" value="0">
        </div>
        <div class="bg-white rounded-xl border p-6">
            <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.sales-invoices.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Invoice</button>
        </div>
    </form>
</div>
<script>
let rowIdx = 1;
function addRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.className = 'border-t border-gray-100';
    tr.innerHTML = `<td class="px-3 py-2"><input name="items[${rowIdx}][product_name]" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
        <td class="px-3 py-2"><input name="items[${rowIdx}][quantity]" type="number" step="0.01" value="1" oninput="calcRow(this)" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
        <td class="px-3 py-2"><input name="items[${rowIdx}][unit_price]" type="number" step="0.01" value="0" oninput="calcRow(this)" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
        <td class="px-3 py-2"><input name="items[${rowIdx}][discount_amount]" type="number" step="0.01" value="0" oninput="calcRow(this)" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
        <td class="px-3 py-2"><input name="items[${rowIdx}][tax_percentage]" type="number" step="0.01" value="0" oninput="calcRow(this)" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
        <td class="px-3 py-2 text-xs font-semibold text-gray-900" id="rowTotal-${rowIdx}">$0.00</td>
        <td class="px-3 py-2"><button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-xs">&times;</button></td>`;
    tbody.appendChild(tr);
    rowIdx++;
}
function removeRow(btn) { btn.closest('tr').remove(); calcAll(); }
function calcRow(input) { calcAll(); }
function calcAll() {
    let subtotal = 0, discount = 0, tax = 0;
    document.querySelectorAll('#itemsBody tr').forEach((tr, i) => {
        const inputs = tr.querySelectorAll('input');
        const qty = parseFloat(inputs[1]?.value || 0);
        const price = parseFloat(inputs[2]?.value || 0);
        const disc = parseFloat(inputs[3]?.value || 0);
        const taxPct = parseFloat(inputs[4]?.value || 0);
        const lineTotal = (qty * price) - disc;
        const lineTax = lineTotal * (taxPct / 100);
        subtotal += qty * price;
        discount += disc;
        tax += lineTax;
        const totalEl = tr.querySelector('td:nth-child(6)');
        if (totalEl) totalEl.textContent = '$' + (lineTotal + lineTax).toFixed(2);
    });
    const total = subtotal - discount + tax;
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('discount').textContent = '$' + discount.toFixed(2);
    document.getElementById('tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
    document.getElementById('subtotalInput').value = subtotal.toFixed(2);
    document.getElementById('taxInput').value = tax.toFixed(2);
    document.getElementById('discountInput').value = discount.toFixed(2);
    document.getElementById('totalInput').value = total.toFixed(2);
}
</script>
@endsection
