@extends('layouts.admin')
@section('title', 'New Journal Entry - ' . config('app.name'))
@section('page_title', 'New Journal Entry')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.journal-entries.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Journal Entries</a>
</div>

@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('admin.journal-entries.store') }}" class="bg-white rounded-xl border p-6 space-y-4" id="jeForm">
    @csrf
    <div class="grid grid-cols-3 gap-3">
        <div><label class="block text-xs font-medium text-gray-600 mb-1">Entry Date *</label><input name="entry_date" type="date" required value="{{ old('entry_date', date('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-600 mb-1">Reference</label><input name="reference" value="{{ old('reference') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
        <div><label class="block text-xs font-medium text-gray-600 mb-1">Project (optional)</label>
            <select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                <option value="">None</option>
                @foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach
            </select>
        </div>
    </div>
    <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">{{ old('description') }}</textarea></div>

    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-medium text-gray-600">Lines (must balance: total debit = total credit)</label>
            <button type="button" onclick="addLine()" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">+ Add line</button>
        </div>
        <table class="w-full text-sm border rounded-lg overflow-hidden">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50"><th class="px-3 py-2">Account</th><th class="px-3 py-2 w-32">Debit</th><th class="px-3 py-2 w-32">Credit</th><th class="px-3 py-2">Description</th><th class="px-3 py-2 w-10"></th></tr></thead>
            <tbody id="linesBody"></tbody>
        </table>
        <div class="flex justify-end gap-6 mt-2 text-xs text-gray-600">
            <span>Total Debit: <span id="totalDebit" class="font-semibold">0</span></span>
            <span>Total Credit: <span id="totalCredit" class="font-semibold">0</span></span>
        </div>
    </div>

    <div class="pt-2"><button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Post Journal Entry</button></div>
</form>

<template id="lineTemplate">
    <tr class="border-t">
        <td class="px-3 py-2">
            <select name="lines[__i__][chart_of_account_id]" required class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none">
                <option value="">Select account</option>
                @foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>@endforeach
            </select>
        </td>
        <td class="px-3 py-2"><input type="number" step="0.01" min="0" name="lines[__i__][debit]" value="0" oninput="recalc()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
        <td class="px-3 py-2"><input type="number" step="0.01" min="0" name="lines[__i__][credit]" value="0" oninput="recalc()" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
        <td class="px-3 py-2"><input name="lines[__i__][description]" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:border-emerald-500 outline-none"></td>
        <td class="px-3 py-2"><button type="button" onclick="this.closest('tr').remove(); recalc();" class="text-red-500 hover:text-red-700 text-xs">&times;</button></td>
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
    document.getElementById('totalDebit').textContent = d.toFixed(2);
    document.getElementById('totalCredit').textContent = c.toFixed(2);
}
addLine(); addLine();
</script>
@endsection
