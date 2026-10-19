@extends('layouts.admin')
@section('title', 'Fleet Management - ' . config('app.name'))
@section('page_title', 'Fleet Management')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage company vehicles</p>
    <a href="{{ route('admin.fleet.create') }}" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Vehicle
    </a>
</div>
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
<div class="bg-white rounded-md border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Vehicle #</th><th class="px-5 py-3 font-medium">Reg. Number</th><th class="px-5 py-3 font-medium">Make / Model</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Odometer</th><th class="px-5 py-3 font-medium">Assigned To</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($vehicles as $vehicle)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs"><a href="{{ route('admin.fleet.show', $vehicle) }}" class="font-medium text-gray-800 hover:text-gray-700">{{ $vehicle->vehicle_number }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $vehicle->registration_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year }})</td>
            <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ $vehicle->vehicle_type ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ number_format($vehicle->odometer_reading, 0) }} km</td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $vehicle->assignedTo?->name ?? 'Unassigned' }}</td>
            <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $vehicle->status === 'active' ? '#10b981' : ($vehicle->status === 'maintenance' ? '#f59e0b' : '#9ca3af') }}"></span>{{ $vehicle->status }}</span></td>
            <td class="px-5 py-3 flex items-center gap-3">
                <a href="{{ route('admin.fleet.show', $vehicle) }}" class="text-gray-600 hover:text-gray-900 text-xs">View</a>
                <a href="{{ route('admin.fleet.edit', $vehicle) }}" class="text-gray-600 hover:text-gray-900 text-xs">Edit</a>
                <form method="POST" action="{{ route('admin.fleet.destroy', $vehicle) }}" onsubmit="return confirmAction(this, {title: 'Delete this vehicle?'})">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs">Delete</button></form>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No vehicles registered</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $vehicles->links() }}</div>
</div>
@endsection
