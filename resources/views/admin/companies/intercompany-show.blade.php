@extends('layouts.admin')
@section('title', $intercompany->transaction_number . ' - ' . config('app.name'))
@section('page_title', $intercompany->transaction_number)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.intercompany.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Intercompany</a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Transaction Details --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="font-bold text-gray-800 mb-4">Transaction Details</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-400">Number</dt><dd class="text-gray-700 font-medium">{{ $intercompany->transaction_number }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">From</dt><dd class="text-gray-700">{{ $intercompany->fromCompany?->legal_name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">To</dt><dd class="text-gray-700">{{ $intercompany->toCompany?->legal_name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Type</dt><dd class="text-gray-700 capitalize">{{ str_replace('_', ' ', $intercompany->type) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Amount</dt><dd class="text-gray-700 font-bold">{{ number_format($intercompany->amount, 0) }} {{ $intercompany->currency }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Date</dt><dd class="text-gray-700">{{ $intercompany->transaction_date->format('d M Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Status</dt><dd><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $intercompany->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : ($intercompany->status === 'eliminated' ? 'bg-gray-50 text-gray-500 border border-gray-100' : 'bg-amber-50 text-amber-700 border border-amber-100') }}">{{ $intercompany->status }}</span></dd></div>
            @if($intercompany->eliminated_at)
            <div class="flex justify-between"><dt class="text-gray-400">Eliminated At</dt><dd class="text-gray-700">{{ $intercompany->eliminated_at->format('d M Y H:i') }}</dd></div>
            @endif
        </dl>
        @if($intercompany->description)
        <div class="mt-4 pt-4 border-t">
            <p class="text-xs text-gray-400 mb-1">Description</p>
            <p class="text-sm text-gray-700">{{ $intercompany->description }}</p>
        </div>
        @endif
        <div class="mt-4 flex gap-2">
            @if($intercompany->status === 'completed')
            <form method="POST" action="{{ route('admin.intercompany.eliminate', $intercompany) }}" onsubmit="return confirm('Mark as eliminated for consolidation?')">@csrf<button class="px-3 py-1.5 bg-amber-600 text-white text-xs font-medium rounded-lg hover:bg-amber-700">Eliminate</button></form>
            @endif
            <form method="POST" action="{{ route('admin.intercompany.destroy', $intercompany) }}" onsubmit="return confirm('Delete this transaction?')">@csrf @method('DELETE')<button class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700">Delete</button></form>
        </div>
    </div>

    {{-- Line Items --}}
    <div class="bg-white rounded-xl border overflow-hidden lg:col-span-2">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h4 class="text-sm font-bold text-gray-700">Line Items</h4></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Description</th><th class="px-4 py-2 font-medium text-right">Qty</th><th class="px-4 py-2 font-medium text-right">Unit Price</th><th class="px-4 py-2 font-medium text-right">Total</th></tr></thead>
            <tbody>
            @forelse($intercompany->lines as $line)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs text-gray-700">{{ $line->description }}</td>
                <td class="px-4 py-2 text-xs text-right text-gray-600">{{ number_format($line->quantity, 2) }}</td>
                <td class="px-4 py-2 text-xs text-right text-gray-600">{{ number_format($line->unit_price, 0) }}</td>
                <td class="px-4 py-2 text-xs text-right font-medium text-gray-700">{{ number_format($line->line_total, 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No line items</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
</div>
@endsection
