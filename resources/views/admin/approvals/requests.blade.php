@extends('layouts.admin')
@section('title', 'Approval Requests - ' . config('app.name'))
@section('page_title', 'All Approval Requests')
@section('content')
@php $sc = ['approved' => '#10b981', 'rejected' => '#ef4444', 'pending' => '#f59e0b']; @endphp

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

<div class="mb-4"><a href="{{ route('admin.approvals.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Workflows</a></div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">All approval requests</span>
        <span class="text-xs text-gray-400">{{ $requests->total() }} requests</span>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Number</th><th class="px-5 py-3 font-semibold">Module</th><th class="px-5 py-3 font-semibold text-right">Amount</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold">Progress</th><th class="px-5 py-3 font-semibold">Requested By</th><th class="px-5 py-3 font-semibold text-right">Date</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($requests as $req)
        @php $totalSteps = $req->workflow?->steps->count() ?? 1; @endphp
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono font-medium text-gray-900">{{ $req->request_number }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $req->module_label ?? $req->module) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold text-gray-900">TZS {{ number_format($req->amount, 0) }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc[$req->status] ?? '#9ca3af' }}"></span>{{ $req->status }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-1.5">
                    <div class="w-16 h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gray-800" style="width:{{ min(100, ($req->current_level / max(1, $totalSteps)) * 100) }}%"></div>
                    </div>
                    <span class="text-[10px] text-gray-400 font-mono">{{ $req->current_level }}/{{ $totalSteps }}</span>
                </div>
            </td>
            <td class="px-5 py-3.5 text-xs {{ $req->requestedBy ? 'text-gray-500' : 'text-gray-300' }}">{{ $req->requestedBy?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right text-gray-400">{{ $req->created_at->format('d M Y') }}</td>
            <td class="px-5 py-3.5">
                @if($req->status === 'pending')
                <div class="flex items-center justify-end gap-4">
                    <form method="POST" action="{{ route('admin.approvals.approve', $req) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Approve {{ $req->request_number }}?', color: '#059669'})">@csrf<button class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Approve</button></form>
                    <form method="POST" action="{{ route('admin.approvals.reject', $req) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Reject {{ $req->request_number }}?', color: '#dc2626'})">@csrf<input type="hidden" name="rejection_reason" value="Rejected"><button class="text-xs font-medium text-gray-400 hover:text-red-600">Reject</button></form>
                </div>
                @else
                <div class="text-right text-xs text-gray-300">—</div>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No approval requests</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($requests->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
