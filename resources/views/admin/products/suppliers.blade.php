@extends('layouts.admin')
@section('title', 'Suppliers - ' . config('app.name'))
@section('page_title', 'Suppliers')
@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

@php $isAdmin = in_array(auth()->user()->role, ['admin', 'superadmin']); @endphp

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
        <form method="GET" class="flex items-center gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, contact, email..." class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 w-52">
            <select name="status" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Filter</button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.suppliers.index') }}" class="text-xs text-gray-400 hover:text-gray-600">Clear</a>
            @endif
        </form>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Supplier</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Supplier</th><th class="px-5 py-3 font-semibold">Contact Person</th><th class="px-5 py-3 font-semibold">Email</th><th class="px-5 py-3 font-semibold">Phone</th><th class="px-5 py-3 font-semibold">Payment Terms</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($suppliers as $s)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5">
                <p class="text-xs font-medium text-gray-900">{{ $s->name }}</p>
                @if($s->address)<p class="text-[10px] text-gray-400 mt-0.5 max-w-[220px] truncate">{{ $s->address }}</p>@endif
            </td>
            <td class="px-5 py-3.5 text-xs {{ $s->contact_person ? 'text-gray-500' : 'text-gray-300' }}">{{ $s->contact_person ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs {{ $s->email ? 'text-gray-700' : 'text-gray-300' }}">{{ $s->email ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs {{ $s->phone ? 'text-gray-500' : 'text-gray-300' }}">{{ $s->phone ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs {{ $s->payment_terms ? 'text-gray-500' : 'text-gray-300' }}">{{ $s->payment_terms ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $s->is_active ? '#10b981' : '#d1d5db' }}"></span>{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    @if($isAdmin)
                    <button type="button" onclick='openEditDrawer({{ $s->id }}, @json($s->name), @json($s->contact_person), @json($s->email), @json($s->phone), @json($s->address), @json($s->payment_terms), {{ $s->is_active ? 1 : 0 }})' class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</button>
                    @endif
                    <form id="del-sup-{{ $s->id }}" method="POST" action="{{ route('admin.suppliers.destroy', $s) }}" class="inline">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-sup-{{ $s->id }}')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No suppliers found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($suppliers->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $suppliers->links() }}</div>
    @endif
</div>

{{-- Add Supplier Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Add Supplier</h3><p class="text-[11px] text-slate-400 mt-0.5">Register a new vendor</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.suppliers.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" required placeholder="Supplier name" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Contact Person</label><input name="contact_person" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" type="email" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Address</label><textarea name="address" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Terms</label><input name="payment_terms" placeholder="e.g. Net 30" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="is_active" checked class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Active</label>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Supplier</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Supplier Drawer (admin/superadmin only) --}}
@if($isAdmin)
<div id="editDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeEditDrawer()"></div>
    <div id="editDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Edit Supplier</h3><p class="text-[11px] text-slate-400 mt-0.5" id="editSupplierName"></p></div>
            <button type="button" onclick="closeEditDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" id="editSupplierForm" action="" class="flex-1 overflow-y-auto p-5">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" id="edit_name" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Contact Person</label><input name="contact_person" id="edit_contact_person" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" id="edit_phone" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" id="edit_email" type="email" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Address</label><textarea name="address" id="edit_address" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Terms</label><input name="payment_terms" id="edit_payment_terms" placeholder="e.g. Net 30" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="is_active" id="edit_is_active" class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Active</label>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Update Supplier</button>
                <button type="button" onclick="closeEditDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endif
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
@if($isAdmin)
function openEditDrawer(id, name, contactPerson, email, phone, address, paymentTerms, isActive) {
    const d = document.getElementById('editDrawer');
    const form = document.getElementById('editSupplierForm');
    form.action = `{{ url('admin/suppliers') }}/${id}`;
    document.getElementById('editSupplierName').textContent = name;
    document.getElementById('edit_name').value = name || '';
    document.getElementById('edit_contact_person').value = contactPerson || '';
    document.getElementById('edit_email').value = email || '';
    document.getElementById('edit_phone').value = phone || '';
    document.getElementById('edit_address').value = address || '';
    document.getElementById('edit_payment_terms').value = paymentTerms || '';
    document.getElementById('edit_is_active').checked = !!isActive;
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('editDrawerPanel').classList.remove('-translate-x-full'));
}
function closeEditDrawer() {
    const p = document.getElementById('editDrawerPanel');
    if (!p) return;
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('editDrawer').classList.add('hidden'), 300);
}
@endif
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeDrawer(); @if($isAdmin) closeEditDrawer(); @endif } });
</script>
@endpush
