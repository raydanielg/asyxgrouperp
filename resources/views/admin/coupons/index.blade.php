@extends('layouts.admin')

@section('title', 'Coupons - ' . config('app.name'))
@section('page_title', 'Coupons')

@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $coupons->total() }} coupons · manage discount codes</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Coupon</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Code</th><th class="px-5 py-3 font-semibold">Name</th><th class="px-5 py-3 font-semibold text-right">Discount</th><th class="px-5 py-3 font-semibold">Type</th><th class="px-5 py-3 font-semibold">Limit</th><th class="px-5 py-3 font-semibold">Expiry</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($coupons as $coupon)
        @php $expired = $coupon->expiry_date && $coupon->expiry_date->isPast(); @endphp
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><span class="text-xs font-mono font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $coupon->code }}</span></td>
            <td class="px-5 py-3.5 text-xs text-gray-800">{{ $coupon->name }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold text-gray-900">{{ $coupon->type === 'percentage' ? $coupon->discount . '%' : 'TZS ' . number_format($coupon->discount) }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500 capitalize">{{ $coupon->type }}</td>
            <td class="px-5 py-3.5 text-xs {{ $coupon->limit ? 'text-gray-500' : 'text-gray-300' }}">{{ $coupon->limit ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs {{ $expired ? 'text-red-500 font-medium' : 'text-gray-400' }}">{{ $coupon->expiry_date ? $coupon->expiry_date->format('d M Y') : '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $coupon->status && !$expired ? '#10b981' : '#d1d5db' }}"></span>{{ $expired ? 'Expired' : ($coupon->status ? 'Active' : 'Inactive') }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</a>
                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete this coupon?'})">
                        @csrf @method('DELETE')
                        <button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No coupons found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($coupons->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $coupons->links() }}</div>
    @endif
</div>

{{-- Add Coupon Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Add Coupon</h3><p class="text-[11px] text-slate-400 mt-0.5">Create a discount code</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.coupons.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" required placeholder="e.g. Launch Promo" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Code <span class="text-red-500">*</span></label><input name="code" required placeholder="PROMO2026" class="w-full px-3 py-2 text-sm font-mono border border-gray-200 rounded outline-none focus:border-gray-400 uppercase"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Discount <span class="text-red-500">*</span></label><input name="discount" type="number" step="0.01" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label><select name="type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="percentage">Percentage</option><option value="flat">Flat</option><option value="fixed">Fixed</option></select></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Usage Limit</label><input name="limit" type="number" min="0" placeholder="Unlimited" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Expiry Date</label><input name="expiry_date" type="datetime-local" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="status" checked class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Active</label>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Coupon</button>
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
