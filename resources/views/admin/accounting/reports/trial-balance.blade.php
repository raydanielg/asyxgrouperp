@extends('layouts.admin')
@section('title', 'Trial Balance - ' . config('app.name'))
@section('page_title', 'Trial Balance')
@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.financial-reports.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Reports</a>
    <form method="GET" class="flex items-center gap-2">
        <label class="text-xs text-gray-500">As of</label>
        <input type="date" name="as_of" value="{{ $asOf }}" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400">
    </form>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200"><th class="px-5 py-3 font-semibold">Code</th><th class="px-5 py-3 font-semibold">Account</th><th class="px-5 py-3 font-semibold">Type</th><th class="px-5 py-3 font-semibold text-right">Debit</th><th class="px-5 py-3 font-semibold text-right">Credit</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($rows as $row)
        <tr>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-600">{{ $row['account']->code }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-800">{{ $row['account']->name }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-400 capitalize">{{ $row['account']->type }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono {{ $row['debit'] > 0 ? 'text-gray-900' : 'text-gray-300' }}">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono {{ $row['credit'] > 0 ? 'text-gray-900' : 'text-gray-300' }}">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-xs text-gray-400">No posted transactions yet</td></tr>
        @endforelse
        </tbody>
        @if(count($rows))
        <tfoot>
        <tr class="border-t-2 border-gray-800 font-bold">
            <td class="px-5 py-3.5 text-xs" colspan="3">Total</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format($total_debit, 2) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format($total_credit, 2) }}</td>
        </tr>
        </tfoot>
        @endif
    </table></div>
</div>

@if(count($rows) && abs($total_debit - $total_credit) > 0.01)
<div class="mt-4 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded p-3">
    Warning: debits and credits do not match. Review recent journal entries.
</div>
@endif
@endsection
