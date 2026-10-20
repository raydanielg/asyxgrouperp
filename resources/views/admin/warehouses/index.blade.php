@extends('layouts.admin')

@section('title', 'Warehouses - ' . config('app.name'))
@section('page_title', 'Warehouses')

@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $warehouses->total() }} warehouse{{ $warehouses->total() === 1 ? '' : 's' }} · manage storage locations</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Warehouse</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Name</th><th class="px-5 py-3 font-semibold">Location</th><th class="px-5 py-3 font-semibold">Phone</th><th class="px-5 py-3 font-semibold">Email</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($warehouses as $warehouse)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $warehouse->name }}</td>
            <td class="px-5 py-3.5">
                <p class="text-xs text-gray-500">{{ $warehouse->city }}{{ $warehouse->zip_code ? ', ' . $warehouse->zip_code : '' }}</p>
                @if($warehouse->address)<p class="text-[10px] text-gray-400 mt-0.5 max-w-[220px] truncate">{{ $warehouse->address }}</p>@endif
            </td>
            <td class="px-5 py-3.5 text-xs {{ $warehouse->phone ? 'text-gray-500' : 'text-gray-300' }}">{{ $warehouse->phone ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs {{ $warehouse->email ? 'text-gray-500' : 'text-gray-300' }}">{{ $warehouse->email ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $warehouse->is_active ? '#10b981' : '#d1d5db' }}"></span>{{ $warehouse->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <button type="button" onclick='openEditDrawer({{ $warehouse->id }}, @json($warehouse->name), @json($warehouse->address), @json($warehouse->city), @json($warehouse->zip_code), @json($warehouse->phone), @json($warehouse->email), {{ $warehouse->is_active ? 1 : 0 }})' class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</button>
                    <form method="POST" action="{{ route('admin.warehouses.destroy', $warehouse) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete this warehouse?'})">
                        @csrf @method('DELETE')
                        <button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-xs">No warehouses found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($warehouses->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $warehouses->links() }}</div>
    @endif
</div>

{{-- Add Warehouse Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Add Warehouse</h3><p class="text-[11px] text-slate-400 mt-0.5">Register a storage location</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.warehouses.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" required placeholder="e.g. Main Warehouse" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Address <span class="text-red-500">*</span></label><textarea name="address" required rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">City <span class="text-red-500">*</span></label><input name="city" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Zip Code <span class="text-red-500">*</span></label><input name="zip_code" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" type="email" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="is_active" checked class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Active</label>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Warehouse</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Warehouse Drawer --}}
<div id="editDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeEditDrawer()"></div>
    <div id="editDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Edit Warehouse</h3><p class="text-[11px] text-slate-400 mt-0.5" id="editWarehouseName"></p></div>
            <button type="button" onclick="closeEditDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" id="editWarehouseForm" action="" class="flex-1 overflow-y-auto p-5">
            @csrf @method('PATCH')
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" id="edit_name" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Address <span class="text-red-500">*</span></label><textarea name="address" id="edit_address" required rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">City <span class="text-red-500">*</span></label><input name="city" id="edit_city" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Zip Code <span class="text-red-500">*</span></label><input name="zip_code" id="edit_zip_code" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" id="edit_phone" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" id="edit_email" type="email" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="is_active" id="edit_is_active" class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Active</label>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Update Warehouse</button>
                <button type="button" onclick="closeEditDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer() {
    document.getElementById('createDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('createDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDrawer() {
    document.getElementById('createDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('createDrawer').classList.add('hidden'), 300);
}
function openEditDrawer(id, name, address, city, zip, phone, email, isActive) {
    const form = document.getElementById('editWarehouseForm');
    form.action = `{{ url('admin/warehouses') }}/${id}`;
    document.getElementById('editWarehouseName').textContent = name;
    document.getElementById('edit_name').value = name || '';
    document.getElementById('edit_address').value = address || '';
    document.getElementById('edit_city').value = city || '';
    document.getElementById('edit_zip_code').value = zip || '';
    document.getElementById('edit_phone').value = phone || '';
    document.getElementById('edit_email').value = email || '';
    document.getElementById('edit_is_active').checked = !!isActive;
    document.getElementById('editDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('editDrawerPanel').classList.remove('-translate-x-full'));
}
function closeEditDrawer() {
    const p = document.getElementById('editDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('editDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeDrawer(); closeEditDrawer(); } });
</script>
@endpush
