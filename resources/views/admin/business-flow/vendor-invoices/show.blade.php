@extends('layouts.admin')
@section('title', 'Vendor Invoice - ' . config('app.name'))
@section('page_title', 'Invoice: ' . $invoice->vendor_invoice_number)
@section('content')
<div class="mb-4"><a href="{{ route('admin.vendor-invoices.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Vendor Invoices</a></div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Invoice Details</h3>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-gray-400">Invoice No.</span><span class="font-mono text-gray-700">{{ $invoice->vendor_invoice_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Supplier Ref</span><span class="text-gray-700">{{ $invoice->supplier_invoice_ref ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Supplier</span><span class="text-gray-700">{{ $invoice->supplier?->name ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">LPO</span><span class="text-gray-700">{{ $invoice->lpo?->lpo_number ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Project</span><span class="text-gray-700">{{ $invoice->project?->title ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Invoice Date</span><span class="text-gray-700">{{ $invoice->invoice_date->format('d M Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Due Date</span><span class="text-gray-700">{{ $invoice->due_date?->format('d M Y') ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Subtotal</span><span class="text-gray-700">TZS {{ number_format($invoice->subtotal) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Tax</span><span class="text-gray-700">TZS {{ number_format($invoice->tax_amount) }}</span></div>
            <div class="flex justify-between border-t pt-2"><span class="font-semibold">Total</span><span class="font-bold text-emerald-700">TZS {{ number_format($invoice->total) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Paid</span><span class="text-emerald-600">TZS {{ number_format($invoice->amount_paid) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Balance</span><span class="text-red-600 font-semibold">TZS {{ number_format($invoice->balance) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] @if($invoice->status==='paid')bg-emerald-50 text-emerald-700@elseif($invoice->status==='partially_paid')bg-amber-50 text-amber-700@else bg-red-50 text-red-700@endif">{{ ucfirst(str_replace('_',' ',$invoice->status)) }}</span></div>
        </div>
        @if($invoice->balance > 0)
        <div class="mt-4 pt-4 border-t">
            <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="w-full px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700">Record Payment</button>
        </div>
        @endif
    </div>
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Payment History</h3>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">Payment No.</th><th class="py-2">Date</th><th class="py-2">Amount</th><th class="py-2">Method</th><th class="py-2">Reference</th></tr></thead>
                <tbody>
        @forelse($invoice->payments as $p)
        <tr class="border-t border-gray-100">
                    <td class="py-2 font-mono text-gray-700">{{ $p->payment_number }}</td>
                    <td class="py-2 text-gray-500">{{ $p->payment_date->format('d M Y') }}</td>
                    <td class="py-2 font-semibold text-emerald-600">TZS {{ number_format($p->amount) }}</td>
                    <td class="py-2 text-gray-500">{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</td>
                    <td class="py-2 text-gray-400">{{ $p->reference_number ?? '—' }}</td>
                
        </tr>
        @empty
        <tr><td colspan="5" class="py-4 text-center text-gray-400">No payments recorded</td></tr>
        @endforelse
        </tbody>
            </table></div>
        </div>
    </div>
</div>

<div id="paymentModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Record Payment</h3><button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button></div>
        <form method="POST" action="{{ route('admin.vendor-payments.store') }}" class="p-6 space-y-4">@csrf
            <input type="hidden" name="vendor_invoice_id" value="{{ $invoice->id }}">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Date *</label><input name="payment_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" required value="{{ $invoice->balance }}" max="{{ $invoice->balance }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label><select name="payment_method" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
        @foreach(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','mobile_money'=>'Mobile Money'] as $k=>$v)
        <option value="{{ $k }}">{{ $v }}</option>
        @endforeach
        </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Reference Number</label><input name="reference_number" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Record Payment</button></div>
        </form>
    </div>
</div>
@endsection
