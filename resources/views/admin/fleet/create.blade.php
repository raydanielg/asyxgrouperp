@extends('layouts.admin')
@section('title', 'Add Vehicle - ' . config('app.name'))
@section('page_title', 'Register Vehicle')
@section('content')
<div class="mb-4"><a href="{{ route('admin.fleet.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Fleet</a></div>
<div class="bg-white rounded-xl border p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.fleet.store') }}">@csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Vehicle Number <span class="text-red-500">*</span></label><input type="text" name="vehicle_number" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Registration Number <span class="text-red-500">*</span></label><input type="text" name="registration_number" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Make <span class="text-red-500">*</span></label><input type="text" name="make" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Model <span class="text-red-500">*</span></label><input type="text" name="model" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Year</label><input type="number" name="year" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Color</label><input type="text" name="color" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Vehicle Type</label><select name="vehicle_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"><option value="car">Car</option><option value="truck">Truck</option><option value="van">Van</option><option value="motorcycle">Motorcycle</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Fuel Type</label><select name="fuel_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"><option value="diesel">Diesel</option><option value="petrol">Petrol</option><option value="electric">Electric</option><option value="hybrid">Hybrid</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Fuel Capacity (L)</label><input type="number" name="fuel_capacity" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Odometer (km)</label><input type="number" name="odometer_reading" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Purchase Date</label><input type="date" name="purchase_date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Purchase Price</label><input type="number" name="purchase_price" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Insurance Expiry</label><input type="date" name="insurance_expiry" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Registration Expiry</label><input type="date" name="registration_expiry" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"><option value="active">Active</option><option value="maintenance">Maintenance</option><option value="retired">Retired</option></select></div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Register Vehicle</button>
            <a href="{{ route('admin.fleet.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
