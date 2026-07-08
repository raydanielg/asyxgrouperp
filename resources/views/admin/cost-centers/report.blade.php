@extends('layouts.admin')
@section('title', 'Cost Allocation Report - ' . config('app.name'))
@section('page_title', 'Cost Allocation Report')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.cost-centers.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Cost Centers</a>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($summary as $s)
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-lg font-bold text-emerald-600">TZS {{ number_format($s->total, 0) }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $s->costCenter?->name ?? 'Deleted' }}</p>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border p-4 mb-6">
    <form method="GET" action="{{ route('admin.cost-centers.report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Cost Center</label>
            <select name="cost_center_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <option value="">All Centers</option>
                @foreach($costCenters as $cc)
                <option value="{{ $cc->id }}" @selected($costCenterId == $cc->id)>{{ $cc->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Date From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Date To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Filter</button>
            <a href="{{ route('admin.cost-centers.report') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Clear</a>
        </div>
    </form>
</div>

{{-- Allocations Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-700">Cost Allocations</h4>
        <span class="text-xs text-gray-500">{{ $allocations->total() }} records</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50">
                    <th class="px-4 py-2 font-medium">Date</th>
                    <th class="px-4 py-2 font-medium">Transaction</th>
                    <th class="px-4 py-2 font-medium">Cost Center</th>
                    <th class="px-4 py-2 font-medium">Allocated Amount</th>
                    <th class="px-4 py-2 font-medium">%</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allocations as $alloc)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2 text-xs text-gray-500">
                        {{ $alloc->costAllocatable?->expense_date?->format('d M Y') ?? $alloc->costAllocatable?->created_at?->format('d M Y') ?? '—' }}
                    </td>
                    <td class="px-4 py-2 text-xs text-gray-700">
                        {{ $alloc->costAllocatable?->expense_number ?? $alloc->costAllocatable?->category ?? '#' . $alloc->cost_allocatable_id }}
                        <span class="text-[10px] text-gray-400">({{ class_basename($alloc->cost_allocatable_type) }})</span>
                    </td>
                    <td class="px-4 py-2 text-xs font-medium text-gray-900">{{ $alloc->costCenter?->name ?? 'Deleted' }}</td>
                    <td class="px-4 py-2 text-xs font-semibold text-red-600">TZS {{ number_format($alloc->amount, 0) }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ number_format($alloc->percentage, 1) }}%</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-xs">No cost allocations found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $allocations->links() }}</div>
</div>
@endsection