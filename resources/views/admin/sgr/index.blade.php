@extends('layouts.admin')
@section('title', 'SGR - ' . config('app.name'))
@section('page_title', 'SGR Dashboard')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-amber-50 text-amber-700 text-sm border border-amber-100">{{ session('success') }}</div>
@endif

<div class="bg-gradient-to-r from-amber-700 to-amber-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 left-20 w-24 h-24 bg-white/5 rounded-full -mb-12"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">SGR Management</h2>
            <p class="text-amber-100 text-sm mt-1">Reports & action points for SGR operations</p>
        </div>
        <div class="text-right"><p class="text-amber-100 text-xs">{{ now()->format('l, d M Y') }}</p></div>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-amber-600">{{ $stats['total'] }}</p><p class="text-xs text-gray-500 mt-1">Total Action Points</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p><p class="text-xs text-gray-500 mt-1">Pending Approval</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $stats['approved'] }}</p><p class="text-xs text-gray-500 mt-1">Approved</p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</p><p class="text-xs text-gray-500 mt-1">Overdue</p></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Action Points by Status</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50"><th class="px-4 py-2 font-medium">Status</th><th class="px-4 py-2 font-medium text-right">Count</th></tr></thead>
                <tbody>
                @forelse($stats['by_status'] as $item)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2 text-xs text-gray-700">{{ $item->status ?: 'No status' }}</td>
                    <td class="px-4 py-2 text-xs font-semibold text-gray-900 text-right">{{ $item->total }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400 text-xs">No data yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('admin.sgr.action-points.import') }}" class="flex items-center gap-3 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Action Points
            </a>
            <a href="{{ route('admin.sgr.action-points.reports') }}" class="flex items-center gap-3 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                View Reports
            </a>
            <a href="{{ route('admin.sgr.download-template') }}" class="flex items-center gap-3 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download Template
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-700">Recent Action Points</h4>
        <a href="{{ route('admin.sgr.action-points.reports') }}" class="text-xs text-amber-600 hover:text-amber-700">View All</a>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Activity</th><th class="px-4 py-2 font-medium">Responsible</th><th class="px-4 py-2 font-medium">Due Date</th><th class="px-4 py-2 font-medium">Status</th><th class="px-4 py-2 font-medium">Approval</th></tr></thead>
        <tbody>
        @forelse($recent as $ap)
        <tr class="border-t border-gray-100">
            <td class="px-4 py-2 text-xs text-gray-700 max-w-xs truncate">{{ $ap->activity }}</td>
            <td class="px-4 py-2 text-xs text-gray-600">{{ $ap->responsible_person }}</td>
            <td class="px-4 py-2 text-xs text-gray-500">{{ $ap->due_date?->format('d M Y') ?: '—' }}</td>
            <td class="px-4 py-2 text-xs"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600">{{ $ap->status ?: 'No status' }}</span></td>
            <td class="px-4 py-2 text-xs"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $ap->approval_status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($ap->approval_status === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ $ap->approval_status }}</span></td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-xs">No action points yet. <a href="{{ route('admin.sgr.action-points.import') }}" class="text-amber-600 hover:text-amber-700">Import now</a></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

@if($isAdmin && $pendingBatches->isNotEmpty())
<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
        <h4 class="text-sm font-bold text-gray-700">Pending Batches</h4>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Batch</th><th class="px-4 py-2 font-medium">Filename</th><th class="px-4 py-2 font-medium">Rows</th><th class="px-4 py-2 font-medium">Uploaded</th><th class="px-4 py-2 font-medium"></th></tr></thead>
        <tbody>
        @foreach($pendingBatches as $pb)
        <tr class="border-t border-gray-100">
            <td class="px-4 py-2 text-[10px] text-gray-400 font-mono">{{ $pb->import_batch }}</td>
            <td class="px-4 py-2 text-xs text-gray-700">{{ $pb->source_filename }}</td>
            <td class="px-4 py-2 text-xs text-gray-600">{{ $pb->total }}</td>
            <td class="px-4 py-2 text-xs text-gray-500">{{ \Carbon\Carbon::parse($pb->uploaded_at)->diffForHumans() }}</td>
            <td class="px-4 py-2 text-xs">
                <form method="POST" action="{{ route('admin.sgr.action-points.approve') }}" class="flex gap-1">
                    @csrf
                    <input type="hidden" name="batch" value="{{ $pb->import_batch }}">
                    <button type="submit" name="action" value="approve" class="px-2 py-1 bg-emerald-600 text-white text-[10px] font-medium rounded hover:bg-emerald-700">Approve</button>
                    <button type="submit" name="action" value="reject" class="px-2 py-1 bg-red-600 text-white text-[10px] font-medium rounded hover:bg-red-700">Reject</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endif
@endsection
