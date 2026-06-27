@extends('layouts.admin')
@section('title', 'Edit Coupon - ' . config('app.name'))
@section('page_title', 'Edit Coupon')
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Edit Coupon</h3>
        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" value="{{ old('name', $coupon->name) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Code *</label><input name="code" value="{{ old('code', $coupon->code) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm font-mono focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Discount *</label><input name="discount" type="number" step="0.01" value="{{ old('discount', $coupon->discount) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Type *</label><select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage</option><option value="flat" {{ old('type', $coupon->type) === 'flat' ? 'selected' : '' }}>Flat</option><option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed</option></select></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Limit</label><input name="limit" type="number" value="{{ old('limit', $coupon->limit) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Expiry Date</label><input name="expiry_date" type="datetime-local" value="{{ old('expiry_date', $coupon->expiry_date ? $coupon->expiry_date->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div><label class="flex items-center gap-2"><input type="checkbox" name="status" {{ old('status', $coupon->status) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Active</span></label></div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
