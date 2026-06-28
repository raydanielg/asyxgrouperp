@extends('layouts.admin')
@section('title', 'New Intercompany Transaction - ' . config('app.name'))
@section('page_title', 'New Intercompany Transaction')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.intercompany.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Intercompany</a>
</div>

<div class="bg-white rounded-xl border p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.intercompany.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">From Company <span class="text-red-500">*</span></label>
                <select name="from_company_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">— Select —</option>
                    @foreach($companies as $id => $name)
                    <option value="{{ $id }}" @selected(old('from_company_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">To Company <span class="text-red-500">*</span></label>
                <select name="to_company_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">— Select —</option>
                    @foreach($companies as $id => $name)
                    <option value="{{ $id }}" @selected(old('to_company_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="invoice" @selected(old('type') === 'invoice')>Invoice</option>
                    <option value="transfer" @selected(old('type') === 'transfer')>Transfer</option>
                    <option value="shared_service" @selected(old('type') === 'shared_service')>Shared Service</option>
                    <option value="shared_staff" @selected(old('type') === 'shared_staff')>Shared Staff</option>
                    <option value="loan" @selected(old('type') === 'loan')>Loan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label>
                <input type="number" name="amount" value="{{ old('amount') }}" required min="0" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Currency <span class="text-red-500">*</span></label>
                <input type="text" name="currency" value="{{ old('currency', 'TZS') }}" required maxlength="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Transaction Date <span class="text-red-500">*</span></label>
                <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- Line Items --}}
        <div class="mt-6 border-t pt-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-gray-700">Line Items (Optional)</h4>
                <button type="button" onclick="addLine()" class="text-xs text-emerald-600 hover:text-emerald-700">+ Add Line</button>
            </div>
            <div id="linesContainer" class="space-y-2"></div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">Create Transaction</button>
            <a href="{{ route('admin.intercompany.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Cancel</a>
        </div>
    </form>
</div>

<script>
let lineIndex = 0;
function addLine() {
    lineIndex++;
    const html = `
    <div class="grid grid-cols-12 gap-2 items-start" id="line-${lineIndex}">
        <input type="text" name="lines[${lineIndex}][description]" placeholder="Description" class="col-span-5 px-2 py-1.5 text-xs border border-gray-200 rounded-lg">
        <input type="number" name="lines[${lineIndex}][quantity]" placeholder="Qty" step="0.01" class="col-span-2 px-2 py-1.5 text-xs border border-gray-200 rounded-lg">
        <input type="number" name="lines[${lineIndex}][unit_price]" placeholder="Unit Price" step="0.01" class="col-span-3 px-2 py-1.5 text-xs border border-gray-200 rounded-lg">
        <button type="button" onclick="document.getElementById('line-${lineIndex}').remove()" class="col-span-2 text-xs text-red-500 hover:text-red-700">Remove</button>
    </div>`;
    document.getElementById('linesContainer').insertAdjacentHTML('beforeend', html);
}
</script>
@endsection
