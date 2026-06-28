@extends('layouts.admin')
@section('title', 'Register Asset - ' . config('app.name'))
@section('page_title', 'Register Fixed Asset')
@section('content')
<div class="mb-4"><a href="{{ route('admin.fixed-assets.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Assets</a></div>
<div class="bg-white rounded-xl border p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.fixed-assets.store') }}">@csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Asset Number <span class="text-red-500">*</span></label><input type="text" name="asset_number" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Asset Tag</label><input type="text" name="asset_tag" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input type="text" name="name" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><select name="category" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"><option value="it_equipment">IT Equipment</option><option value="furniture">Furniture</option><option value="vehicle">Vehicle</option><option value="machinery">Machinery</option><option value="building">Building</option><option value="other">Other</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Location</label><input type="text" name="location" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Acquisition Date <span class="text-red-500">*</span></label><input type="date" name="acquisition_date" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Acquisition Cost <span class="text-red-500">*</span></label><input type="number" name="acquisition_cost" required step="0.01" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Salvage Value</label><input type="number" name="salvage_value" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Useful Life (Years) <span class="text-red-500">*</span></label><input type="number" name="useful_life_years" required min="1" value="5" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Depreciation Method</label><select name="depreciation_method" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"><option value="straight_line">Straight Line</option><option value="declining_balance">Declining Balance</option></select></div>
            <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></textarea></div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Register Asset</button>
            <a href="{{ route('admin.fixed-assets.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
