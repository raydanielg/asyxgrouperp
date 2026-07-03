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
        <tbody>
        @forelse($proposals as $proposal)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
            <td class="px-5 py-3 text-xs font-mono text-gray-700"><a href="{{ route('admin.sales-proposals.show', $proposal) }}" class="hover:text-emerald-600 font-medium">{{ $proposal->proposal_number }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $proposal->customer?->name ?? 'N/A' }}<br><span class="text-[10px] text-gray-400">{{ $proposal->customer?->email ?? '' }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $proposal->proposal_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $proposal->due_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($proposal->total_amount) }}</td>
            <td class="px-5 py-3">@php $c=['draft'=>'gray','sent'=>'sky','accepted'=>'emerald','rejected'=>'red']; @endphp<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-{{ $c[$proposal->status] ?? 'gray' }}-50 text-{{ $c[$proposal->status] ?? 'gray' }}-700 border border-{{ $c[$proposal->status] ?? 'gray' }}-100">{{ ucfirst($proposal->status) }}</span></td>
            <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.sales-proposals.show', $proposal) }}" class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 flex items-center justify-center transition-all" title="View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="{{ route('admin.sales-proposals.edit', $proposal) }}" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </a>
                    @if($proposal->status === 'accepted' && !$proposal->converted_to_invoice)
                    <button onclick="convertProposal({{ $proposal->id }}, '{{ $proposal->proposal_number }}')" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-all" title="Convert to Invoice">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </button>
                    <form id="convert-proposal-{{ $proposal->id }}" method="POST" action="{{ route('admin.sales-proposals.convert', $proposal) }}" class="hidden">@csrf</form>
                    @endif
                    <form id="del-proposal-{{ $proposal->id }}" method="POST" action="{{ route('admin.sales-proposals.destroy', $proposal) }}" class="hidden">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-proposal-{{ $proposal->id }}', 'Delete Quotation?', 'Quotation {{ $proposal->proposal_number }} will be permanently removed.')" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-12 text-center"><div class="flex flex-col items-center gap-3"><svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p class="text-sm text-gray-400">No quotations yet</p><a href="{{ route('admin.sales-proposals.create') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Create your first quotation →</a></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $proposals->links() }}</div>
</div>
@endsection
