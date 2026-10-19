@extends('layouts.admin')
@section('title', 'Policies - ' . config('app.name'))
@section('page_title', 'Company Policies')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $policies->total() }} policies</span>
        <button type="button" onclick="openPolDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Policy</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Title</th>
                    <th class="px-5 py-3 font-semibold">Category</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($policies as $p)
            <tr class="hover:bg-gray-50/60">
                <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $p->title }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $p->category ?? '—' }}</td>
                <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $p->is_active ? '#10b981' : '#9ca3af' }}"></span>{{ $p->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end">
                        <form method="POST" action="{{ route('admin.policies.destroy', $p) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete policy?', text:'{{ addslashes($p->title) }} will be permanently removed.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-10 text-center text-xs text-gray-400">No policies</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($policies->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $policies->links() }}</div>
    @endif
</div>

<div id="polDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closePolDrawer()"></div>
    <div id="polDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Policy</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Company policy document</p>
            </div>
            <button type="button" onclick="closePolDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.policies.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="e.g. Leave Policy" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><input name="category" placeholder="e.g. HR, IT, Safety" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Content</label><textarea name="content" rows="8" placeholder="Policy text..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div class="flex items-center gap-2"><input type="checkbox" name="is_active" checked class="w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400"><label class="text-xs text-gray-600">Active</label></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create</button>
                <button type="button" onclick="closePolDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openPolDrawer() {
    document.getElementById('polDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('polDrawerPanel').classList.remove('-translate-x-full'));
}
function closePolDrawer() {
    document.getElementById('polDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('polDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closePolDrawer(); });
</script>
@endpush
@endsection
