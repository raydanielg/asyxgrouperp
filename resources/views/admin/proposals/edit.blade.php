@extends('layouts.admin')
@section('title', 'Edit Quotation - ' . config('app.name'))
@section('page_title', 'Edit Sales Quotation')
@section('content')
<div class="max-w-5xl">
    <form method="POST" action="{{ route('admin.sales-proposals.update', $salesProposal) }}" class="space-y-6">
        @csrf @method('PATCH')
        {{-- Header --}}
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div><h2 class="text-xl font-bold">Edit Quotation</h2><p class="text-emerald-100 text-xs mt-1">{{ $salesProposal->proposal_number }}</p></div>
                <svg class="w-10 h-10 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
        </div>
        {{-- Details --}}
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2"><span class="w-1 h-4 bg-emerald-600 rounded-full"></span> Quotation Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Quotation Number *</label><input name="proposal_number" value="{{ old('proposal_number', $salesProposal->proposal_number) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm font-mono focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Date *</label><input name="proposal_date" type="date" value="{{ old('proposal_date', $salesProposal->proposal_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Valid Until *</label><input name="due_date" type="date" value="{{ old('due_date', $salesProposal->due_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer *</label><select name="customer_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">@foreach($customers as $c)<option value="{{ $c->id }}" {{ old('customer_id', $salesProposal->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} - {{ $c->email }}</option>@endforeach</select></div>
            </div>
        </div>
        {{-- Items --}}
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
                        @if($salesProposal->items->count() > 0)
                            @foreach($salesProposal->items as $i => $item)
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2"><input name="items[{{ $i }}][product_name]" value="{{ old("items.{$i}.product_name", $item->product_name) }}" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                                <td class="px-3 py-2"><input name="items[{{ $i }}][quantity]" type="number" step="0.01" value="{{ old("items.{$i}.quantity", $item->quantity) }}" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                                <td class="px-3 py-2"><input name="items[{{ $i }}][unit_price]" type="number" step="0.01" value="{{ old("items.{$i}.unit_price", $item->unit_price) }}" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                                <td class="px-3 py-2"><input name="items[{{ $i }}][discount_amount]" type="number" step="0.01" value="{{ old("items.{$i}.discount_amount", $item->discount_amount) }}" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                                <td class="px-3 py-2"><input name="items[{{ $i }}][tax_percentage]" type="number" step="0.01" value="{{ old("items.{$i}.tax_percentage", $item->tax_percentage) }}" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                                <td class="px-3 py-2 text-xs font-semibold text-gray-900">TZS {{ number_format($item->total_amount) }}</td>
                                <td class="px-3 py-2"><button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-xs">&times;</button></td>
                            </tr>
                            @endforeach
                        @else
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2"><input name="items[0][product_name]" placeholder="Product or service name" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                                <td class="px-3 py-2"><input name="items[0][quantity]" type="number" step="0.01" value="1" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                                <td class="px-3 py-2"><input name="items[0][unit_price]" type="number" step="0.01" value="0" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                                <td class="px-3 py-2"><input name="items[0][discount_amount]" type="number" step="0.01" value="0" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                                <td class="px-3 py-2"><input name="items[0][tax_percentage]" type="number" step="0.01" value="0" oninput="calcAll()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
                                <td class="px-3 py-2 text-xs font-semibold text-gray-900">TZS 0</td>
                                <td class="px-3 py-2"><button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-xs">&times;</button></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {{-- Summary --}}
            <div class="mt-4 flex justify-end">
                <div class="w-80 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 space-y-3 border border-gray-200">
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900" id="subtotal">TZS {{ number_format($salesProposal->subtotal) }}</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Discount</span><span class="font-medium text-red-600" id="discount">TZS {{ number_format($salesProposal->discount_amount) }}</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Tax</span><span class="font-medium text-gray-900" id="tax">TZS {{ number_format($salesProposal->tax_amount) }}</span></div>
                    <div class="border-t-2 border-emerald-200 pt-3 flex justify-between items-center"><span class="font-bold text-gray-900">Total</span><span class="font-bold text-lg text-emerald-700" id="total">TZS {{ number_format($salesProposal->total_amount) }}</span></div>
                </div>
            </div>
            <input type="hidden" name="subtotal" id="subtotalInput" value="{{ $salesProposal->subtotal }}">
            <input type="hidden" name="tax_amount" id="taxInput" value="{{ $salesProposal->tax_amount }}">
            <input type="hidden" name="discount_amount" id="discountInput" value="{{ $salesProposal->discount_amount }}">
            <input type="hidden" name="total_amount" id="totalInput" value="{{ $salesProposal->total_amount }}">
        </div>
        {{-- Terms & Notes --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border p-6"><label class="block text-xs font-medium text-gray-600 mb-1">Payment Terms</label><input name="payment_terms" value="{{ old('payment_terms', $salesProposal->payment_terms) }}" placeholder="e.g., Net 30, 50% advance" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="bg-white rounded-xl border p-6"><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" placeholder="Additional notes for the customer" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">{{ old('notes', $salesProposal->notes) }}</textarea></div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.sales-proposals.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-medium rounded-lg hover:from-emerald-700 hover:to-emerald-800 shadow-sm">Update Quotation</button>
        </div>
    </form>
</div>
<script>
let rowIdx = {{ max($salesProposal->items->count(), 1) }};
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
