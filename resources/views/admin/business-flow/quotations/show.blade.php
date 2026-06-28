@extends('layouts.admin')
@section('title', 'Quotation - ' . config('app.name'))
@section('page_title', 'Quotation: ' . $quotation->quotation_number)
@section('content')
<div class="mb-4 flex items-center justify-between">
  <a href="{{ route('admin.quotations.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Quotations</a>
  <button onclick="downloadPdf('{{ route('admin.quotations.pdf', $quotation) }}', 'Quotation {{ $quotation->quotation_number }}')" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-1.5">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Download PDF
  </button>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Quotation Details</h3>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-gray-400">Quotation No.</span><span class="font-mono text-gray-700">{{ $quotation->quotation_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Client</span><span class="text-gray-700">{{ $quotation->client_name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Email</span><span class="text-gray-700">{{ $quotation->client_email ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Lead</span><span class="text-gray-700">{{ $quotation->lead?->full_name ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Date</span><span class="text-gray-700">{{ $quotation->quotation_date->format('d M Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Valid Until</span><span class="text-gray-700">{{ $quotation->valid_until?->format('d M Y') ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Subtotal</span><span class="text-gray-700">TZS {{ number_format($quotation->subtotal) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Discount</span><span class="text-red-600">TZS {{ number_format($quotation->discount_amount) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Tax</span><span class="text-gray-700">TZS {{ number_format($quotation->tax_amount) }}</span></div>
            <div class="flex justify-between border-t pt-2"><span class="font-semibold">Total</span><span class="font-bold text-emerald-700">TZS {{ number_format($quotation->total) }}</span></div>
        </div>
        <div class="mt-4">
            <form method="POST" action="{{ route('admin.quotations.status', $quotation) }}">@csrf @method('PATCH')
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs mb-2 outline-none">
        @foreach(['draft'=>'Draft','sent'=>'Sent','accepted'=>'Accepted','rejected'=>'Rejected','expired'=>'Expired'] as $k=>$v)
        <option value="{{ $k }}" @selected($quotation->status===$k)>{{ $v }}</option>
        @endforeach
        </select>
                <button type="submit" class="w-full px-3 py-2 bg-sky-600 text-white text-xs font-medium rounded-lg hover:bg-sky-700">Update Status</button>
            </form>
        </div>
    </div>
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Items</h3>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">Description</th><th class="py-2">Qty</th><th class="py-2">Unit Price</th><th class="py-2">Discount</th><th class="py-2">Tax %</th><th class="py-2">Total</th></tr></thead>
                <tbody>
        @foreach($quotation->items as $item)
        <tr class="border-t border-gray-100">
                    <td class="py-2 text-gray-700">{{ $item->description }}</td>
                    <td class="py-2 text-gray-500">{{ $item->quantity }} {{ $item->unit ?? '' }}</td>
                    <td class="py-2 text-gray-500">TZS {{ number_format($item->unit_price) }}</td>
                    <td class="py-2 text-red-500">TZS {{ number_format($item->discount_amount) }}</td>
                    <td class="py-2 text-gray-500">{{ $item->tax_percentage }}%</td>
                    <td class="py-2 font-semibold text-gray-900">TZS {{ number_format($item->line_total) }}</td>
                </tr>
        @endforeach
        </tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function downloadPdf(url, title) {
  Swal.fire({
    title: 'Downloading...',
    text: 'Preparing ' + title,
    allowOutsideClick: false,
    didOpen: () => { Swal.showLoading(); },
    timer: 800,
    timerProgressBar: false,
    willClose: () => { window.open(url, '_blank'); }
  });
}
</script>
@endpush
