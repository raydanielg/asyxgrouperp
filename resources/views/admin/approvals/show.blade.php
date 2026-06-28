@extends('layouts.admin')
@section('title', $workflow->name . ' - ' . config('app.name'))
@section('page_title', $workflow->name)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.approvals.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Workflows</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Workflow Info --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="font-bold text-gray-800 mb-3">{{ $workflow->name }}</h3>
        <p class="text-xs text-gray-500 mb-4">{{ $workflow->description }}</p>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-400">Module</dt><dd class="text-gray-700 capitalize">{{ str_replace('_', ' ', $workflow->module) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Steps</dt><dd class="text-gray-700">{{ $workflow->steps->count() }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Status</dt><dd>@if($workflow->is_active)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>@else<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100">Inactive</span>@endif</dd></div>
        </dl>
        <div class="mt-4 pt-4 border-t">
            <h4 class="text-xs font-bold text-gray-600 mb-2">Approval Chain</h4>
            <div class="space-y-2">
                @foreach($workflow->steps as $step)
                <div class="flex items-center gap-2 text-xs">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px]">{{ $step->level }}</div>
                    <div>
                        <p class="text-gray-700 font-medium">{{ $step->name }}</p>
                        <p class="text-gray-400 text-[10px]">{{ ucfirst($step->approver_type) }}: {{ $step->approver_role ?? $step->approver?->name ?? '—' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Requests --}}
    <div class="bg-white rounded-xl border overflow-hidden lg:col-span-2">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h4 class="text-sm font-bold text-gray-700">Approval Requests</h4></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Number</th><th class="px-4 py-2 font-medium">Amount</th><th class="px-4 py-2 font-medium">Status</th><th class="px-4 py-2 font-medium">Level</th><th class="px-4 py-2 font-medium">Requested By</th><th class="px-4 py-2 font-medium">Date</th></tr></thead>
            <tbody>
            @forelse($requests as $req)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs font-medium">{{ $req->request_number }}</td>
                <td class="px-4 py-2 text-xs">{{ number_format($req->amount, 0) }}</td>
                <td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $req->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($req->status === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ $req->status }}</span></td>
                <td class="px-4 py-2 text-xs">{{ $req->current_level }}</td>
                <td class="px-4 py-2 text-xs text-gray-600">{{ $req->requestedBy?->name }}</td>
                <td class="px-4 py-2 text-xs text-gray-400">{{ $req->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400 text-xs">No requests yet</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="px-5 py-3 border-t">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
