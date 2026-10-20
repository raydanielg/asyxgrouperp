@extends('layouts.admin')
@section('title', $workflow->name . ' - ' . config('app.name'))
@section('page_title', $workflow->name)
@section('content')
@php $sc = ['approved' => '#10b981', 'rejected' => '#ef4444', 'pending' => '#f59e0b']; @endphp

<div class="mb-4"><a href="{{ route('admin.approvals.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Workflows</a></div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Workflow info + chain --}}
    <div class="space-y-4">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between">
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-gray-900">{{ $workflow->name }}</h3>
                    @if($workflow->description)<p class="text-[11px] text-gray-400 mt-1">{{ $workflow->description }}</p>@endif
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 flex-shrink-0"><span style="width:6px;height:6px;border-radius:99px;background:{{ $workflow->is_active ? '#10b981' : '#d1d5db' }}"></span>{{ $workflow->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="px-5 py-4 grid grid-cols-2 gap-y-3 text-xs">
                <div><p class="text-gray-400">Module</p><p class="text-gray-800 mt-0.5 capitalize">{{ str_replace('_', ' ', $workflow->module) }}</p></div>
                <div><p class="text-gray-400">Steps</p><p class="text-gray-800 mt-0.5">{{ $workflow->steps->count() }}</p></div>
            </div>
        </div>

        {{-- Approval chain --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Approval Chain</h4></div>
            <div class="px-5 py-4">
                @forelse($workflow->steps as $i => $step)
                <div class="flex items-start gap-3 {{ !$loop->last ? 'pb-3.5 mb-3.5 border-b border-gray-50' : '' }}">
                    <div class="flex flex-col items-center flex-shrink-0">
                        <span class="w-6 h-6 rounded-full bg-gray-900 text-white flex items-center justify-center text-[10px] font-bold">{{ $step->level }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-900">{{ $step->name }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ ucfirst($step->approver_type) }}{{ ($step->approver_role ?? $step->approver?->name) ? ' · ' . ($step->approver_role ?? $step->approver->name) : '' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-4">No steps defined</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Requests --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden lg:col-span-2 self-start">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Approval Requests</h4>
            <span class="text-[11px] text-gray-400">{{ $requests->total() }}</span>
        </div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100">
                    <th class="px-5 py-2.5 font-semibold">Number</th><th class="px-5 py-2.5 font-semibold text-right">Amount</th><th class="px-5 py-2.5 font-semibold">Status</th><th class="px-5 py-2.5 font-semibold">Level</th><th class="px-5 py-2.5 font-semibold">Requested By</th><th class="px-5 py-2.5 font-semibold text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($requests as $req)
            <tr class="hover:bg-gray-50/60">
                <td class="px-5 py-3 text-xs font-mono font-medium text-gray-900">{{ $req->request_number }}</td>
                <td class="px-5 py-3 text-xs text-right font-mono font-bold text-gray-900">{{ number_format($req->amount, 0) }}</td>
                <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc[$req->status] ?? '#9ca3af' }}"></span>{{ $req->status }}</span></td>
                <td class="px-5 py-3 text-xs text-gray-400">Level {{ $req->current_level }}</td>
                <td class="px-5 py-3 text-xs {{ $req->requestedBy ? 'text-gray-500' : 'text-gray-300' }}">{{ $req->requestedBy?->name ?? '—' }}</td>
                <td class="px-5 py-3 text-xs text-right text-gray-400">{{ $req->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No requests yet</td></tr>
            @endforelse
            </tbody>
        </table></div>
        @if($requests->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">{{ $requests->links() }}</div>
        @endif
    </div>
</div>
@endsection
