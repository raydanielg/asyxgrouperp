@extends('layouts.admin')
@section('title', 'Products - ' . config('app.name'))
@section('page_title', 'Products')
@section('content')

{{-- ═══ Current Company Banner ═══ --}}
<div class="mb-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
        @php
            $currentCompany = $isGroupUser ? collect($companies)->firstWhere('id', $currentCompanyId) : $userCompany;
        @endphp
        @if($currentCompany)
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-100">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5"/></svg>
            <span class="text-xs font-medium text-emerald-700">{{ $currentCompany->name }}</span>
            @if($currentCompany->is_group)<span class="text-[9px] px-1.5 py-0.5 rounded-full bg-gold-100 text-gold-700">Group</span>@endif
        </div>
        @endif
        <p class="text-sm text-gray-500">Manage products and services</p>
    </div>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Product
    </button>
</div>

{{-- ═══ Products Table ═══ --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Code</th>
            <th class="px-5 py-3 font-medium">Name</th>
            <th class="px-5 py-3 font-medium">Category</th>
            <th class="px-5 py-3 font-medium">Purchase Price</th>
            <th class="px-5 py-3 font-medium">Sale Price</th>
            <th class="px-5 py-3 font-medium">Stock</th>
            @if($isGroupUser)<th class="px-5 py-3 font-medium">Company</th>@endif
            <th class="px-5 py-3 font-medium">Status</th>
            <th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
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
                @endif
            </td>
            @if($isGroupUser)
            <td class="px-5 py-3">
                @if($p->company)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $p->company->short_code }}</span>
                @else
                <span class="text-[10px] text-gray-400">N/A</span>
                @endif
            </td>
            @endif
            <td class="px-5 py-3">
                @if($p->is_active)
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700">Active</span>
                @else
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600">Inactive</span>
                @endif
            </td>
            <td class="px-5 py-3">
                <form id="del-prod-{{ $p->id }}" method="POST" action="{{ route('admin.products.destroy', $p) }}">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-prod-{{ $p->id }}', 'Delete Product?', 'Are you sure you want to delete {{ $p->name }}? This cannot be undone.')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
            </td>
        </tr>
        @empty
        <tr><td colspan="{{ $isGroupUser ? 9 : 8 }}" class="px-5 py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p class="text-gray-400 text-xs">No products found</p>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="mt-3 text-xs text-emerald-600 hover:text-emerald-700 font-medium">+ Add your first product</button>
        </td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $products->links() }}</div>
</div>

{{-- ═══ Create Product Modal ═══ --}}
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Add Product</h3>
                <p class="text-xs text-gray-400 mt-0.5">Create a new product or service</p>
            </div>
            <button onclick="document.getElementById('createModal').classList.add('hidden')" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-4" id="productForm">@csrf
            {{-- Company/Branch selector --}}
            @if($isGroupUser)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Company / Branch *</label>
                <select name="company_id" id="productCompany" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="">— Select Company —</option>
                    @foreach($companies as $c)
                    <option value="{{ $c->id }}" @selected($currentCompanyId == $c->id)>{{ $c->name }} @if($c->is_group)(Group)@endif</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400 mt-1">Product will be assigned to this company/branch</p>
            </div>
            @else
            <input type="hidden" name="company_id" value="{{ $userCompany?->id ?? $currentCompanyId }}">
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                <input name="name" required placeholder="e.g. Cement Bag 50kg" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Optional description" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                    <select name="category_id" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        <option value="">None</option>
                        @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Warehouse</label>
                    <select name="warehouse_id" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        <option value="">None</option>
                        @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Purchase Price (TZS)</label>
                    <input name="purchase_price" type="number" step="0.01" value="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sale Price (TZS)</label>
                    <input name="sale_price" type="number" step="0.01" value="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Stock Qty</label>
                    <input name="stock_quantity" type="number" value="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Reorder Level</label>
                    <input name="reorder_level" type="number" value="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Unit</label>
                    <input name="unit" value="piece" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                <select name="type" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="product">Product</option>
                    <option value="service">Service</option>
                </select>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                <label class="text-xs text-gray-600">Active</label>
            </div>
            <div class="flex gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validate company selection for group users before submit
@if($isGroupUser)
document.getElementById('productForm').addEventListener('submit', function(e) {
    const companyId = document.getElementById('productCompany').value;
    if (!companyId) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Company Required',
            text: 'Please select a company/branch for this product.',
            confirmButtonColor: '#024938'
        });
        return false;
    }
});
@endif
</script>
@endsection
