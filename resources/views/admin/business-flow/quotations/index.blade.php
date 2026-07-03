@extends('layouts.admin')
@section('title', 'Quotations - ' . config('app.name'))
@section('page_title', 'Quotations / Proposals')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Create quotations before converting leads to deals</p>
    <button onclick="document.getElementById('quoModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Quotation
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Quotation No.</th><th class="px-5 py-3 font-medium">Client</th><th class="px-5 py-3 font-medium">Lead</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($quotations as $q)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $q->quotation_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $q->client_name }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $q->lead?->full_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $q->quotation_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($q->total) }}</td>
            <td class="px-5 py-3">@php $sc=['draft'=>'gray','sent'=>'sky','accepted'=>'emerald','rejected'=>'red','expired'=>'red']; $c=$sc[$q->status]??'gray'; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-{{ $c }}-50 text-{{ $c }}-700">{{ ucfirst($q->status) }}</span></td>
            <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.quotations.show', $q) }}" class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 flex items-center justify-center transition-all" title="View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <button onclick="downloadPdf('{{ route('admin.quotations.pdf', $q) }}', '{{ $q->quotation_number }}')" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all" title="Download PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </button>
                    <form id="del-quo-{{ $q->id }}" method="POST" action="{{ route('admin.quotations.destroy', $q) }}" class="hidden">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-quo-{{ $q->id }}', 'Delete Quotation?', 'Quotation {{ $q->quotation_number }} will be permanently removed.')" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No quotations found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $quotations->links() }}</div>
</div>

<div id="quoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Create Quotation</h3><button onclick="document.getElementById('quoModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button></div>
        <form method="POST" action="{{ route('admin.quotations.store') }}" class="p-6 space-y-4">@csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Lead</label><select name="lead_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">No Lead</option>
        @foreach($leads as $l)
        <option value="{{ $l->id }}">{{ $l->full_name }} ({{ $l->company ?? 'N/A' }})</option>
        @endforeach
        </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Client Name *</label><input name="client_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Client Email</label><input name="client_email" type="email" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Quotation Date *</label><input name="quotation_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Valid Until</label><input name="valid_until" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Terms</label><textarea name="terms" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Items *</label><div id="quoItems" class="space-y-2"></div><button type="button" onclick="addQuoItem()" class="text-xs text-emerald-600 hover:text-emerald-700 mt-2">+ Add Item</button></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('quoModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Quotation</button></div>
        </form>
    </div>
</div>
<script>
let quoItemIdx = 0;
function addQuoItem() {
    const container = document.getElementById('quoItems');
    const div = document.createElement('div');
    div.className = 'flex flex-wrap items-center gap-2 border rounded-lg p-2';
    div.innerHTML = '<input name="items['+quoItemIdx+'][description]" placeholder="Description" required class="flex-1 min-w-[150px] px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+quoItemIdx+'][quantity]" type="number" step="0.01" placeholder="Qty" required class="w-20 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+quoItemIdx+'][unit]" placeholder="Unit" class="w-20 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+quoItemIdx+'][unit_price]" type="number" step="0.01" placeholder="Unit Price" required class="w-28 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+quoItemIdx+'][discount_amount]" type="number" step="0.01" placeholder="Discount" value="0" class="w-24 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+quoItemIdx+'][tax_percentage]" type="number" step="0.01" placeholder="Tax %" value="0" class="w-20 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 text-xs">&times;</button>';
    container.appendChild(div);
    quoItemIdx++;
}
addQuoItem();
</script>
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

function convertQuotation(id, number) {
  Swal.fire({
    title: 'Convert to Invoice?',
    text: 'Quotation ' + number + ' will be converted into a sales invoice.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#7c3aed',
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
      document.getElementById('convert-quo-' + id).submit();
    }
  });
}
</script>
@endpush
@endsection
