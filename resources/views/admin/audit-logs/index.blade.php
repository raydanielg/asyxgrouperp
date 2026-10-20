@extends('layouts.admin')
@section('title', 'Audit Logs - ' . config('app.name'))
@section('page_title', 'Audit Logs')
@section('content')
@php $ac = ['delete' => '#ef4444', 'create' => '#10b981', 'update' => '#f59e0b', 'login' => '#3b82f6', 'logout' => '#9ca3af', 'export' => '#8b5cf6', 'view' => '#64748b']; @endphp

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
        <form method="GET" action="{{ route('admin.audit-logs.filter') }}" class="flex items-center gap-2 flex-wrap">
            <select name="action" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Actions</option>
                @foreach(['create','update','delete','login','logout','view','export'] as $a)
                <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                @endforeach
            </select>
            <input type="text" name="module" placeholder="Module" value="{{ request('module') }}" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 w-32">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400">
            <button type="submit" class="px-4 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Filter</button>
            @if(request()->hasAny(['action','module','date_from','date_to']))
            <a href="{{ route('admin.audit-logs.index') }}" class="text-xs text-gray-400 hover:text-gray-600">Clear</a>
            @endif
        </form>
        <span class="text-xs text-gray-400">{{ $logs->total() }} entries</span>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">User</th><th class="px-5 py-3 font-semibold">Action</th><th class="px-5 py-3 font-semibold">Module</th><th class="px-5 py-3 font-semibold">IP</th><th class="px-5 py-3 font-semibold">URL</th><th class="px-5 py-3 font-semibold text-right">Time</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($logs as $log)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $log->user_name ?? $log->user?->name ?? 'System' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $ac[$log->action] ?? '#9ca3af' }}"></span>{{ $log->action }}</span></td>
            <td class="px-5 py-3.5 text-xs text-gray-500 capitalize">{{ $log->module ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-400">{{ $log->ip_address ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-400 max-w-[220px] truncate" title="{{ $log->url }}">{{ $log->url ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right text-gray-400">{{ $log->created_at->format('d M Y, H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-xs">No audit logs</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($logs->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
