@extends('layouts.admin')
@section('title', 'Products - ' . config('app.name'))
@section('page_title', 'Products')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage products and services</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Product
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Code</th><th class="px-5 py-3 font-medium">Name</th><th class="px-5 py-3 font-medium">Category</th><th class="px-5 py-3 font-medium">Purchase Price</th><th class="px-5 py-3 font-medium">Sale Price</th><th class="px-5 py-3 font-medium">Stock</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($products as $p)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $p->product_code }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $p->name }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $p->category?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">TZS {{ number_format($p->purchase_price) }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-emerald-700">TZS {{ number_format($p->sale_price) }}</td>
            <td class="px-5 py-3">
        @if($p->stock_quantity <= $p->reorder_level && $p->reorder_level > 0)
        <span class="text-xs font-bold text-red-600">{{ $p->stock_quantity }} {{ $p->unit }}</span><span class="ml-1 text-[9px] text-red-500">LOW</span>
        @else
        <span class="text-xs text-gray-700">{{ $p->stock_quantity }} {{ $p->unit }}</span>
        @endif</td>
            <td class="px-5 py-3">
        @if($p->is_active)
        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700">Active</span>
        @else
        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600">Inactive</span>
        @endif</td>
            <td class="px-5 py-3"><form id="del-prod-{{ $p->id }}" method="POST" action="{{ route('admin.products.destroy', $p) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-prod-{{ $p->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No products found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $products->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Product</h3>
        <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><select name="category_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">None</option>
        @foreach($categories as $c)
        <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
        </select></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Warehouse</label><select name="warehouse_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">None</option>
        @foreach($warehouses as $w)
        <option value="{{ $w->id }}">{{ $w->name }}</option>
        @endforeach
        </select></div></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Purchase Price</label><input name="purchase_price" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Sale Price</label><input name="sale_price" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div class="grid grid-cols-3 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Stock Qty</label><input name="stock_quantity" type="number" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Reorder Level</label><input name="reorder_level" type="number" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Unit</label><input name="unit" value="piece" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Type</label><select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="product">Product</option><option value="service">Service</option></select></div>
            <div class="flex items-center gap-2"><input type="checkbox" name="is_active" checked class="rounded border-gray-300 text-emerald-600"><label class="text-xs text-gray-600">Active</label></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
