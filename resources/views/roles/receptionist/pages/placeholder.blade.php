@php
$title = $title ?? ucwords(str_replace('-', ' ', $module ?? 'Module'));
$description = $description ?? 'Manage ' . strtolower($title) . ' from the reception desk.';
@endphp
@extends('layouts.admin')
@section('title', $title)
@section('page_title', $title)
@section('content')
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10">
        <h2 class="text-2xl font-bold">{{ $title }}</h2>
        <p class="text-emerald-100 text-sm mt-1">{{ $description }}</p>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Today</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">This Week</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $weekCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Pending</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $pendingCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Total</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalCount ?? 0 }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-gray-900">{{ $title }} List</h3>
        <button class="px-4 py-2 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors">Add New</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Details</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-400">
                        No records yet. Use the <strong>Add New</strong> button to create the first record.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-amber-800 text-xs">
    <strong>Coming soon:</strong> Full CRUD and backend for {{ strtolower($title) }} will be wired here. Admin has full access to all reception modules.
</div>
@endsection
