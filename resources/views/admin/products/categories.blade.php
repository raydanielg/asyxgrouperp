@extends('layouts.admin')
@section('title', 'Product Categories - ' . config('app.name'))
@section('page_title', 'Product Categories')
@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $categories->total() }} categories · organize products into groups</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Category</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Name</th><th class="px-5 py-3 font-semibold">Slug</th><th class="px-5 py-3 font-semibold text-right">Products</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($categories as $c)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5">
                <p class="text-xs font-medium text-gray-900">{{ $c->name }}</p>
                @if($c->description)<p class="text-[10px] text-gray-400 mt-0.5 max-w-[260px] truncate">{{ $c->description }}</p>@endif
            </td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-400">{{ $c->slug }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-700">{{ $c->products->count() }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $c->is_active ? '#10b981' : '#d1d5db' }}"></span>{{ $c->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="px-5 py-3.5 text-right">
                <form id="del-cat-{{ $c->id }}" method="POST" action="{{ route('admin.product-categories.destroy', $c) }}" class="inline">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-cat-{{ $c->id }}')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-xs">No categories found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($categories->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $categories->links() }}</div>
    @endif
</div>

{{-- Add Category Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Add Category</h3><p class="text-[11px] text-slate-400 mt-0.5">Organize products into groups</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.product-categories.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                    <input name="name" required placeholder="e.g. Networking Equipment" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Optional description..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                    <input type="checkbox" name="is_active" checked class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">
                    Active
                </label>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Category</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
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
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
</script>
@endpush
