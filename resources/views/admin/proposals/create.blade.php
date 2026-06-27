@extends('layouts.admin')
@section('title', 'Create Quotation - ' . config('app.name'))
@section('page_title', 'Create Sales Quotation')
@section('content')
<div class="max-w-5xl">
    <form method="POST" action="{{ route('admin.sales-proposals.store') }}" class="space-y-6">
        @csrf
        {{-- Header Section with Gradient --}}
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div><h2 class="text-xl font-bold">New Quotation</h2><p class="text-emerald-100 text-xs mt-1">Create a professional quotation for your customer</p></div>
                <svg class="w-10 h-10 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
        {{-- Details Card --}}
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2"><span class="w-1 h-4 bg-emerald-600 rounded-full"></span> Quotation Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Quotation Number *</label><input name="proposal_number" value="PROP-{{ date('Ymd') }}-{{ strtoupper(\Illuminate\Support\Str::random(4)) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm font-mono focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Date *</label><input name="proposal_date" type="date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Valid Until *</label><input name="due_date" type="date" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer *</label><select name="customer_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select Customer...</option>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }} - {{ $c->email }}</option>@endforeach</select></div>
            </div>
        </div>
        {{-- Items Card --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2"><span class="w-1 h-4 bg-sky-600 rounded-full"></span> Items / Services</h3>
                <button type="button" onclick="addRow()" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Item</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b">
                        <th class="px-3 py-2 font-medium">Description</th>
                        <th class="px-3 py-2 font-medium w-20">Qty</th>
                        <th class="px-3 py-2 font-medium w-32">Unit Price</th>
                        <th class="px-3 py-2 font-medium w-28">Discount</th>
                        <th class="px-3 py-2 font-medium w-20">Tax %</th>
                        <th class="px-3 py-2 font-medium w-32">Line Total</th>
                        <th class="px-3 py-2 w-10"></th>
                    </tr></thead>
                    <tbody id="itemsBody">
                        <tr class="border-t border-gray-100">
                            <td class="px-3 py-2"><input name="items[0][product_name]" placeholder="Product or service name" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                            <td class="px-3 py-2"><input name="items[0][quantity]" type="number" step="0.01" value="1" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                            <td class="px-3 py-2"><input name="items[0][unit_price]" type="number" step="0.01" value="0" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                            <td class="px-3 py-2"><input name="items[0][discount_amount]" type="number" step="0.01" value="0" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                            <td class="px-3 py-2"><input name="items[0][tax_percentage]" type="number" step="0.01" value="0" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                            <td class="px-3 py-2 text-xs font-semibold text-gray-900">TZS 0</td>
                            <td class="px-3 py-2"><button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-xs">&times;</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Summary Box --}}
            <div class="mt-4 flex justify-end">
                <div class="w-80 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 space-y-3 border border-gray-200">
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900" id="subtotal">TZS 0</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Discount</span><span class="font-medium text-red-600" id="discount">TZS 0</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Tax</span><span class="font-medium text-gray-900" id="tax">TZS 0</span></div>
                    <div class="border-t-2 border-emerald-200 pt-3 flex justify-between items-center"><span class="font-bold text-gray-900">Total</span><span class="font-bold text-lg text-emerald-700" id="total">TZS 0</span></div>
                </div>
            </div>
            <input type="hidden" name="subtotal" id="subtotalInput" value="0">
            <input type="hidden" name="tax_amount" id="taxInput" value="0">
            <input type="hidden" name="discount_amount" id="discountInput" value="0">
            <input type="hidden" name="total_amount" id="totalInput" value="0">
        </div>
        {{-- Terms & Notes --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border p-6"><label class="block text-xs font-medium text-gray-600 mb-1">Payment Terms</label><input name="payment_terms" placeholder="e.g., Net 30, 50% advance" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="bg-white rounded-xl border p-6"><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" placeholder="Additional notes for the customer" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.sales-proposals.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-medium rounded-lg hover:from-emerald-700 hover:to-emerald-800 shadow-sm">Create Quotation</button>
        </div>
    </form>
</div>
<script>
let rowIdx = 1;
function addRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.className = 'border-t border-gray-100';
    tr.innerHTML = '<td class="px-3 py-2"><input name="items['+rowIdx+'][product_name]" placeholder="Product or service name" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>'+
        '<td class="px-3 py-2"><input name="items['+rowIdx+'][quantity]" type="number" step="0.01" value="1" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>'+
        '<td class="px-3 py-2"><input name="items['+rowIdx+'][unit_price]" type="number" step="0.01" value="0" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>'+
        '<td class="px-3 py-2"><input name="items['+rowIdx+'][discount_amount]" type="number" step="0.01" value="0" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>'+
        '<td class="px-3 py-2"><input name="items['+rowIdx+'][tax_percentage]" type="number" step="0.01" value="0" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>'+
        '<td class="px-3 py-2 text-xs font-semibold text-gray-900">TZS 0</td>'+
        '<td class="px-3 py-2"><button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-xs">&times;</button></td>';
    tbody.appendChild(tr);
    rowIdx++;
}
function removeRow(btn) { btn.closest('tr').remove(); calcAll(); }
function fmt(n) { return 'TZS ' + Math.round(n).toLocaleString(); }
function calcAll() {
    let subtotal = 0, discount = 0, tax = 0;
    document.querySelectorAll('#itemsBody tr').forEach((tr) => {
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
        if (totalEl) totalEl.textContent = fmt(lineTotal + lineTax);
    });
    const total = subtotal - discount + tax;
    document.getElementById('subtotal').textContent = fmt(subtotal);
    document.getElementById('discount').textContent = fmt(discount);
    document.getElementById('tax').textContent = fmt(tax);
    document.getElementById('total').textContent = fmt(total);
    document.getElementById('subtotalInput').value = subtotal.toFixed(2);
    document.getElementById('taxInput').value = tax.toFixed(2);
    document.getElementById('discountInput').value = discount.toFixed(2);
    document.getElementById('totalInput').value = total.toFixed(2);
}
</script>
@endsection
