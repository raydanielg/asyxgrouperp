@extends('layouts.admin')
@section('title', 'Login History - ' . config('app.name'))
@section('page_title', 'Login History')
@section('content')
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left border-b border-gray-200 bg-gray-50/50">
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">User</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">IP Address</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Type</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Date</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($histories as $history)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $history->user?->name ?? 'Unknown' }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $history->ip }}</td>
            <td class="px-5 py-3.5">
                @php $tc = $history->type === 'login' ? '#10b981' : '#ef4444'; @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $tc }}"></span>{{ ucfirst($history->type) }}</span>
            </td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $history->date->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400 text-xs">No login history found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($histories->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $histories->links() }}</div>
    @endif
</div>
@endsection
