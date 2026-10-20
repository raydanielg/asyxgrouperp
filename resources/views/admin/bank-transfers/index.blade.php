@extends('layouts.admin')
@section('title', 'Bank Transfers - ' . config('app.name'))
@section('page_title', 'Bank Transfer Payments')
@section('content')

@php
    $pendingCount = $transfers->where('status', 'pending')->count();
    $sc = ['pending' => '#f59e0b', 'approved' => '#10b981', 'rejected' => '#ef4444'];
@endphp

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $transfers->total() }} transfers</span>
        @if($pendingCount > 0)
        <span class="text-xs text-amber-600 font-medium">{{ $pendingCount }} awaiting approval</span>
        @endif
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Order ID</th><th class="px-5 py-3 font-semibold">User</th><th class="px-5 py-3 font-semibold text-right">Amount</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold">Date</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($transfers as $transfer)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono font-medium text-gray-900">{{ $transfer->order_id }}</td>
            <td class="px-5 py-3.5">
                <p class="text-xs text-gray-800">{{ $transfer->user?->name ?? '—' }}</p>
                @if($transfer->user?->email)<p class="text-[10px] text-gray-400 mt-0.5">{{ $transfer->user->email }}</p>@endif
            </td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold text-gray-900">TZS {{ number_format($transfer->price ?? 0) }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc[$transfer->status] ?? '#9ca3af' }}"></span>{{ ucfirst($transfer->status) }}</span></td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $transfer->created_at->format('d M Y, H:i') }}</td>
            <td class="px-5 py-3.5">
                @if($transfer->status === 'pending')
                <div class="flex items-center justify-end gap-4">
                    <form method="POST" action="{{ route('admin.bank-transfers.update', $transfer) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Approve this transfer?', color: '#059669'})">@csrf @method('PATCH')
                        <input type="hidden" name="status" value="approved"><button class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.bank-transfers.update', $transfer) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Reject this transfer?', color: '#dc2626'})">@csrf @method('PATCH')
                        <input type="hidden" name="status" value="rejected"><button class="text-xs font-medium text-gray-400 hover:text-red-600">Reject</button>
                    </form>
                </div>
                @else
                <div class="text-right text-xs text-gray-300">—</div>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-xs">No bank transfers found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($transfers->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $transfers->links() }}</div>
    @endif
</div>
@endsection
