@extends('layouts.admin')
@section('title', 'Journal Entry - ' . config('app.name'))
@section('page_title', 'Journal Entry ' . $journalEntry->entry_number)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.journal-entries.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Journal Entries</a>
</div>

<div class="bg-white rounded-xl border p-6 mb-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><p class="text-[10px] text-gray-400 uppercase">Entry #</p><p class="font-mono text-emerald-700">{{ $journalEntry->entry_number }}</p></div>
        <div><p class="text-[10px] text-gray-400 uppercase">Date</p><p>{{ $journalEntry->entry_date->format('d M Y') }}</p></div>
        <div><p class="text-[10px] text-gray-400 uppercase">Source</p><p>{{ str_replace('_', ' ', $journalEntry->source_type) }}</p></div>
        <div><p class="text-[10px] text-gray-400 uppercase">Project</p><p>{{ $journalEntry->project?->title ?? '—' }}</p></div>
    </div>
    @if($journalEntry->description)
    <div class="mt-3"><p class="text-[10px] text-gray-400 uppercase">Description</p><p class="text-sm text-gray-700">{{ $journalEntry->description }}</p></div>
    @endif
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Account</th><th class="px-5 py-3 font-medium">Description</th><th class="px-5 py-3 font-medium">Project</th><th class="px-5 py-3 font-medium text-right">Debit</th><th class="px-5 py-3 font-medium text-right">Credit</th></tr></thead>
        <tbody>
        @foreach($journalEntry->lines as $line)
        <tr class="border-t border-gray-100">
            <td class="px-5 py-3 text-xs text-gray-700">{{ $line->chartOfAccount->code }} - {{ $line->chartOfAccount->name }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $line->description ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $line->project?->title ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-right font-semibold text-gray-700">{{ $line->debit > 0 ? number_format($line->debit, 2) : '' }}</td>
            <td class="px-5 py-3 text-xs text-right font-semibold text-gray-700">{{ $line->credit > 0 ? number_format($line->credit, 2) : '' }}</td>
        </tr>
        @endforeach
        <tr class="border-t-2 border-gray-200 bg-gray-50/50 font-semibold">
            <td class="px-5 py-3 text-xs" colspan="3">Total</td>
            <td class="px-5 py-3 text-xs text-right">{{ number_format($journalEntry->lines->sum('debit'), 2) }}</td>
            <td class="px-5 py-3 text-xs text-right">{{ number_format($journalEntry->lines->sum('credit'), 2) }}</td>
        </tr>
        </tbody>
    </table></div>
</div>
@endsection
