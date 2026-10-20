@extends('layouts.admin')
@section('title', 'Fleet Management - ' . config('app.name'))
@section('page_title', 'Fleet Management')
@section('content')
@php
    $active = $vehicles->where('status', 'active')->count();
    $maint = $vehicles->where('status', 'maintenance')->count();
@endphp

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Company vehicles</span>
        <a href="{{ route('admin.fleet.create') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Vehicle</a>
    </div>
    <div class="grid grid-cols-3 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ $vehicles->total() }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Active</p><p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $active }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">In Maintenance</p><p class="text-lg font-bold text-amber-600 mt-0.5">{{ $maint }}</p></div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Vehicle #</th><th class="px-5 py-3 font-semibold">Reg. Number</th><th class="px-5 py-3 font-semibold">Make / Model</th><th class="px-5 py-3 font-semibold">Type</th><th class="px-5 py-3 font-semibold text-right">Odometer</th><th class="px-5 py-3 font-semibold">Assigned To</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($vehicles as $vehicle)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.fleet.show', $vehicle) }}" class="text-xs font-mono font-medium text-gray-900 hover:text-gray-600">{{ $vehicle->vehicle_number }}</a></td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $vehicle->registration_number }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-600">{{ $vehicle->make }} {{ $vehicle->model }}{{ $vehicle->year ? ' ('.$vehicle->year.')' : '' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500 capitalize">{{ $vehicle->vehicle_type ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-700">{{ number_format($vehicle->odometer_reading, 0) }} km</td>
            <td class="px-5 py-3.5 text-xs {{ $vehicle->assignedTo ? 'text-gray-600' : 'text-gray-300' }}">{{ $vehicle->assignedTo?->name ?? 'Unassigned' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $vehicle->status === 'active' ? '#10b981' : ($vehicle->status === 'maintenance' ? '#f59e0b' : '#9ca3af') }}"></span>{{ $vehicle->status }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.fleet.show', $vehicle) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <a href="{{ route('admin.fleet.edit', $vehicle) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</a>
                    <form method="POST" action="{{ route('admin.fleet.destroy', $vehicle) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete this vehicle?'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No vehicles registered</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($vehicles->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $vehicles->links() }}</div>
    @endif
</div>
@endsection
