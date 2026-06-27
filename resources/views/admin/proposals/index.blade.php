@extends('layouts.admin')
@section('title', 'Quotations - ' . config('app.name'))
@section('page_title', 'Sales Quotations / Proposals')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <p class="text-sm text-gray-500">Create professional quotations for your products and services</p>
    <a href="{{ route('admin.sales-proposals.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-medium rounded-lg hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Quotation
    </a>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Quotation #</th><th class="px-5 py-3 font-medium">Customer</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Due Date</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($proposals as $proposal)<tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
            <td class="px-5 py-3 text-xs font-mono text-gray-700"><a href="{{ route('admin.sales-proposals.show', $proposal) }}" class="hover:text-emerald-600 font-medium">{{ $proposal->proposal_number }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $proposal->customer?->name ?? 'N/A' }}<br><span class="text-[10px] text-gray-400">{{ $proposal->customer?->email ?? '' }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $proposal->proposal_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $proposal->due_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($proposal->total_amount) }}</td>
            <td class="px-5 py-3">@php $c=['draft'=>'gray','sent'=>'sky','accepted'=>'emerald','rejected'=>'red']; @endphp<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-{{ $c[$proposal->status] ?? 'gray' }}-50 text-{{ $c[$proposal->status] ?? 'gray' }}-700 border border-{{ $c[$proposal->status] ?? 'gray' }}-100">{{ ucfirst($proposal->status) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-3">
                <a href="{{ route('admin.sales-proposals.show', $proposal) }}" class="text-sky-600 hover:text-sky-700 text-xs font-medium">View</a>
                <a href="{{ route('admin.sales-proposals.edit', $proposal) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Edit</a>
                @if($proposal->status === 'accepted' && !$proposal->converted_to_invoice)<form method="POST" action="{{ route('admin.sales-proposals.convert', $proposal) }}">@csrf<button type="submit" class="text-amber-600 hover:text-amber-700 text-xs font-medium" onclick="return confirm('Convert this quotation to an invoice?')">→ Invoice</button></form>@endif
                <form method="POST" action="{{ route('admin.sales-proposals.destroy', $proposal) }}" class="inline" onsubmit="return confirm('Delete this quotation?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs">Delete</button></form>
            </td>
        </tr>@empty<tr><td colspan="7" class="px-5 py-12 text-center"><div class="flex flex-col items-center gap-3"><svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p class="text-sm text-gray-400">No quotations yet</p><a href="{{ route('admin.sales-proposals.create') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Create your first quotation →</a></div></td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $proposals->links() }}</div>
</div>
@endsection
