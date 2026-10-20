@extends('layouts.admin')
@section('title', 'Stock Movements - ' . config('app.name'))
@section('page_title', 'Stock Movements')
@section('content')

@php
    $typeCounts = [
        'in' => \App\Models\StockMovement::where('type', 'in')->count(),
        'out' => \App\Models\StockMovement::where('type', 'out')->count(),
        'adjustment' => \App\Models\StockMovement::where('type', 'adjustment')->count(),
    ];
@endphp

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
        <form method="GET" class="flex items-center gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product or reference..." class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 w-52">
            <select name="product_id" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Products</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
            <select name="warehouse_id" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Warehouses</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                @endforeach
            </select>
            <select name="type" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Types</option>
                <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>In</option>
                <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Out</option>
                <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Filter</button>
            @if(request()->hasAny(['search','product_id','warehouse_id','type']))
            <a href="{{ route('admin.stock-movements.index') }}" class="text-xs text-gray-400 hover:text-gray-600">Clear</a>
            @endif
        </form>
        <div class="flex items-center gap-4 text-[11px] text-gray-400">
            <span><span class="font-semibold text-emerald-600">{{ $typeCounts['in'] }}</span> in</span>
            <span><span class="font-semibold text-red-500">{{ $typeCounts['out'] }}</span> out</span>
            <span><span class="font-semibold text-amber-500">{{ $typeCounts['adjustment'] }}</span> adjustments</span>
        </div>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Product</th><th class="px-5 py-3 font-semibold">Warehouse</th><th class="px-5 py-3 font-semibold">Type</th><th class="px-5 py-3 font-semibold text-right">Qty</th><th class="px-5 py-3 font-semibold text-right">Balance After</th><th class="px-5 py-3 font-semibold">Reference</th><th class="px-5 py-3 font-semibold">By</th><th class="px-5 py-3 font-semibold text-right">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($movements as $m)
        @php
            $tc = ['in' => '#10b981', 'out' => '#ef4444', 'adjustment' => '#f59e0b'][$m->type] ?? '#9ca3af';
            $sign = ['in' => '+', 'out' => '-'][$m->type] ?? '';
        @endphp
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $m->product?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $m->warehouse?->name ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $tc }}"></span>{{ $m->type }}</span></td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold {{ $m->type === 'in' ? 'text-emerald-600' : ($m->type === 'out' ? 'text-red-500' : 'text-amber-500') }}">{{ $sign }}{{ $m->quantity }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-700">{{ $m->balance_after }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-400 max-w-[180px] truncate">{{ $m->reference ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs {{ $m->creator ? 'text-gray-500' : 'text-gray-300' }}">{{ $m->creator?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right text-gray-400">{{ $m->created_at->format('d M Y, H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No stock movements recorded</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($movements->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $movements->links() }}</div>
    @endif
</div>
@endsection
