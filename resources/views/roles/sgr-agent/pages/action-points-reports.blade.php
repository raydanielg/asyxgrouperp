@extends('layouts.admin')
@section('title', 'My Reports - SGR')
@section('page_title', 'My Action Points Reports')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track your submitted SGR action points</p>
    <a href="{{ route('role.page', ['module' => 'import-action-points']) }}" class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Upload New
    </a>
</div>
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <span class="text-[10px] font-medium text-gray-500">Total</span>
        <p class="text-xl font-bold text-gray-900 mt-1">{{ $total }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <span class="text-[10px] font-medium text-amber-600">Pending Approval</span>
        <p class="text-xl font-bold text-amber-600 mt-1">{{ $pendingApproval }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <span class="text-[10px] font-medium text-emerald-600">Approved</span>
        @php $approved = \App\Models\SgrActionPoint::where('created_by', auth()->id())->where('approval_status', 'approved')->count(); @endphp
        <p class="text-xl font-bold text-emerald-600 mt-1">{{ $approved }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <span class="text-[10px] font-medium text-rose-600">Overdue</span>
        <p class="text-xl font-bold text-rose-600 mt-1">{{ $overdue }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <span class="text-[10px] font-medium text-violet-600">Completed</span>
        <p class="text-xl font-bold text-violet-600 mt-1">{{ $completed }}</p>
    </div>
</div>
<form method="GET" class="mb-4 flex items-center gap-3 flex-wrap">
    <select name="status" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
        <option value="">All Statuses</option>
        @foreach($statuses as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
    </select>
    <select name="approval_status" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
        <option value="">All Approval</option>
        <option value="pending" {{ request('approval_status') === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>Approved</option>
        <option value="rejected" {{ request('approval_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
    </select>
    <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">Filter</button>
    <a href="{{ route('role.page', ['module' => 'action-points-reports']) }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors">Reset</a>
</form>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">#</th><th class="px-5 py-3 font-medium">Activity</th><th class="px-5 py-3 font-medium">Responsible</th><th class="px-5 py-3 font-medium">Due Date</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Approval</th><th class="px-5 py-3 font-medium">Uploaded</th></tr></thead>
        <tbody>
        @forelse($actionPoints as $ap)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs text-gray-400">{{ $ap->row_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-900 max-w-[200px]">{{ Str::limit($ap->activity, 60) }}</td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $ap->responsible_person }}</td>
            <td class="px-5 py-3 text-xs {{ $ap->due_date && $ap->due_date->isPast() && $ap->status !== 'Completed' ? 'text-red-500 font-medium' : 'text-gray-400' }}">{{ $ap->due_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium @if($ap->status === 'Completed') bg-emerald-50 text-emerald-700 @elseif($ap->status === 'In Progress') bg-sky-50 text-sky-700 @else bg-amber-50 text-amber-700 @endif">{{ $ap->status }}</span></td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium @if($ap->approval_status === 'approved') bg-emerald-50 text-emerald-700 @elseif($ap->approval_status === 'rejected') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif">{{ ucfirst($ap->approval_status) }}</span></td>
            <td class="px-5 py-3 text-[10px] text-gray-400">{{ $ap->created_at->format('d M Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No action points found. <a href="{{ route('role.page', ['module' => 'import-action-points']) }}" class="text-amber-600 hover:text-amber-700 font-medium">Upload now</a></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $actionPoints->links() }}</div>
</div>
@endsection