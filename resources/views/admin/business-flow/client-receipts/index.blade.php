@extends('layouts.admin')
@section('title', 'Client Receipts - ' . config('app.name'))
@section('page_title', 'Client Payment Receipts')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Record payments received from clients</p>
    <button onclick="document.getElementById('receiptModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Receipt
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Receipt No.</th><th class="px-5 py-3 font-medium">Client</th><th class="px-5 py-3 font-medium">Project</th><th class="px-5 py-3 font-medium">Amount</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Method</th><th class="px-5 py-3 font-medium">Reference</th><th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>@forelse($receipts as $r)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $r->receipt_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $r->client_name }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $r->project?->title ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-emerald-700">TZS {{ number_format($r->amount) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $r->receipt_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ ucfirst(str_replace('_',' ',$r->payment_method)) }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $r->reference_number ?? '—' }}</td>
            <td class="px-5 py-3"><form id="del-cr-{{ $r->id }}" method="POST" action="{{ route('admin.client-receipts.destroy', $r) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-cr-{{ $r->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        </tr>@empty<tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No client receipts found</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $receipts->links() }}</div>
</div>

<div id="receiptModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-xl w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Add Client Receipt</h3><button onclick="document.getElementById('receiptModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button></div>
        <form method="POST" action="{{ route('admin.client-receipts.store') }}" class="p-6 space-y-4">@csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Client Name *</label><input name="client_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label><select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">No Project</option>@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount (TZS) *</label><input name="amount" type="number" required min="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Receipt Date *</label><input name="receipt_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label><select name="payment_method" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">@foreach(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','mobile_money'=>'Mobile Money'] as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Reference Number</label><input name="reference_number" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Invoice Reference</label><input name="invoice_reference" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('receiptModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Receipt</button></div>
        </form>
    </div>
</div>
@endsection
