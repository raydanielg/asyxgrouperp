@extends('layouts.admin')
@section('title', 'Sales Proposals - ' . config('app.name'))
@section('page_title', 'Sales Proposals')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage sales proposals</p>
    <a href="{{ route('admin.sales-proposals.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Proposal
    </a>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Proposal #</th><th class="px-5 py-3 font-medium">Customer</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($proposals as $proposal)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700"><a href="{{ route('admin.sales-proposals.show', $proposal) }}" class="hover:text-emerald-600">{{ $proposal->proposal_number }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $proposal->customer?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">${{ number_format($proposal->total_amount, 2) }}</td>
            <td class="px-5 py-3">@php $c=['draft'=>'gray','sent'=>'sky','accepted'=>'emerald','rejected'=>'red']; @endphp<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$proposal->status] ?? 'gray' }}-50 text-{{ $c[$proposal->status] ?? 'gray' }}-700 border border-{{ $c[$proposal->status] ?? 'gray' }}-100">{{ ucfirst($proposal->status) }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $proposal->proposal_date->format('d M Y') }}</td>
            <td class="px-5 py-3 flex items-center gap-2"><a href="{{ route('admin.sales-proposals.edit', $proposal) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">Edit</a><form method="POST" action="{{ route('admin.sales-proposals.destroy', $proposal) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs">Delete</button></form></td>
        </tr>@empty<tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No proposals found</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $proposals->links() }}</div>
</div>
@endsection
