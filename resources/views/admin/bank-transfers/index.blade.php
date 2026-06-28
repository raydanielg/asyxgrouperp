@extends('layouts.admin')
@section('title', 'Bank Transfers - ' . config('app.name'))
@section('page_title', 'Bank Transfer Payments')
@section('content')
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Order ID</th><th class="px-5 py-3 font-medium">User</th><th class="px-5 py-3 font-medium">Amount</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($transfers as $transfer)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $transfer->order_id }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $transfer->user?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($transfer->price ?? 0) }} {{ $transfer->price_currency }}</td>
            <td class="px-5 py-3">@php $c=['pending'=>'amber','approved'=>'emerald','rejected'=>'red']; @endphp<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$transfer->status] ?? 'gray' }}-50 text-{{ $c[$transfer->status] ?? 'gray' }}-700 border border-{{ $c[$transfer->status] ?? 'gray' }}-100">{{ ucfirst($transfer->status) }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $transfer->created_at->format('d M Y') }}</td>
            <td class="px-5 py-3">@if($transfer->status === 'pending')
                <form method="POST" action="{{ route('admin.bank-transfers.update', $transfer) }}" class="inline flex gap-2">@csrf @method('PATCH')
                    <input type="hidden" name="status" value="approved"><button class="text-emerald-600 hover:text-emerald-700 text-xs">Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.bank-transfers.update', $transfer) }}" class="inline">@csrf @method('PATCH')
                    <input type="hidden" name="status" value="rejected"><button class="text-red-500 hover:text-red-700 text-xs">Reject</button>
                </form>
            @endif</td>
        
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No bank transfers found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $transfers->links() }}</div>
</div>
@endsection
