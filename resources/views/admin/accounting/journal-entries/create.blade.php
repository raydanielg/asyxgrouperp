@extends('layouts.admin')
@section('title', 'New Journal Entry - ' . config('app.name'))
@section('page_title', 'New Journal Entry')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.journal-entries.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Journal Entries</a>
</div>

@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded p-3 mb-4">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('admin.journal-entries.store') }}" id="jeForm">
    @csrf
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Entry Details</h4></div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Entry Date <span class="text-red-500">*</span></label><input name="entry_date" type="date" required value="{{ old('entry_date', date('Y-m-d')) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Reference</label><input name="reference" value="{{ old('reference') }}" placeholder="e.g. INV-001" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Project (optional)</label>
                <select name="project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                    <option value="">— None —</option>
                    @foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-3"><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" placeholder="What this entry records..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ old('description') }}</textarea></div>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Lines <span class="text-gray-400 normal-case font-normal">— total debit must equal total credit</span></h4>
            <button type="button" onclick="addLine()" class="text-xs font-medium text-gray-600 hover:text-gray-900">+ Add line</button>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100"><th class="px-5 py-2.5 font-semibold">Account</th><th class="px-5 py-2.5 font-semibold w-36">Debit</th><th class="px-5 py-2.5 font-semibold w-36">Credit</th><th class="px-5 py-2.5 font-semibold">Description</th><th class="px-5 py-2.5 w-10"></th></tr></thead>
            <tbody id="linesBody" class="divide-y divide-gray-100"></tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100 flex justify-end gap-8 text-xs">
            <span class="text-gray-500">Total Debit: <span id="totalDebit" class="font-mono font-bold text-gray-900">0.00</span></span>
            <span class="text-gray-500">Total Credit: <span id="totalCredit" class="font-mono font-bold text-gray-900">0.00</span></span>
            <span id="balanceHint" class="font-medium text-gray-400"></span>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors">Post Journal Entry</button>
        <a href="{{ route('admin.journal-entries.index') }}" class="text-xs text-gray-400 hover:text-gray-700">Cancel</a>
    </div>
</form>

<template id="lineTemplate">
    <tr>
        <td class="px-5 py-2.5">
            <select name="lines[__i__][chart_of_account_id]" required class="w-full px-2.5 py-2 rounded border border-gray-200 text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">Select account</option>
                @foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>@endforeach
            </select>
        </td>
        <td class="px-5 py-2.5"><input type="number" step="0.01" min="0" name="lines[__i__][debit]" value="0" oninput="recalc()" class="w-full px-2.5 py-2 rounded border border-gray-200 text-xs outline-none focus:border-gray-400 font-mono"></td>
        <td class="px-5 py-2.5"><input type="number" step="0.01" min="0" name="lines[__i__][credit]" value="0" oninput="recalc()" class="w-full px-2.5 py-2 rounded border border-gray-200 text-xs outline-none focus:border-gray-400 font-mono"></td>
        <td class="px-5 py-2.5"><input name="lines[__i__][description]" class="w-full px-2.5 py-2 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></td>
        <td class="px-5 py-2.5"><button type="button" onclick="this.closest('tr').remove(); recalc();" class="text-gray-400 hover:text-red-600">&times;</button></td>
    </tr>
</template>

<script>
let lineIndex = 0;
function addLine() {
    const tpl = document.getElementById('lineTemplate').innerHTML.replaceAll('__i__', lineIndex++);
    document.getElementById('linesBody').insertAdjacentHTML('beforeend', tpl);
}
function recalc() {
    let d = 0, c = 0;
    document.querySelectorAll('[name^="lines"][name$="[debit]"]').forEach(el => d += parseFloat(el.value || 0));
    document.querySelectorAll('[name^="lines"][name$="[credit]"]').forEach(el => c += parseFloat(el.value || 0));
    document.getElementById('totalDebit').textContent = d.toLocaleString('en-US', {minimumFractionDigits:2});
    document.getElementById('totalCredit').textContent = c.toLocaleString('en-US', {minimumFractionDigits:2});
    const hint = document.getElementById('balanceHint');
    if (d === c && d > 0) { hint.textContent = 'Balanced'; hint.className = 'font-medium text-emerald-600'; }
    else if (d !== c) { hint.textContent = 'Unbalanced'; hint.className = 'font-medium text-red-500'; }
    else { hint.textContent = ''; }
}
addLine(); addLine();
</script>
@endsection
