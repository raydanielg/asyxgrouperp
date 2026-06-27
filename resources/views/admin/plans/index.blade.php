@extends('layouts.admin')

@section('title', 'Plans - ' . config('app.name'))
@section('page_title', 'Subscription Plans')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage subscription plans</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Plan
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
    @forelse($plans as $plan)
    <div class="bg-white rounded-xl border p-5 hover:shadow-lg transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="text-sm font-bold text-gray-900">{{ $plan->name }}</h3>
                @if($plan->free_plan)<span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Free</span>@endif
                @if($plan->trial)<span class="inline-flex items-center mt-1 ml-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Trial: {{ $plan->trial_days }}d</span>@endif
            </div>
            @if($plan->status)<span class="w-2 h-2 bg-emerald-500 rounded-full mt-2"></span>@else<span class="w-2 h-2 bg-gray-300 rounded-full mt-2"></span>@endif
        </div>
        <p class="text-xs text-gray-400 mb-3">{{ $plan->description ?? 'No description' }}</p>
        <div class="flex items-baseline gap-1 mb-3">
            <span class="text-2xl font-bold text-gray-900">${{ number_format($plan->package_price_monthly, 2) }}</span>
            <span class="text-xs text-gray-400">/mo</span>
            <span class="text-xs text-gray-400 ml-2">${{ number_format($plan->package_price_yearly, 2) }}/yr</span>
        </div>
        <div class="text-xs text-gray-500 space-y-1 mb-3">
            <p>Users: {{ $plan->number_of_users }}</p>
            <p>Storage: {{ number_format($plan->storage_limit) }} KB</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="inline" onsubmit="return confirm('Delete this plan?')">
                @csrf @method('DELETE')
                <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-8 text-gray-400 text-sm">No plans found</div>
    @endforelse
</div>
<div class="px-1">{{ $plans->links() }}</div>

<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Plan</h3>
        <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-3">
            @csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Monthly $ *</label><input name="package_price_monthly" type="number" step="0.01" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Yearly $ *</label><input name="package_price_yearly" type="number" step="0.01" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Users *</label><input name="number_of_users" type="number" value="1" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Storage (KB)</label><input name="storage_limit" type="number" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2"><input type="checkbox" name="free_plan" class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Free Plan</span></label>
                <label class="flex items-center gap-2"><input type="checkbox" name="trial" class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Trial</span></label>
                <label class="flex items-center gap-2"><input type="checkbox" name="status" checked class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Active</span></label>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
