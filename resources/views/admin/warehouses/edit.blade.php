@extends('layouts.admin')
@section('title', 'Edit Warehouse - ' . config('app.name'))
@section('page_title', 'Edit Warehouse')
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Edit Warehouse</h3>
        <form method="POST" action="{{ route('admin.warehouses.update', $warehouse) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" value="{{ old('name', $warehouse->name) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Address *</label><textarea name="address" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">{{ old('address', $warehouse->address) }}</textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">City *</label><input name="city" value="{{ old('city', $warehouse->city) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Zip Code *</label><input name="zip_code" value="{{ old('zip_code', $warehouse->zip_code) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" value="{{ old('phone', $warehouse->phone) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" type="email" value="{{ old('email', $warehouse->email) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div><label class="flex items-center gap-2"><input type="checkbox" name="is_active" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Active</span></label></div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.warehouses.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
