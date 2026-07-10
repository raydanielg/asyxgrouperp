@extends('layouts.admin')
@section('title', 'Parking Revenue Reports - SGR - ' . config('app.name'))
@section('page_title', 'SGR Parking Revenue Reports')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-amber-50 text-amber-700 text-sm border border-amber-100">{{ session('success') }}</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.sgr.parking-revenue.import') }}" class="text-xs text-gray-500 hover:text-amber-600">&larr; Import</a>
    <a href="{{ route('admin.sgr.parking-revenue.index') }}" class="text-xs text-gray-500 hover:text-amber-600">Dashboard</a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-amber-600">{{ number_format($summary['total']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Records</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-emerald-600">{{ number_format($summary['total_collected'], 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Collected</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-sky-600">{{ number_format($summary['total_deposited'], 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Deposited</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-rose-600">{{ number_format($summary['total_difference'], 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Difference</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl border p-5">
        <h4 class="text-sm font-bold text-gray-900 mb-3">By Cashier</h4>
        <div class="space-y-2">
            @forelse($summary['by_cashier'] as $item)
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600">{{ $item->cashier_name }}</span>
                <span class="font-semibold text-gray-900">{{ number_format($item->collected, 2) }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-4">No data</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border p-5">
        <h4 class="text-sm font-bold text-gray-900 mb-3">By Booth</h4>
        <div class="space-y-2">
            @forelse($summary['by_booth'] as $item)
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600">{{ $item->booth }}</span>
                <span class="font-semibold text-gray-900">{{ number_format($item->collected, 2) }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-4">No data</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border p-5">
        <h4 class="text-sm font-bold text-gray-900 mb-3">By Date</h4>
        <div class="space-y-2 max-h-60 overflow-y-auto">
            @forelse($summary['by_date'] as $item)
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600">{{ \Carbon\Carbon::parse($item->date_in)->format('d M Y') }}</span>
                <span class="font-semibold text-gray-900">{{ number_format($item->collected, 2) }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-4">No data</p>
            @endforelse
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border p-5 mb-6">
    <h4 class="text-sm font-bold text-gray-900 mb-3">Filter Records</h4>
    <form method="GET" action="{{ route('admin.sgr.parking-revenue.reports') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Import Batch</label>
            <select name="batch" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
                <option value="">All batches</option>
                @foreach($batches as $b)
                <option value="{{ $b }}" @selected($batch === $b)>{{ $b }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Cashier</label>
            <input type="text" name="cashier" value="{{ $cashier }}" placeholder="Search cashier..." class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Booth / Comment</label>
            <input type="text" name="booth" value="{{ $booth }}" placeholder="e.g. Booth 1" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Date From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Date To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
        </div>
        <div class="md:col-span-5 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700">Apply Filters</button>
            <a href="{{ route('admin.sgr.parking-revenue.reports') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Clear</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-700">Revenue Records</h4>
        <span class="text-xs text-gray-500">{{ $items->total() }} records</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50">
                    <th class="px-4 py-2 font-medium">SN</th>
                    <th class="px-4 py-2 font-medium">Date In</th>
                    <th class="px-4 py-2 font-medium">Date Out</th>
                    <th class="px-4 py-2 font-medium">Time</th>
                    <th class="px-4 py-2 font-medium">Cashier</th>
                    <th class="px-4 py-2 font-medium">Booth</th>
                    <th class="px-4 py-2 font-medium text-right">Collected</th>
                    <th class="px-4 py-2 font-medium text-right">Deposited</th>
                    <th class="px-4 py-2 font-medium text-right">Difference</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2 text-xs text-gray-400">{{ $item->sn }}</td>
                    <td class="px-4 py-2 text-xs text-gray-700">{{ $item->date_in?->format('d M Y') ?? '—' }}</td>
                    <td class="px-4 py-2 text-xs text-gray-700">{{ $item->date_out?->format('d M Y') ?? '—' }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $item->time_in ? $item->time_in . ' - ' . $item->time_out : '—' }}</td>
                    <td class="px-4 py-2 text-xs text-gray-700">{{ $item->cashier_name ?? '—' }}</td>
                    <td class="px-4 py-2 text-xs text-gray-600">{{ $item->comments ?? '—' }}</td>
                    <td class="px-4 py-2 text-xs text-emerald-700 text-right">{{ number_format($item->amount_collected, 2) }}</td>
                    <td class="px-4 py-2 text-xs text-sky-700 text-right">{{ number_format($item->amount_deposited, 2) }}</td>
                    <td class="px-4 py-2 text-xs text-rose-600 text-right">{{ number_format($item->difference, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-400 text-xs">No records found. <a href="{{ route('admin.sgr.parking-revenue.import') }}" class="text-amber-600 hover:text-amber-700">Import now</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $items->links() }}</div>
</div>
@endsection
