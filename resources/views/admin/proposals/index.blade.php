@extends('layouts.admin')
@section('title', 'Quotations - ' . config('app.name'))
@section('page_title', 'Sales Quotations')
@section('content')
@php
$totalValue = $proposals->sum('total_amount');
$acceptedCount = $proposals->where('status', 'accepted')->count();
$draftCount = $proposals->where('status', 'draft')->count();
$sentCount = $proposals->where('status', 'sent')->count();
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Total Quoted</span>
        <p class="text-xl font-bold text-gray-900 mt-1">TZS {{ number_format($totalValue) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-emerald-200 p-4">
        <span class="text-[10px] font-medium text-emerald-600 uppercase tracking-wider">Accepted</span>
        <p class="text-xl font-bold text-emerald-700 mt-1">{{ $acceptedCount }}</p>
    </div>
    <div class="bg-white rounded-xl border border-amber-200 p-4">
        <span class="text-[10px] font-medium text-amber-600 uppercase tracking-wider">Sent</span>
        <p class="text-xl font-bold text-amber-700 mt-1">{{ $sentCount }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Draft</span>
        <p class="text-xl font-bold text-gray-700 mt-1">{{ $draftCount }}</p>
    </div>
</div>

<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2 text-xs text-gray-500">
        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-50 border">{{ $proposals->total() ?? 0 }} Total</span>
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-100">Rejected: {{ $proposals->where('status', 'rejected')->count() }}</span>
    </div>
    <a href="{{ route('admin.sales-proposals.create') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-medium rounded-lg hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Quotation
    </a>
</div>

<div class="bg-white rounded-xl border overflow-hidden shadow-sm">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Quotation #</th>
            <th class="px-5 py-3 font-medium">Customer</th>
            <th class="px-5 py-3 font-medium">Date</th>
            <th class="px-5 py-3 font-medium">Due Date</th>
            <th class="px-5 py-3 font-medium text-right">Total</th>
            <th class="px-5 py-3 font-medium">Status</th>
            <th class="px-5 py-3 font-medium text-right">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($proposals as $proposal)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
            <td class="px-5 py-3 text-xs font-mono text-gray-700"><a href="{{ route('admin.sales-proposals.show', $proposal) }}" class="hover:text-emerald-600 font-semibold">{{ $proposal->proposal_number }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $proposal->customer?->name ?? 'N/A' }}<br><span class="text-[10px] text-gray-400">{{ $proposal->customer?->email ?? '' }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $proposal->proposal_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs {{ $proposal->due_date->isPast() && $proposal->status !== 'accepted' ? 'text-rose-600 font-medium' : 'text-gray-500' }}">{{ $proposal->due_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TZS {{ number_format($proposal->total_amount) }}</td>
            <td class="px-5 py-3">
                @php $c=['draft'=>'gray','sent'=>'sky','accepted'=>'emerald','rejected'=>'red']; @endphp
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-medium bg-{{ $c[$proposal->status] ?? 'gray' }}-50 text-{{ $c[$proposal->status] ?? 'gray' }}-700 border border-{{ $c[$proposal->status] ?? 'gray' }}-100">
                    @if($proposal->status === 'accepted')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>@endif
                    {{ ucfirst($proposal->status) }}
                </span>
            </td>
            <td class="px-5 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                    <a href="{{ route('admin.sales-proposals.show', $proposal) }}" title="View" class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="{{ route('admin.sales-proposals.edit', $proposal) }}" title="Edit" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </a>
                    @if($proposal->status === 'accepted' && !$proposal->converted_to_invoice)
                    <button onclick="convertProposal({{ $proposal->id }}, '{{ $proposal->proposal_number }}')" title="Convert to Invoice" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </button>
                    <form id="convert-proposal-{{ $proposal->id }}" method="POST" action="{{ route('admin.sales-proposals.convert', $proposal) }}" class="hidden">@csrf</form>
                    @endif
                    <form id="del-proposal-{{ $proposal->id }}" method="POST" action="{{ route('admin.sales-proposals.destroy', $proposal) }}" class="hidden">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-proposal-{{ $proposal->id }}', 'Delete Quotation?', 'Quotation {{ $proposal->proposal_number }} will be permanently removed.')" title="Delete" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-12 text-center">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm text-gray-400">No quotations yet</p>
                <a href="{{ route('admin.sales-proposals.create') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Create your first quotation →</a>
            </div>
        </td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t bg-gray-50/30">{{ $proposals->links() }}</div>
</div>

@push('scripts')
<script>
function convertProposal(id, number) {
  Swal.fire({
    title: 'Convert to Invoice?',
    text: 'Quotation ' + number + ' will be converted into a sales invoice.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d97706',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, convert it',
    cancelButtonText: 'Cancel',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: 'Converting...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });
      document.getElementById('convert-proposal-' + id).submit();
    }
  });
}
</script>
@endpush
@endsection
