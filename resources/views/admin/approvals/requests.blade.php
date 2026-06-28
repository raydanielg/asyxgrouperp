@extends('layouts.admin')
@section('title', 'Approval Requests - ' . config('app.name'))
@section('page_title', 'All Approval Requests')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.approvals.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Workflows</a>
</div>
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Number</th><th class="px-5 py-3 font-medium">Module</th><th class="px-5 py-3 font-medium text-right">Amount</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Level</th><th class="px-5 py-3 font-medium">Requested By</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($requests as $req)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium">{{ $req->request_number }}</td>
            <td class="px-5 py-3 text-xs capitalize">{{ str_replace('_', ' ', $req->module_label ?? $req->module) }}</td>
            <td class="px-5 py-3 text-xs text-right font-medium">{{ number_format($req->amount, 0) }}</td>
            <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $req->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($req->status === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ $req->status }}</span></td>
            <td class="px-5 py-3 text-xs">{{ $req->current_level }} / {{ $req->workflow->steps->count() }}</td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $req->requestedBy?->name }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $req->created_at->format('d M Y') }}</td>
            <td class="px-5 py-3 flex gap-2">
                @if($req->status === 'pending')
                <form method="POST" action="{{ route('admin.approvals.approve', $req) }}">@csrf<button class="text-xs text-emerald-600 hover:text-emerald-700">Approve</button></form>
                <form method="POST" action="{{ route('admin.approvals.reject', $req) }}" onsubmit="return confirm('Reject?')">@csrf<input type="hidden" name="rejection_reason" value="Rejected"><button class="text-xs text-red-500 hover:text-red-700">Reject</button></form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No approval requests</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $requests->links() }}</div>
</div>
@endsection
