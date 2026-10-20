@extends('layouts.admin')
@section('title', 'POS Reports - ' . config('app.name'))
@section('page_title', 'POS Reports')
@section('content')
@php
    $methods = ['cash' => '#10b981', 'card' => '#3b82f6', 'mobile' => '#f59e0b'];
@endphp

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
        <span class="text-xs text-gray-400">POS sales performance</span>
        <a href="{{ route('admin.pos.index') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Open Terminal</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Today's Sales</p><p class="text-lg font-bold text-gray-900 mt-0.5">TZS {{ number_format($todayTotal) }}</p><p class="text-[10px] text-gray-400 mt-0.5">{{ $todayCount }} sale{{ $todayCount === 1 ? '' : 's' }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">This Month</p><p class="text-lg font-bold text-gray-900 mt-0.5">TZS {{ number_format($monthTotal) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">All Time</p><p class="text-lg font-bold text-gray-900 mt-0.5">TZS {{ number_format($allTotal) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">By Method</p><div class="mt-1 space-y-0.5">@foreach(['cash','card','mobile'] as $m)<p class="text-[11px] text-gray-600 flex items-center gap-1.5"><span style="width:6px;height:6px;border-radius:99px;background:{{ $methods[$m] }}"></span>{{ ucfirst($m) }} <span class="font-mono text-gray-800">· {{ number_format($methodTotals[$m] ?? 0) }}</span></p>@endforeach</div></div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
        <form method="GET" class="flex items-center gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search sale #..." class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 w-44">
            <select name="method" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Methods</option>
                <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="card" {{ request('method') === 'card' ? 'selected' : '' }}>Card</option>
                <option value="mobile" {{ request('method') === 'mobile' ? 'selected' : '' }}>Mobile</option>
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400">
            <button type="submit" class="px-4 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Filter</button>
            @if(request()->hasAny(['search','method','date']))
            <a href="{{ route('admin.pos.reports') }}" class="text-xs text-gray-400 hover:text-gray-600">Clear</a>
            @endif
        </form>
        <span class="text-xs text-gray-400">{{ $sales->total() }} sales</span>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Sale #</th>
                <th class="px-5 py-3 font-semibold text-right">Items</th>
                <th class="px-5 py-3 font-semibold text-right">Total</th>
                <th class="px-5 py-3 font-semibold">Payment</th>
                <th class="px-5 py-3 font-semibold">Cashier</th>
                <th class="px-5 py-3 font-semibold text-right">Date</th>
                <th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($sales as $s)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.pos.show', $s) }}" class="text-xs font-mono font-medium text-gray-900 hover:text-gray-600">{{ $s->sale_number }}</a></td>
            <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-500">{{ $s->items->count() }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold text-gray-900">TZS {{ number_format($s->total_amount) }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $methods[$s->payment_method] ?? '#9ca3af' }}"></span>{{ ucfirst($s->payment_method) }}</span></td>
            <td class="px-5 py-3.5 text-xs {{ $s->cashier ? 'text-gray-500' : 'text-gray-300' }}">{{ $s->cashier?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right text-gray-400">{{ $s->created_at->format('d M Y, H:i') }}</td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.pos.show', $s) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <form id="del-pos-{{ $s->id }}" method="POST" action="{{ route('admin.pos.destroy', $s) }}" class="inline">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-pos-{{ $s->id }}')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No sales recorded</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($sales->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $sales->links() }}</div>
    @endif
</div>
@endsection
