@extends('layouts.admin')
@section('title', 'Call Logs - ' . config('app.name'))
@section('page_title', 'Call Logs')
@section('content')
<div class="mb-4"><a href="{{ route('admin.call-center.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Dashboard</a></div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Direction</th><th class="px-5 py-3 font-medium">Caller</th><th class="px-5 py-3 font-medium">Callee</th><th class="px-5 py-3 font-medium">Duration</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Agent</th><th class="px-5 py-3 font-medium">Time</th></tr></thead>
        <tbody>
        @forelse($calls as $call)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $call->call_direction === 'inbound' ? 'bg-sky-50 text-sky-700' : 'bg-emerald-50 text-emerald-700') }}">{{ $call->call_direction }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $call->caller_name ?? $call->caller_phone }}</td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $call->callee_name ?? $call->callee_phone ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $call->duration_formatted }}</td>
            <td class="px-5 py-3 text-xs"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $call->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($call->status === 'missed' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ $call->status }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $call->agent?->name ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $call->call_start->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No call logs</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $calls->links() }}</div>
</div>
@endsection
