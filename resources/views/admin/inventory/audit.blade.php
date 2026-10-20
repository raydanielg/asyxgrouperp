@extends('layouts.admin')
@section('title', 'Inventory Audit')
@section('page_title', 'Inventory Integrity Audit')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Negative Stock Balances</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $negative->count() ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $negative->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($negative as $row)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-800">{{ $row->product?->name }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $row->warehouse?->name }}</td>
                    <td class="px-5 py-3 text-xs text-right font-mono text-red-600">{{ number_format($row->quantity) }}</td>
                </tr>
                @empty
                <tr><td class="px-5 py-8 text-center text-xs text-gray-400">No negative balances.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Received GRNs Missing Stock Movement</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $missingMovement->count() ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $missingMovement->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($missingMovement as $grn)
                <tr>
                    <td class="px-5 py-3 text-xs font-mono text-gray-600">{{ $grn->grn_number }}</td>
                    <td class="px-5 py-3 text-xs text-amber-600">Warehouse posting required, no movement found</td>
                </tr>
                @empty
                <tr><td class="px-5 py-8 text-center text-xs text-gray-400">All received GRNs have movement records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Duplicate Movement References</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $duplicates->count() ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $duplicates->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($duplicates as $dup)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-600">{{ $dup->reference }}</td>
                    <td class="px-5 py-3 text-xs text-right font-mono">{{ $dup->cnt }} rows</td>
                </tr>
                @empty
                <tr><td class="px-5 py-8 text-center text-xs text-gray-400">No duplicate references.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Completed Transfers — Missing Movement History</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $transferMissing->count() ? 'bg-gray-100 text-gray-500' : 'bg-emerald-50 text-emerald-600' }}">{{ $transferMissing->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($transferMissing as $t)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-600">Transfer #{{ $t->id }} — {{ $t->product_name }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400">Legacy record — no stock history exists (pre-integration)</td>
                </tr>
                @empty
                <tr><td class="px-5 py-8 text-center text-xs text-gray-400">All product-linked transfers have movements.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100">
            <p class="text-[10px] text-gray-400">Missing-history records are distinguished from verified discrepancies — legacy data predates stock integration and cannot be reconciled automatically.</p>
            <p class="text-xs text-gray-600 mt-2">Unmapped LPO/GRN items awaiting reconciliation: <a href="{{ route('admin.inventory.reconciliation') }}" class="font-medium text-gray-900">{{ $unmappedCount }}</a></p>
        </div>
    </div>

</div>
@endsection
