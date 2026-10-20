@extends('layouts.admin')
@section('title', 'Edit Warehouse - ' . config('app.name'))
@section('page_title', 'Edit Warehouse')
@section('content')
<div class="mb-4"><a href="{{ route('admin.warehouses.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Warehouses</a></div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden max-w-2xl">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-base font-bold text-gray-900">Edit Warehouse</h3>
        <p class="text-[11px] text-gray-400 mt-0.5">{{ $warehouse->name }} · {{ $warehouse->city }}</p>
    </div>
    <form method="POST" action="{{ route('admin.warehouses.update', $warehouse) }}">
        @csrf @method('PATCH')
        <div class="px-5 py-4 space-y-4">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" value="{{ old('name', $warehouse->name) }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Address <span class="text-red-500">*</span></label><textarea name="address" required rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ old('address', $warehouse->address) }}</textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">City <span class="text-red-500">*</span></label><input name="city" value="{{ old('city', $warehouse->city) }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Zip Code <span class="text-red-500">*</span></label><input name="zip_code" value="{{ old('zip_code', $warehouse->zip_code) }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" value="{{ old('phone', $warehouse->phone) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" type="email" value="{{ old('email', $warehouse->email) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="is_active" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Active</label>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700">Update Warehouse</button>
            <a href="{{ route('admin.warehouses.index') }}" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</a>
        </div>
    </form>
</div>
@endsection
