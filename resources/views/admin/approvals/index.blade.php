@extends('layouts.admin')
@section('title', 'Approval Workflows - ' . config('app.name'))
@section('page_title', 'Approval Workflows')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Configure multi-level approval processes</p>
    <a href="{{ route('admin.approvals.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Workflow
    </a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

{{-- Pending Requests --}}
@if($pendingRequests->isNotEmpty())
<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-3 border-b bg-amber-50/50"><h3 class="text-sm font-bold text-amber-700">Pending Approvals ({{ $pendingRequests->count() }})</h3></div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-2 font-medium">Number</th><th class="px-5 py-2 font-medium">Module</th><th class="px-5 py-2 font-medium">Amount</th><th class="px-5 py-2 font-medium">Requested By</th><th class="px-5 py-2 font-medium">Level</th><th class="px-5 py-2 font-medium">Actions</th></tr></thead>
        <tbody>
        @foreach($pendingRequests as $req)
        <tr class="border-t border-gray-100">
            <td class="px-5 py-2 text-xs font-medium">{{ $req->request_number }}</td>
            <td class="px-5 py-2 text-xs">{{ $req->module_label ?? $req->module }}</td>
            <td class="px-5 py-2 text-xs font-medium">{{ number_format($req->amount, 0) }}</td>
            <td class="px-5 py-2 text-xs text-gray-600">{{ $req->requestedBy?->name }}</td>
            <td class="px-5 py-2 text-xs">Level {{ $req->current_level }}</td>
            <td class="px-5 py-2 flex gap-2">
                <form method="POST" action="{{ route('admin.approvals.approve', $req) }}">@csrf<button class="text-xs text-emerald-600 hover:text-emerald-700">Approve</button></form>
                <form method="POST" action="{{ route('admin.approvals.reject', $req) }}" onsubmit="return confirm('Reject this request?')">@csrf<input type="hidden" name="rejection_reason" value="Rejected by approver"><button class="text-xs text-red-500 hover:text-red-700">Reject</button></form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endif

{{-- Workflows --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Workflow</th><th class="px-5 py-3 font-medium">Module</th><th class="px-5 py-3 font-medium">Steps</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($workflows as $wf)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs"><a href="{{ route('admin.approvals.show', $wf) }}" class="font-medium text-gray-800 hover:text-emerald-600">{{ $wf->name }}</a></td>
            <td class="px-5 py-3 text-xs"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100 capitalize">{{ str_replace('_', ' ', $wf->module) }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $wf->steps->count() }} step(s)</td>
            <td class="px-5 py-3">@if($wf->is_active)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>@else<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100">Inactive</span>@endif</td>
            <td class="px-5 py-3 flex items-center gap-3">
                <a href="{{ route('admin.approvals.show', $wf) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">View</a>
                <form method="POST" action="{{ route('admin.approvals.destroy', $wf) }}" onsubmit="return confirm('Delete this workflow?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs">Delete</button></form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No workflows defined</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection
