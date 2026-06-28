@extends('layouts.admin')
@section('title', 'Intercompany Transactions - ' . config('app.name'))
@section('page_title', 'Intercompany Transactions')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track transactions between group companies</p>
    <a href="{{ route('admin.intercompany.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Transaction
    </a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Number</th>
            <th class="px-5 py-3 font-medium">From</th>
            <th class="px-5 py-3 font-medium">To</th>
            <th class="px-5 py-3 font-medium">Type</th>
            <th class="px-5 py-3 font-medium text-right">Amount</th>
            <th class="px-5 py-3 font-medium">Date</th>
            <th class="px-5 py-3 font-medium">Status</th>
            <th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($transactions as $ict)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs"><a href="{{ route('admin.intercompany.show', $ict) }}" class="text-emerald-600 hover:underline font-medium">{{ $ict->transaction_number }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $ict->fromCompany?->short_code ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $ict->toCompany?->short_code ?? '—' }}</td>
            <td class="px-5 py-3 text-xs"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100 capitalize">{{ str_replace('_', ' ', $ict->type) }}</span></td>
            <td class="px-5 py-3 text-xs text-right font-medium">{{ number_format($ict->amount, 0) }} {{ $ict->currency }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $ict->transaction_date->format('d M Y') }}</td>
            <td class="px-5 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $ict->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : ($ict->status === 'eliminated' ? 'bg-gray-50 text-gray-500 border border-gray-100' : 'bg-amber-50 text-amber-700 border border-amber-100') }}">{{ $ict->status }}</span>
            </td>
            <td class="px-5 py-3 flex items-center gap-3">
                <a href="{{ route('admin.intercompany.show', $ict) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">View</a>
                @if($ict->status === 'completed')
                <form method="POST" action="{{ route('admin.intercompany.eliminate', $ict) }}" class="inline" onsubmit="return confirm('Mark as eliminated for consolidation?')">@csrf<button class="text-amber-600 hover:text-amber-700 text-xs">Eliminate</button></form>
                @endif
                <form method="POST" action="{{ route('admin.intercompany.destroy', $ict) }}" class="inline" onsubmit="return confirm('Delete this transaction?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs">Delete</button></form>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No intercompany transactions found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $transactions->links() }}</div>
</div>
@endsection
