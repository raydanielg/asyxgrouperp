@extends('layouts.admin')
@section('title', 'Quotations - ' . config('app.name'))
@section('page_title', 'Sales Quotations')
@section('content')
@php
$totalValue = $proposals->sum('total_amount');
$acceptedCount = $proposals->where('status', 'accepted')->count();
$draftCount = $proposals->where('status', 'draft')->count();
$sentCount = $proposals->where('status', 'sent')->count();
$rejectedCount = $proposals->where('status', 'rejected')->count();
@endphp

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
        <span class="text-xs text-gray-400">{{ $proposals->total() }} quotations · {{ $rejectedCount }} rejected</span>
        <a href="{{ route('admin.sales-proposals.create') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Quotation</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Quoted</p><p class="text-lg font-bold text-gray-900 mt-0.5">TZS {{ number_format($totalValue) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Accepted</p><p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $acceptedCount }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Sent</p><p class="text-lg font-bold text-blue-600 mt-0.5">{{ $sentCount }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Draft</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ $draftCount }}</p></div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Quotation #</th><th class="px-5 py-3 font-semibold">Customer</th><th class="px-5 py-3 font-semibold">Date</th><th class="px-5 py-3 font-semibold">Due Date</th><th class="px-5 py-3 font-semibold text-right">Total</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($proposals as $proposal)
        @php $pc = ['draft'=>'#9ca3af','sent'=>'#3b82f6','accepted'=>'#10b981','rejected'=>'#ef4444'][$proposal->status] ?? '#9ca3af'; @endphp
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5"><a href="{{ route('admin.sales-proposals.show', $proposal) }}" class="text-xs font-mono font-medium text-gray-900 hover:text-gray-600">{{ $proposal->proposal_number }}</a></td>
            <td class="px-5 py-3.5">
                <p class="text-xs text-gray-800">{{ $proposal->customer?->name ?? '—' }}</p>
                @if($proposal->customer?->email)<p class="text-[10px] text-gray-400 mt-0.5">{{ $proposal->customer->email }}</p>@endif
            </td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $proposal->proposal_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5 text-xs {{ $proposal->due_date->isPast() && $proposal->status !== 'accepted' ? 'text-red-600 font-medium' : 'text-gray-400' }}">{{ $proposal->due_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold text-gray-900">TZS {{ number_format($proposal->total_amount) }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $pc }}"></span>{{ ucfirst($proposal->status) }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.sales-proposals.show', $proposal) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <a href="{{ route('admin.sales-proposals.edit', $proposal) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</a>
                    @if($proposal->status === 'accepted' && !$proposal->converted_to_invoice)
                    <button onclick="convertProposal({{ $proposal->id }}, '{{ $proposal->proposal_number }}')" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">→ Invoice</button>
                    <form id="convert-proposal-{{ $proposal->id }}" method="POST" action="{{ route('admin.sales-proposals.convert', $proposal) }}" class="hidden">@csrf</form>
                    @endif
                    <form id="del-proposal-{{ $proposal->id }}" method="POST" action="{{ route('admin.sales-proposals.destroy', $proposal) }}" class="hidden">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-proposal-{{ $proposal->id }}', 'Delete Quotation?', 'Quotation {{ $proposal->proposal_number }} will be permanently removed.')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center">
            <p class="text-xs text-gray-400 mb-1">No quotations yet</p>
            <a href="{{ route('admin.sales-proposals.create') }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Create your first quotation →</a>
        </td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($proposals->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $proposals->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function convertProposal(id, number) {
  Swal.fire({
    title: 'Convert to Invoice?',
    text: 'Quotation ' + number + ' will be converted into a sales invoice.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#111827',
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
