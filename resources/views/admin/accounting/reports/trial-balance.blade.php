@extends('layouts.admin')
@section('title', 'Trial Balance - ' . config('app.name'))
@section('page_title', 'Trial Balance')
@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.financial-reports.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Reports</a>
    <form method="GET" class="flex items-center gap-2">
        <label class="text-xs text-gray-500">As of</label>
        <input type="date" name="as_of" value="{{ $asOf }}" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none">
    </form>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Code</th><th class="px-5 py-3 font-medium">Account</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium text-right">Debit</th><th class="px-5 py-3 font-medium text-right">Credit</th></tr></thead>
        <tbody>
        @forelse($rows as $row)
        <tr class="border-t border-gray-100">
            <td class="px-5 py-3 text-xs font-mono text-gray-600">{{ $row['account']->code }}</td>
            <td class="px-5 py-3 text-xs text-gray-800">{{ $row['account']->name }}</td>
            <td class="px-5 py-3 text-xs text-gray-400 capitalize">{{ $row['account']->type }}</td>
            <td class="px-5 py-3 text-xs text-right font-semibold text-gray-700">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
            <td class="px-5 py-3 text-xs text-right font-semibold text-gray-700">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No posted transactions yet</td></tr>
        @endforelse
        </tbody>
        @if(count($rows))
        <tfoot>
        <tr class="border-t-2 border-gray-200 bg-gray-50/50 font-semibold">
            <td class="px-5 py-3 text-xs" colspan="3">Total</td>
            <td class="px-5 py-3 text-xs text-right">{{ number_format($total_debit, 2) }}</td>
            <td class="px-5 py-3 text-xs text-right">{{ number_format($total_credit, 2) }}</td>
        </tr>
        </tfoot>
        @endif
    </table></div>
</div>

@if(count($rows) && abs($total_debit - $total_credit) > 0.01)
<div class="mt-4 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded-lg p-3">
    Warning: debits and credits do not match. Review recent journal entries.
</div>
@endif
@endsection
