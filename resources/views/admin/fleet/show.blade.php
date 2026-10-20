@extends('layouts.admin')
@section('title', $vehicle->vehicle_number . ' - ' . config('app.name'))
@section('page_title', $vehicle->vehicle_number)
@section('content')
<div class="mb-4"><a href="{{ route('admin.fleet.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Fleet</a></div>
@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-md border p-6">
        <h3 class="font-bold text-gray-800 mb-4">{{ $vehicle->make }} {{ $vehicle->model }}</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-400">Vehicle #</dt><dd class="text-gray-700">{{ $vehicle->vehicle_number }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Reg. Number</dt><dd class="text-gray-700">{{ $vehicle->registration_number }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Year</dt><dd class="text-gray-700">{{ $vehicle->year ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Color</dt><dd class="text-gray-700">{{ $vehicle->color ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Type</dt><dd class="text-gray-700 capitalize">{{ $vehicle->vehicle_type ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Fuel</dt><dd class="text-gray-700">{{ $vehicle->fuel_type }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Odometer</dt><dd class="text-gray-700">{{ number_format($vehicle->odometer_reading, 0) }} km</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Assigned To</dt><dd class="text-gray-700">{{ $vehicle->assignedTo?->name ?? 'Unassigned' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Insurance</dt><dd class="text-gray-700">{{ $vehicle->insurance_expiry?->format('d M Y') ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Reg. Expiry</dt><dd class="text-gray-700">{{ $vehicle->registration_expiry?->format('d M Y') ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Status</dt><dd><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $vehicle->status === 'active' ? '#10b981' : ($vehicle->status === 'maintenance' ? '#f59e0b' : '#9ca3af') }}"></span>{{ $vehicle->status }}</span></dd></div>
        </dl>
    </div>
    <div class="lg:col-span-2 space-y-6">
        {{-- Maintenance --}}
        <div class="bg-white rounded-md border overflow-hidden">
            <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between"><h4 class="text-sm font-bold text-gray-700">Maintenance Records</h4></div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Date</th><th class="px-4 py-2 font-medium">Type</th><th class="px-4 py-2 font-medium">Provider</th><th class="px-4 py-2 font-medium text-right">Cost</th></tr></thead>
                <tbody>
                @forelse($maintenance as $m)
                <tr class="border-t border-gray-100"><td class="px-4 py-2 text-xs">{{ $m->service_date->format('d M Y') }}</td><td class="px-4 py-2 text-xs capitalize">{{ $m->maintenance_type }}</td><td class="px-4 py-2 text-xs text-gray-600">{{ $m->service_provider ?? '—' }}</td><td class="px-4 py-2 text-xs text-right font-medium">{{ number_format($m->cost, 0) }}</td></tr>
                @empty
                <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400 text-xs">No maintenance records</td></tr>
                @endforelse
                </tbody>
            </table></div>
            <div class="px-5 py-3 border-t">{{ $maintenance->links() }}</div>
        </div>
        {{-- Fuel Logs --}}
        <div class="bg-white rounded-md border overflow-hidden">
            <div class="px-5 py-3 border-b bg-gray-50/50"><h4 class="text-sm font-bold text-gray-700">Fuel Logs</h4></div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Date</th><th class="px-4 py-2 font-medium text-right">Litres</th><th class="px-4 py-2 font-medium text-right">Cost/L</th><th class="px-4 py-2 font-medium text-right">Total</th></tr></thead>
                <tbody>
                @forelse($fuelLogs as $f)
                <tr class="border-t border-gray-100"><td class="px-4 py-2 text-xs">{{ $f->fuel_date->format('d M Y') }}</td><td class="px-4 py-2 text-xs text-right">{{ number_format($f->litres, 2) }}</td><td class="px-4 py-2 text-xs text-right">{{ number_format($f->cost_per_litre, 0) }}</td><td class="px-4 py-2 text-xs text-right font-medium">{{ number_format($f->total_cost, 0) }}</td></tr>
                @empty
                <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400 text-xs">No fuel logs</td></tr>
                @endforelse
                </tbody>
            </table></div>
            <div class="px-5 py-3 border-t">{{ $fuelLogs->links() }}</div>
        </div>
    </div>
</div>
@endsection
