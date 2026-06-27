@extends('layouts.admin')
@section('title', 'Edit Proposal - ' . config('app.name'))
@section('page_title', 'Edit Sales Proposal')
@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.sales-proposals.update', $salesProposal) }}" class="space-y-6">
        @csrf @method('PATCH')
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Proposal Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Proposal Number *</label><input name="proposal_number" value="{{ old('proposal_number', $salesProposal->proposal_number) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm font-mono focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Proposal Date *</label><input name="proposal_date" type="date" value="{{ old('proposal_date', $salesProposal->proposal_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date *</label><input name="due_date" type="date" value="{{ old('due_date', $salesProposal->due_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer *</label><select name="customer_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">@foreach($customers as $c)<option value="{{ $c->id }}" {{ old('customer_id', $salesProposal->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} - {{ $c->email }}</option>@endforeach</select></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Summary</h3>
            <div class="grid grid-cols-4 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Subtotal *</label><input name="subtotal" type="number" step="0.01" value="{{ old('subtotal', $salesProposal->subtotal) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Tax</label><input name="tax_amount" type="number" step="0.01" value="{{ old('tax_amount', $salesProposal->tax_amount) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Discount</label><input name="discount_amount" type="number" step="0.01" value="{{ old('discount_amount', $salesProposal->discount_amount) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Total *</label><input name="total_amount" type="number" step="0.01" value="{{ old('total_amount', $salesProposal->total_amount) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-6">
            <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">{{ old('notes', $salesProposal->notes) }}</textarea>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.sales-proposals.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update Proposal</button>
        </div>
    </form>
</div>
@endsection
