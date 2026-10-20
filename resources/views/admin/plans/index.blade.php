@extends('layouts.admin')

@section('title', 'Plans - ' . config('app.name'))
@section('page_title', 'Subscription Plans')

@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $plans->total() }} plans · manage subscription packages</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Plan</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
        @forelse($plans as $plan)
        <div class="border border-gray-200 rounded-md overflow-hidden hover:border-gray-400 transition-colors">
            <div class="px-4 py-3.5 border-b border-gray-100 flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-gray-900">{{ $plan->name }}</h3>
                        @if($plan->free_plan)<span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wide">Free</span>@endif
                    </div>
                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $plan->description ?? 'No description' }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500 flex-shrink-0"><span style="width:6px;height:6px;border-radius:99px;background:{{ $plan->status ? '#10b981' : '#d1d5db' }}"></span>{{ $plan->status ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="px-4 py-3">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-bold text-gray-900 font-mono">{{ number_format($plan->package_price_monthly) }}</span>
                    <span class="text-[11px] text-gray-400">TZS/mo</span>
                    @if($plan->package_price_yearly)
                    <span class="text-[11px] text-gray-400 ml-2">· {{ number_format($plan->package_price_yearly) }}/yr</span>
                    @endif
                </div>
                <div class="flex items-center gap-4 mt-2.5 text-[11px] text-gray-500">
                    <span>{{ $plan->number_of_users }} users</span>
                    <span>{{ number_format($plan->storage_limit) }} KB storage</span>
                    @if($plan->trial)<span class="text-amber-600">{{ $plan->trial_days }}d trial</span>@endif
                </div>
            </div>
            <div class="px-4 py-2.5 border-t border-gray-100 flex items-center gap-4">
                <a href="{{ route('admin.plans.edit', $plan) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</a>
                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete this plan?'})">
                    @csrf @method('DELETE')
                    <button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-10 text-gray-400 text-xs">No plans found</div>
        @endforelse
    </div>
    @if($plans->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $plans->links() }}</div>
    @endif
</div>

{{-- Add Plan Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Add Plan</h3><p class="text-[11px] text-slate-400 mt-0.5">Create a subscription package</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.plans.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" required placeholder="e.g. Business Pro" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Monthly (TZS) <span class="text-red-500">*</span></label><input name="package_price_monthly" type="number" step="0.01" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Yearly (TZS) <span class="text-red-500">*</span></label><input name="package_price_yearly" type="number" step="0.01" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Users <span class="text-red-500">*</span></label><input name="number_of_users" type="number" value="1" min="1" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Storage (KB)</label><input name="storage_limit" type="number" value="0" min="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Trial Days</label><input name="trial_days" type="number" value="0" min="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div></div>
                </div>
                <div class="flex items-center gap-4 flex-wrap">
                    <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="free_plan" class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Free</label>
                    <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="trial" class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Trial</label>
                    <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="status" checked class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Active</label>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Plan</button>
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
