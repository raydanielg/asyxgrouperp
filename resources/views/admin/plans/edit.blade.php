@extends('layouts.admin')
@section('title', 'Edit Plan - ' . config('app.name'))
@section('page_title', 'Edit Plan')
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Edit Plan</h3>
        <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" value="{{ old('name', $plan->name) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">{{ old('description', $plan->description) }}</textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Number of Users *</label><input name="number_of_users" type="number" value="{{ old('number_of_users', $plan->number_of_users) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Storage Limit (MB)</label><input name="storage_limit" type="number" value="{{ old('storage_limit', $plan->storage_limit) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Monthly Price *</label><input name="package_price_monthly" type="number" step="0.01" value="{{ old('package_price_monthly', $plan->package_price_monthly) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Yearly Price *</label><input name="package_price_yearly" type="number" step="0.01" value="{{ old('package_price_yearly', $plan->package_price_yearly) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Trial Days</label><input name="trial_days" type="number" value="{{ old('trial_days', $plan->trial_days) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="space-y-2">
                <label class="flex items-center gap-2"><input type="checkbox" name="free_plan" {{ old('free_plan', $plan->free_plan) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Free Plan</span></label>
                <label class="flex items-center gap-2"><input type="checkbox" name="trial" {{ old('trial', $plan->trial) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Trial Available</span></label>
                <label class="flex items-center gap-2"><input type="checkbox" name="status" {{ old('status', $plan->status) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Active</span></label>
            </div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
