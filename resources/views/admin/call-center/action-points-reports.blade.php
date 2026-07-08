@extends('layouts.admin')
@section('title', 'Call Center Action Points Reports - ' . config('app.name'))
@section('page_title', 'Call Center Action Points Reports')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.call-center.action-points.import') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Import</a>
    <a href="{{ route('admin.call-center.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">Call Center Dashboard</a>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-emerald-600">{{ number_format($summary['total']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Action Points</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-amber-600">{{ number_format($summary['overdue']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Overdue</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-sky-600">{{ $summary['by_responsible']->count() }}</p>
        <p class="text-xs text-gray-500 mt-1">Responsible Persons</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-purple-600">{{ $summary['by_status']->count() }}</p>
        <p class="text-xs text-gray-500 mt-1">Status Categories</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- By Status --}}
    <div class="bg-white rounded-xl border p-5">
        <h4 class="text-sm font-bold text-gray-900 mb-3">By Status</h4>
        <div class="space-y-2">
            @forelse($summary['by_status'] as $item)
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600">{{ $item->status ?: 'No status' }}</span>
                <span class="font-semibold text-gray-900">{{ $item->total }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-4">No data</p>
            @endforelse
        </div>
    </div>

    {{-- By Responsible Person --}}
    <div class="bg-white rounded-xl border p-5 lg:col-span-2">
        <h4 class="text-sm font-bold text-gray-900 mb-3">By Responsible Person</h4>
        <div class="space-y-2">
            @forelse($summary['by_responsible'] as $item)
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600">{{ $item->responsible_person ?: 'Unassigned' }}</span>
                <span class="font-semibold text-gray-900">{{ $item->total }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-4">No data</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border p-5 mb-6">
    <h4 class="text-sm font-bold text-gray-900 mb-3">Filter Action Points</h4>
    <form method="GET" action="{{ route('admin.call-center.action-points.reports') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Import Batch</label>
            <select name="batch" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <option value="">All batches</option>
                @foreach($batches as $b)
                <option value="{{ $b }}" @selected($batch === $b)>{{ $b }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Responsible Person</label>
            <input type="text" name="responsible" value="{{ $responsible }}" placeholder="Search name..." class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Status</label>
            <input type="text" name="status" value="{{ $status }}" placeholder="e.g. ACHIEVED" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Due From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Due To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        @if($isAdmin)
        <div>
            <label class="block text-[10px] text-gray-500 mb-1">Approval Status</label>
            <select name="approval_status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <option value="">All</option>
                <option value="pending" @selected($approvalStatus === 'pending')>Pending</option>
                <option value="approved" @selected($approvalStatus === 'approved')>Approved</option>
                <option value="rejected" @selected($approvalStatus === 'rejected')>Rejected</option>
            </select>
        </div>
        @endif
        <div class="md:col-span-5 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Apply Filters</button>
            <a href="{{ route('admin.call-center.action-points.reports') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Clear</a>
        </div>
    </form>
</div>

{{-- Action Points List --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-700">Action Points</h4>
        <span class="text-xs text-gray-500">{{ $items->total() }} records</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50">
                    <th class="px-4 py-2 font-medium">Activity</th>
                    <th class="px-4 py-2 font-medium">Responsible Person</th>
                    <th class="px-4 py-2 font-medium">Due Date</th>
                    <th class="px-4 py-2 font-medium">Status</th>
                    <th class="px-4 py-2 font-medium">Approval</th>
                    <th class="px-4 py-2 font-medium">Batch</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2 text-xs text-gray-700 max-w-md truncate">{{ $item->activity ?: '—' }}</td>
                    <td class="px-4 py-2 text-xs text-gray-700">{{ $item->responsible_person ?: '—' }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $item->due_date?->format('d M Y') ?: '—' }}</td>
                    <td class="px-4 py-2 text-xs">
                        @php
                        $status = strtolower($item->status ?? '');
                        $color = match(true) {
                            str_contains($status, 'achieved') || str_contains($status, 'done') || str_contains($status, 'completed') => 'bg-emerald-50 text-emerald-700',
                            str_contains($status, 'pending') || str_contains($status, 'progress') => 'bg-amber-50 text-amber-700',
                            str_contains($status, 'overdue') || str_contains($status, 'missed') => 'bg-red-50 text-red-700',
                            default => 'bg-gray-50 text-gray-600',
                        };
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $color }}">{{ $item->status ?: 'No status' }}</span>
                    </td>
                    <td class="px-4 py-2 text-[10px] text-gray-400 font-mono">{{ $item->import_batch }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-xs">No action points found. <a href="{{ route('admin.call-center.action-points.import') }}" class="text-emerald-600 hover:text-emerald-700">Import now</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $items->links() }}</div>
</div>
@endsection
