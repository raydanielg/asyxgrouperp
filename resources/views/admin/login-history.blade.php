@extends('layouts.admin')
@section('title', 'Login History - ' . config('app.name'))
@section('page_title', 'Login History')
@section('content')
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">User</th><th class="px-5 py-3 font-medium">IP Address</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Date</th></tr></thead>
        <tbody>@forelse($histories as $history)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs text-gray-700">{{ $history->user?->name ?? 'Unknown' }}</td>
            <td class="px-5 py-3 text-xs font-mono text-gray-500">{{ $history->ip }}</td>
            <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $history->type === 'login' ? 'emerald' : 'red' }}-50 text-{{ $history->type === 'login' ? 'emerald' : 'red' }}-700 border border-{{ $history->type === 'login' ? 'emerald' : 'red' }}-100">{{ ucfirst($history->type) }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $history->date->format('d M Y H:i') }}</td>
        
        </tr>
        @empty
        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No login history found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $histories->links() }}</div>
</div>
@endsection
