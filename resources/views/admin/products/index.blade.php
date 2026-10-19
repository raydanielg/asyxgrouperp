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
        <div class="flex items-center gap-2 px-3 py-1.5 rounded bg-gray-100 border border-gray-200">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>
            <span class="text-xs font-medium text-gray-700">{{ $currentCompany->name }}</span>
            @if($currentCompany->is_group)<span class="text-[9px] text-gray-400">· Group</span>@endif
        </div>
        @endif
        <p class="text-sm text-gray-500">Manage products and services</p>
    </div>
    <button onclick="openCreateDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Product</button>
</div>

{{-- ═══ Products Table ═══ --}}
<div class="bg-white rounded-md border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm" id="productsTable">
        <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
            <th class="px-5 py-3 font-semibold">Code</th>
            <th class="px-5 py-3 font-semibold">Name</th>
            <th class="px-5 py-3 font-semibold">Category</th>
            <th class="px-5 py-3 font-semibold text-right">Purchase Price</th>
            <th class="px-5 py-3 font-semibold text-right">Sale Price</th>
            <th class="px-5 py-3 font-semibold">Stock</th>
            @if($isGroupUser)<th class="px-5 py-3 font-semibold">Company</th>@endif
            <th class="px-5 py-3 font-semibold">Status</th>
            <th class="px-5 py-3 font-semibold text-right">Actions</th>
        </tr></thead>
        <tbody id="productsBody">
        @forelse($products as $p)
        <tr class="hover:bg-gray-50/60" id="row-{{ $p->id }}">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700">{{ $p->product_code }}</td>
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $p->name }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $p->category?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700 text-right">{{ number_format($p->purchase_price) }}</td>
            <td class="px-5 py-3.5 text-xs font-medium font-mono text-gray-900 text-right">{{ number_format($p->sale_price) }}</td>
            <td class="px-5 py-3.5">
                @if($p->stock_quantity <= $p->reorder_level && $p->reorder_level > 0)
                <span class="text-xs font-medium text-red-600">{{ $p->stock_quantity }} {{ $p->unit }}</span><span class="ml-1 text-[9px] text-red-500">LOW</span>
                @else
                <span class="text-xs text-gray-700">{{ $p->stock_quantity }} {{ $p->unit }}</span>
                @endif
            </td>
            @if($isGroupUser)
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $p->company?->short_code ?? '—' }}</td>
            @endif
            <td class="px-5 py-3.5">
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $p->is_active ? '#10b981' : '#9ca3af' }}"></span>{{ $p->is_active ? 'Active' : 'Inactive' }}</span>
            </td>
            <td class="px-5 py-3.5 text-right">
                <button onclick="deleteProduct({{ $p->id }}, '{{ addslashes($p->name) }}')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
            </td>
        </tr>
        @empty
        <tr id="emptyRow"><td colspan="{{ $isGroupUser ? 9 : 8 }}" class="px-5 py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p class="text-gray-400 text-xs">No products found</p>
            <button onclick="openCreateDrawer()" class="mt-3 text-xs text-gray-600 hover:text-gray-900 font-medium">+ Add your first product</button>
        </td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $products->links() }}</div>
</div>

{{-- ═══ Create Product Drawer ═══ --}}
<div id="createModal" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeCreateModal()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">Add Product</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Create a new product or service</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="productForm" class="flex-1 overflow-y-auto p-5 space-y-4">
            @csrf
            {{-- Company/Branch selector --}}
            @if($isGroupUser)
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Company / Branch *</label>
                <select name="company_id" id="productCompany" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none">
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
                <input name="name" id="pName" required placeholder="e.g. Cement Bag 50kg" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <textarea name="description" id="pDesc" rows="2" placeholder="Optional description" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                    <select name="category_id" id="pCategory" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none">
                        <option value="">None</option>
                        @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Warehouse</label>
                    <select name="warehouse_id" id="pWarehouse" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none">
                        <option value="">None</option>
                        @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Purchase Price (TZS)</label>
                    <input name="purchase_price" id="pPurchase" type="number" step="0.01" value="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sale Price (TZS)</label>
                    <input name="sale_price" id="pSale" type="number" step="0.01" value="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Stock Qty</label>
                    <input name="stock_quantity" id="pStock" type="number" value="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Reorder Level</label>
                    <input name="reorder_level" id="pReorder" type="number" value="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Unit</label>
                    <input name="unit" id="pUnit" value="piece" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                <select name="type" id="pType" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none">
                    <option value="product">Product</option>
                    <option value="service">Service</option>
                </select>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="pActive" checked class="w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400">
                <label class="text-xs text-gray-600">Active</label>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" id="submitBtn" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors flex items-center justify-center gap-2">
                    <span id="submitText">Create Product</span>
                </button>
                <button type="button" onclick="closeCreateModal()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ AJAX Scripts ═══ --}}
<script>
const storeUrl = '{{ route("admin.products.store") }}';
const isGroupUser = {{ $isGroupUser ? 'true' : 'false' }};
const showCompanyCol = {{ $isGroupUser ? 'true' : 'false' }};

function openCreateDrawer() {
    document.getElementById('createModal').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('createDrawerPanel').classList.remove('-translate-x-full'));
    setTimeout(() => document.getElementById('pName').focus(), 300);
}

function closeCreateModal() {
    document.getElementById('createDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => {
        document.getElementById('createModal').classList.add('hidden');
        document.getElementById('productForm').reset();
    }, 300);
}

// Submit via AJAX
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Validate company for group users
    if (isGroupUser) {
        const companyId = document.getElementById('productCompany').value;
        if (!companyId) {
            Swal.fire({ icon: 'warning', title: 'Company Required', text: 'Please select a company/branch for this product.', confirmButtonColor: '#024938' });
            return;
        }
    }

    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('submitText');
    btn.disabled = true;
    btnText.textContent = 'Saving...';
    btn.classList.add('opacity-60');

    const formData = new FormData(this);

    fetch(storeUrl, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async res => {
        if (!res.ok) {
            let errMsg = 'Server returned status ' + res.status + '. Please refresh the page and try again.';
            try {
                const errData = await res.json();
                if (errData.errors) {
                    errMsg = Object.values(errData.errors).flat().join('\n');
                } else if (errData.message) {
                    errMsg = errData.message;
                }
            } catch (e) {}
            throw new Error(errMsg);
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            closeCreateModal();
            btn.disabled = false;
            btnText.textContent = 'Create Product';
            btn.classList.remove('opacity-60');

            // Show success toast
            if (window.showToast) {
                showToast('success', 'Product Created', data.message);
            }

            // Add row to table or reload
            addProductRow(data.product);

            Swal.fire({
                icon: 'success',
                title: 'Product Created!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            btn.disabled = false;
            btnText.textContent = 'Create Product';
            btn.classList.remove('opacity-60');
            Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to create product', confirmButtonColor: '#024938' });
        }
    })
    .catch(err => {
        btn.disabled = false;
        btnText.textContent = 'Create Product';
        btn.classList.remove('opacity-60');
        Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
});

function addProductRow(p) {
    const tbody = document.getElementById('productsBody');
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    const tr = document.createElement('tr');
    tr.className = 'border-t border-gray-100 hover:bg-gray-50/50';
    tr.id = 'row-' + p.id;

    let stockHtml = '';
    if (p.stock_quantity <= p.reorder_level && p.reorder_level > 0) {
        stockHtml = '<span class="text-xs font-bold text-red-600">' + p.stock_quantity + ' ' + (p.unit || '') + '</span><span class="ml-1 text-[9px] text-red-500">LOW</span>';
    } else {
        stockHtml = '<span class="text-xs text-gray-700">' + p.stock_quantity + ' ' + (p.unit || '') + '</span>';
    }

    let companyHtml = '';
    if (showCompanyCol) {
        companyHtml = '<td class="px-5 py-3.5 text-xs text-gray-500">' + (p.company ? (p.company.short_code || '—') : '—') + '</td>';
    }

    const dot = p.is_active ? '#10b981' : '#9ca3af';
    const statusHtml = '<span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:' + dot + '"></span>' + (p.is_active ? 'Active' : 'Inactive') + '</span>';

    const catName = p.category ? p.category.name : 'N/A';

    tr.innerHTML =
        '<td class="px-5 py-3.5 text-xs font-mono text-gray-700">' + p.product_code + '</td>' +
        '<td class="px-5 py-3.5 text-xs font-medium text-gray-900">' + p.name + '</td>' +
        '<td class="px-5 py-3.5 text-xs text-gray-500">' + catName + '</td>' +
        '<td class="px-5 py-3.5 text-xs font-mono text-gray-700 text-right">' + Number(p.purchase_price || 0).toLocaleString() + '</td>' +
        '<td class="px-5 py-3.5 text-xs font-medium font-mono text-gray-900 text-right">' + Number(p.sale_price || 0).toLocaleString() + '</td>' +
        '<td class="px-5 py-3.5">' + stockHtml + '</td>' +
        companyHtml +
        '<td class="px-5 py-3.5">' + statusHtml + '</td>' +
        '<td class="px-5 py-3.5 text-right"><button onclick="deleteProduct(' + p.id + ', \'' + (p.name || '').replace(/'/g, "\\'") + '\')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></td>';

    tbody.insertBefore(tr, tbody.firstChild);
}

function deleteProduct(id, name) {
    Swal.fire({
        title: 'Delete Product?',
        text: 'Are you sure you want to delete "' + name + '"? This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const delUrl = '{{ route("admin.products.destroy", ":id") }}'.replace(':id', id);
            fetch(delUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const row = document.getElementById('row-' + id);
                    if (row) {
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    }
                    if (window.showToast) showToast('success', 'Deleted', data.message);
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to delete', confirmButtonColor: '#024938' });
                }
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
            });
        }
    });
}

// Close modal on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeCreateModal();
});
</script>
@endsection
