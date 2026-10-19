@extends('layouts.admin')
@section('title', 'Journal Entry - ' . config('app.name'))
@section('page_title', 'Journal Entry ' . $journalEntry->entry_number)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.journal-entries.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Journal Entries</a>
</div>

<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Entry #</p><p class="font-mono text-gray-900 mt-1">{{ $journalEntry->entry_number }}</p></div>
        <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Date</p><p class="text-gray-800 mt-1">{{ $journalEntry->entry_date->format('d M Y') }}</p></div>
        <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Source</p><p class="text-gray-800 mt-1">{{ str_replace('_', ' ', $journalEntry->source_type) }}</p></div>
        <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Project</p><p class="text-gray-800 mt-1">{{ $journalEntry->project?->title ?? '—' }}</p></div>
    </div>
    @if($journalEntry->description)
    <div class="mt-4 pt-4 border-t border-gray-100"><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Description</p><p class="text-xs text-gray-600 mt-1">{{ $journalEntry->description }}</p></div>
    @endif
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200"><th class="px-5 py-3 font-semibold">Account</th><th class="px-5 py-3 font-semibold">Description</th><th class="px-5 py-3 font-semibold">Project</th><th class="px-5 py-3 font-semibold text-right">Debit</th><th class="px-5 py-3 font-semibold text-right">Credit</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
        @foreach($journalEntry->lines as $line)
        <tr>
            <td class="px-5 py-3.5 text-xs text-gray-700">{{ $line->chartOfAccount->code }} - {{ $line->chartOfAccount->name }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $line->description ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $line->project?->title ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono {{ $line->debit > 0 ? 'text-gray-900' : 'text-gray-300' }}">{{ $line->debit > 0 ? number_format($line->debit, 2) : '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono {{ $line->credit > 0 ? 'text-gray-900' : 'text-gray-300' }}">{{ $line->credit > 0 ? number_format($line->credit, 2) : '—' }}</td>
        </tr>
        @endforeach
        <tr class="border-t-2 border-gray-800 font-bold">
            <td class="px-5 py-3.5 text-xs" colspan="3">Total</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format($journalEntry->lines->sum('debit'), 2) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format($journalEntry->lines->sum('credit'), 2) }}</td>
        </tr>
        </tbody>
    </table></div>
</div>
@endsection
