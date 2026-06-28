@extends('layouts.admin')
@section('title', 'Audit Logs - ' . config('app.name'))
@section('page_title', 'Audit Logs')
@section('content')
<div class="mb-4">
    <form method="GET" action="{{ route('admin.audit-logs.filter') }}" class="flex flex-wrap gap-3 items-end">
        <div><label class="block text-xs font-medium text-gray-600 mb-1">Action</label><select name="action" class="px-3 py-2 text-sm border border-gray-200 rounded-lg"><option value="">All</option><option value="create">Create</option><option value="update">Update</option><option value="delete">Delete</option><option value="login">Login</option><option value="logout">Logout</option><option value="view">View</option><option value="export">Export</option></select></div>
        <div><label class="block text-xs font-medium text-gray-600 mb-1">Module</label><input type="text" name="module" placeholder="module name" value="{{ request('module') }}" class="px-3 py-2 text-sm border border-gray-200 rounded-lg"></div>
        <div><label class="block text-xs font-medium text-gray-600 mb-1">From</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 text-sm border border-gray-200 rounded-lg"></div>
        <div><label class="block text-xs font-medium text-gray-600 mb-1">To</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 text-sm border border-gray-200 rounded-lg"></div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Filter</button>
        <a href="{{ route('admin.audit-logs.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">Clear</a>
    </form>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">User</th><th class="px-5 py-3 font-medium">Action</th><th class="px-5 py-3 font-medium">Module</th><th class="px-5 py-3 font-medium">IP</th><th class="px-5 py-3 font-medium">URL</th><th class="px-5 py-3 font-medium">Time</th></tr></thead>
        <tbody>
        @forelse($logs as $log)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs text-gray-700">{{ $log->user_name ?? $log->user?->name ?? 'System' }}</td>
            <td class="px-5 py-3 text-xs"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $log->action === 'delete' ? 'bg-red-50 text-red-700' : ($log->action === 'create' ? 'bg-emerald-50 text-emerald-700' : ($log->action === 'update' ? 'bg-amber-50 text-amber-700' : 'bg-sky-50 text-sky-700')) }}">{{ $log->action }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $log->module ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $log->ip_address ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400 max-w-xs truncate">{{ $log->url ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $log->created_at->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No audit logs</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $logs->links() }}</div>
</div>
@endsection
