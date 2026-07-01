@extends('layouts.admin')
@section('title', 'Call Center - ' . config('app.name'))
@section('page_title', 'Call Center Dashboard')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $stats['total_calls'] }}</p><p class="text-xs text-gray-500 mt-1">Total Calls</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-sky-600">{{ $stats['inbound'] }}</p><p class="text-xs text-gray-500 mt-1">Inbound</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $stats['outbound'] }}</p><p class="text-xs text-gray-500 mt-1">Outbound</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-red-600">{{ $stats['missed'] }}</p><p class="text-xs text-gray-500 mt-1">Missed</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-gray-700">{{ sprintf('%02d:%02d', floor($stats['avg_duration']/60), $stats['avg_duration']%60) }}</p><p class="text-xs text-gray-500 mt-1">Avg Duration</p></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Calls --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
            <h4 class="text-sm font-bold text-gray-700">Recent Calls</h4>
            <a href="{{ route('admin.call-center.calls') }}" class="text-xs text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Direction</th><th class="px-4 py-2 font-medium">Phone</th><th class="px-4 py-2 font-medium">Duration</th><th class="px-4 py-2 font-medium">Status</th><th class="px-4 py-2 font-medium">Time</th></tr></thead>
            <tbody>
            @forelse($recentCalls as $call)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $call->call_direction === 'inbound' ? 'bg-sky-50 text-sky-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $call->call_direction }}</span></td>
                <td class="px-4 py-2 text-xs text-gray-600">{{ $call->caller_phone }}</td>
                <td class="px-4 py-2 text-xs text-gray-600">{{ $call->duration_formatted }}</td>
                <td class="px-4 py-2 text-xs"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $call->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($call->status === 'missed' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ $call->status }}</span></td>
                <td class="px-4 py-2 text-xs text-gray-400">{{ $call->call_start->format('d M H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-xs">No calls logged</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>

    {{-- Campaigns --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h4 class="text-sm font-bold text-gray-700">Campaigns</h4></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Name</th><th class="px-4 py-2 font-medium">Calls</th><th class="px-4 py-2 font-medium">Status</th></tr></thead>
            <tbody>
            @forelse($campaigns as $camp)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs text-gray-700">{{ $camp->name }}</td>
                <td class="px-4 py-2 text-xs text-gray-600">{{ $camp->call_logs_count }}</td>
                <td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $camp->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-500') }}">{{ $camp->status }}</span></td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400 text-xs">No campaigns</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="px-5 py-3 border-t">{{ $campaigns->links() }}</div>
    </div>
</div>
@endsection
