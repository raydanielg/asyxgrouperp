@extends('layouts.admin')
@section('title', 'CRM Deals - ' . config('app.name'))
@section('page_title', 'Deals')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track deals through your sales pipeline</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Deal
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Deal #</th><th class="px-5 py-3 font-medium">Title</th><th class="px-5 py-3 font-medium">Lead</th><th class="px-5 py-3 font-medium">Value</th><th class="px-5 py-3 font-medium">Stage</th><th class="px-5 py-3 font-medium">Expected Close</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($deals as $d)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $d->deal_number }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $d->title }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $d->lead?->full_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($d->value) }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700">{{ ucfirst(str_replace('_', ' ', $d->stage)) }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $d->expected_close_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3">@php $c=['open'=>'emerald','won'=>'emerald','lost'=>'red','cancelled'=>'gray']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$d->status] ?? 'gray' }}-50 text-{{ $c[$d->status] ?? 'gray' }}-700">{{ ucfirst($d->status) }}</span></td>
            <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                    @if($d->status==='open' && !$d->project_id)
                    <button onclick="convertDealToProject({{ $d->id }}, '{{ $d->deal_number }}', '{{ $d->title }}')" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all" title="Convert to Project">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </button>
                    <form id="convert-deal-{{ $d->id }}" method="POST" action="{{ route('admin.crm-deals.convert-to-project', $d) }}" class="hidden">@csrf</form>
                    @endif
                    <button onclick="downloadPdf('{{ route('admin.crm-deals.pdf', $d) }}', '{{ $d->deal_number }}')" class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 flex items-center justify-center transition-all" title="Download PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </button>
                    <form id="del-deal-{{ $d->id }}" method="POST" action="{{ route('admin.crm-deals.destroy', $d) }}" class="hidden">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-deal-{{ $d->id }}', 'Delete Deal?', 'Deal {{ $d->deal_number }} will be permanently removed.')" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No deals found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $deals->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Deal</h3>
        <form method="POST" action="{{ route('admin.crm-deals.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Lead</label><select name="lead_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">None</option>
        @foreach($leads as $l)
        <option value="{{ $l->id }}">{{ $l->full_name }} ({{ $l->company ?? 'N/A' }})</option>
        @endforeach
        </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Value *</label><input name="value" type="number" step="0.01" required value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Stage</label><select name="stage" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="prospecting">Prospecting</option><option value="qualification">Qualification</option><option value="negotiation">Negotiation</option><option value="proposal">Proposal</option><option value="closed_won">Closed Won</option><option value="closed_lost">Closed Lost</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Expected Close Date</label><input name="expected_close_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label><select name="assigned_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Unassigned</option>
        @foreach($users as $u)
        <option value="{{ $u->id }}">{{ $u->name }}</option>
        @endforeach
        </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function downloadPdf(url, title) {
  Swal.fire({
    title: 'Downloading...',
    text: 'Preparing ' + title,
    allowOutsideClick: false,
    didOpen: () => { Swal.showLoading(); },
    timer: 800,
    willClose: () => { window.open(url, '_blank'); }
  });
}

function convertDealToProject(id, number, title) {
  Swal.fire({
    title: 'Convert Deal to Project?',
    text: 'Deal ' + number + ' (' + title + ') will be converted into a project.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#059669',
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
      document.getElementById('convert-deal-' + id).submit();
    }
  });
}
</script>
@endpush
@endsection
