@extends('layouts.admin')
@section('title', 'Product Reconciliation')
@section('page_title', 'Legacy Product Reconciliation')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-xs text-gray-500">LPO/GRN items missing product mapping — assign a product manually to enable stock posting. Already-posted items cannot be remapped.</p>
    <a href="{{ route('admin.inventory.audit') }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Audit Report →</a>
</div>

<div class="bg-white rounded-md border border-gray-200 mb-4">
    <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-800">LPO Items ({{ $unmappedLpoItems->total() }})</h3></div>
    <table class="w-full">
        <thead><tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100">
            <th class="px-5 py-2.5">LPO</th><th class="px-5 py-2.5">Description</th><th class="px-5 py-2.5">Qty Ordered</th><th class="px-5 py-2.5">Map to Product</th><th class="px-5 py-2.5 text-right">Action</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($unmappedLpoItems as $item)
            <tr>
                <td class="px-5 py-3 text-xs font-mono text-gray-600">{{ $item->lpo?->lpo_number }}</td>
                <td class="px-5 py-3 text-xs text-gray-800">{{ $item->description }}</td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ number_format($item->quantity_ordered) }}</td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('admin.inventory.map') }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="item_type" value="lpo_item">
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <input type="hidden" name="reason" value="Manual reconciliation">
                        <select name="product_id" required class="px-2 py-1 text-xs border border-gray-200 rounded bg-white w-48">
                            <option value="">— Select product —</option>
                            @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->product_code }})</option>@endforeach
                        </select>
                </td>
                <td class="px-5 py-3 text-right"><button class="text-xs font-medium text-emerald-700 hover:text-emerald-900">Map</button></form></td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-8 text-center text-xs text-gray-400">All LPO items are mapped.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-3">{{ $unmappedLpoItems->links() }}</div>
</div>

<div class="bg-white rounded-md border border-gray-200 mb-4">
    <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-800">Products Missing Warehouse ({{ $unmappedProducts->count() }})</h3>
        <p class="text-[10px] text-gray-400 mt-1">POS cannot sell these — assign a warehouse to enable stock verification.</p>
    </div>
    <table class="w-full">
        <thead><tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100">
            <th class="px-5 py-2.5">Product</th><th class="px-5 py-2.5">Code</th><th class="px-5 py-2.5">Assign Warehouse</th><th class="px-5 py-2.5 text-right">Action</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($unmappedProducts as $p)
            <tr>
                <td class="px-5 py-3 text-xs text-gray-800">{{ $p->name }}</td>
                <td class="px-5 py-3 text-xs font-mono text-gray-400">{{ $p->product_code }}</td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('admin.inventory.assign-warehouse') }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $p->id }}">
                        <input type="hidden" name="reason" value="Warehouse reconciliation — enable POS stock verification">
                        <select name="warehouse_id" required class="px-2 py-1 text-xs border border-gray-200 rounded bg-white w-48">
                            <option value="">— Select warehouse —</option>
                            @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
                        </select>
                </td>
                <td class="px-5 py-3 text-right"><button class="text-xs font-medium text-emerald-700 hover:text-emerald-900">Assign</button></form></td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-8 text-center text-xs text-gray-400">All products have warehouse assignments.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white rounded-md border border-gray-200">
    <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-800">GRN Items</h3></div>
    <table class="w-full">
        <thead><tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100">
            <th class="px-5 py-2.5">GRN</th><th class="px-5 py-2.5">Description</th><th class="px-5 py-2.5">Status</th><th class="px-5 py-2.5">Map to Product</th><th class="px-5 py-2.5 text-right">Action</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($unmappedGrnItems as $item)
            <tr>
                <td class="px-5 py-3 text-xs font-mono text-gray-600">{{ $item->grn?->grn_number }}</td>
                <td class="px-5 py-3 text-xs text-gray-800">{{ $item->description }}</td>
                <td class="px-5 py-3 text-xs">
                    @if($item->grn?->status === 'received' && $item->grn?->warehouse_id)
                    <span class="text-red-500 font-medium">Posted — locked</span>
                    @else
                    <span class="text-gray-500">{{ ucfirst($item->grn?->status ?? '—') }}</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    @if(!($item->grn?->status === 'received' && $item->grn?->warehouse_id))
                    <form method="POST" action="{{ route('admin.inventory.map') }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="item_type" value="grn_item">
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <input type="hidden" name="reason" value="Manual reconciliation">
                        <select name="product_id" required class="px-2 py-1 text-xs border border-gray-200 rounded bg-white w-48">
                            <option value="">— Select product —</option>
                            @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->product_code }})</option>@endforeach
                        </select>
                    @else
                    <span class="text-[10px] text-gray-400">Use corrective stock adjustment</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-right">@if(!($item->grn?->status === 'received' && $item->grn?->warehouse_id))<button class="text-xs font-medium text-emerald-700 hover:text-emerald-900">Map</button></form>@endif</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-8 text-center text-xs text-gray-400">All GRN items are mapped.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
