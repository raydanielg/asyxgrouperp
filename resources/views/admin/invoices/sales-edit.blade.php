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
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer *</label><select name="customer_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        @foreach($customers as $c)
        <option value="{{ $c->id }}" {{ old('customer_id', $salesInvoice->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} - {{ $c->email }}</option>
        @endforeach
        </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Warehouse</label><select name="warehouse_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">None</option>
        @foreach($warehouses as $w)
        <option value="{{ $w->id }}" {{ old('warehouse_id', $salesInvoice->warehouse_id) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
        @endforeach
        </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Terms</label><input name="payment_terms" value="{{ old('payment_terms', $salesInvoice->payment_terms) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
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
        <div class="bg-white rounded-xl border p-6">
            <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">{{ old('notes', $salesInvoice->notes) }}</textarea>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.sales-invoices.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update Invoice</button>
        </div>
    </form>
</div>
@endsection
