@extends('layouts.admin')
@section('title', 'Approval Workflows - ' . config('app.name'))
@section('page_title', 'Approval Workflows')
@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

{{-- Pending Approvals --}}
@if($pendingRequests->isNotEmpty())
<div class="bg-white rounded-md border border-amber-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-amber-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span style="width:7px;height:7px;border-radius:99px;background:#f59e0b"></span>
            <h3 class="text-xs font-bold uppercase tracking-widest text-amber-700">Pending Your Approval</h3>
        </div>
        <span class="text-[11px] font-bold text-amber-700">{{ $pendingRequests->count() }}</span>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100">
                <th class="px-5 py-2.5 font-semibold">Number</th><th class="px-5 py-2.5 font-semibold">Module</th><th class="px-5 py-2.5 font-semibold text-right">Amount</th><th class="px-5 py-2.5 font-semibold">Requested By</th><th class="px-5 py-2.5 font-semibold">Level</th><th class="px-5 py-2.5 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @foreach($pendingRequests as $req)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3 text-xs font-mono font-medium text-gray-900">{{ $req->request_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ $req->module_label ?? str_replace('_', ' ', $req->module) }}</td>
            <td class="px-5 py-3 text-xs text-right font-mono font-bold text-gray-900">TZS {{ number_format($req->amount, 0) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $req->requestedBy?->name ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">Level {{ $req->current_level }}</td>
            <td class="px-5 py-3">
                <div class="flex items-center justify-end gap-4">
                    <form method="POST" action="{{ route('admin.approvals.approve', $req) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Approve {{ $req->request_number }}?', color: '#059669'})">@csrf<button class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Approve</button></form>
                    <form method="POST" action="{{ route('admin.approvals.reject', $req) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Reject {{ $req->request_number }}?', color: '#dc2626'})">@csrf<input type="hidden" name="rejection_reason" value="Rejected by approver"><button class="text-xs font-medium text-gray-400 hover:text-red-600">Reject</button></form>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endif

{{-- Workflows --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $workflows->count() }} workflows · configure multi-level approvals</span>
        <a href="{{ route('admin.approvals.create') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Workflow</a>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Workflow</th><th class="px-5 py-3 font-semibold">Module</th><th class="px-5 py-3 font-semibold text-right">Steps</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($workflows as $wf)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.approvals.show', $wf) }}" class="text-xs font-medium text-gray-900 hover:text-gray-600">{{ $wf->name }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $wf->module) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-500">{{ $wf->steps->count() }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $wf->is_active ? '#10b981' : '#d1d5db' }}"></span>{{ $wf->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.approvals.show', $wf) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <form method="POST" action="{{ route('admin.approvals.destroy', $wf) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete this workflow?'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-xs">No workflows defined</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection
