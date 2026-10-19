@extends('layouts.admin')
@section('title', 'Vendor Invoice - ' . config('app.name'))
@section('page_title', 'Invoice: ' . $invoice->vendor_invoice_number)
@section('content')
@php $vd=['paid'=>'#10b981','partially_paid'=>'#f59e0b','unpaid'=>'#ef4444'][$invoice->status] ?? '#9ca3af'; @endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.vendor-invoices.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Vendor Invoices</a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.vendor-invoices.pdf', $invoice) }}" target="_blank" class="px-3 py-2 text-xs font-medium text-gray-600 border border-gray-200 rounded hover:bg-gray-50">View PDF</a>
        <a href="{{ route('admin.vendor-invoices.pdf', $invoice) }}?download=1" class="px-3 py-2 text-xs font-medium text-gray-600 border border-gray-200 rounded hover:bg-gray-50 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download PDF
        </a>
        @if($invoice->balance > 0)
        <button type="button" onclick="openPayDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Record Payment</button>
        @endif
    </div>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 p-5 mb-5 flex items-center justify-between flex-wrap gap-4">
    <div>
        <div class="flex items-center gap-3">
            <h3 class="font-bold text-gray-900 font-mono">{{ $invoice->vendor_invoice_number }}</h3>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $vd }}"></span>{{ ucfirst(str_replace('_',' ',$invoice->status)) }}</span>
        </div>
        <p class="text-xs text-gray-400 mt-1">{{ $invoice->supplier?->name ?? '—' }}@if($invoice->lpo) · LPO {{ $invoice->lpo->lpo_number }}@endif @if($invoice->supplier_invoice_ref)· Ref: {{ $invoice->supplier_invoice_ref }}@endif</p>
    </div>
    <div class="text-right">
        <p class="text-lg font-bold text-gray-900">{{ number_format($invoice->total) }} <span class="text-xs font-normal text-gray-400">TZS</span></p>
        <p class="text-[11px] {{ $invoice->balance > 0 ? 'text-red-500' : 'text-emerald-600' }}">{{ $invoice->balance > 0 ? 'Balance: ' . number_format($invoice->balance) . ' TZS' : 'Fully paid' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Details + payment progress --}}
    <div class="space-y-5">
        <div class="bg-white rounded-md border border-gray-200">
            <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Details</h4></div>
            <dl class="p-5 space-y-2.5">
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Invoice Date</dt><dd class="text-xs text-gray-800 text-right">{{ $invoice->invoice_date->format('d M Y') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Due Date</dt><dd class="text-xs text-gray-800 text-right">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Supplier</dt><dd class="text-xs text-gray-800 text-right max-w-[160px] truncate">{{ $invoice->supplier?->name ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">LPO</dt><dd class="text-xs text-gray-800 text-right">@if($invoice->lpo)<a href="{{ route('admin.lpos.show', $invoice->lpo) }}" class="font-mono hover:underline">{{ $invoice->lpo->lpo_number }}</a>@else—@endif</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Project</dt><dd class="text-xs text-gray-800 text-right max-w-[160px] truncate">{{ $invoice->project?->title ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Supplier Ref</dt><dd class="text-xs font-mono text-gray-800 text-right">{{ $invoice->supplier_invoice_ref ?? '—' }}</dd></div>
            </dl>
            <div class="px-5 py-3.5 border-t border-gray-100 space-y-2.5">
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Subtotal</dt><dd class="text-xs text-gray-800 text-right">{{ number_format($invoice->subtotal) }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-xs text-gray-400">Tax</dt><dd class="text-xs text-gray-800 text-right">+{{ number_format($invoice->tax_amount) }}</dd></div>
                <div class="flex justify-between gap-4 pt-2 border-t border-gray-100"><dt class="text-xs font-bold text-gray-900">Total</dt><dd class="text-sm font-bold text-gray-900 text-right">{{ number_format($invoice->total) }} <span class="text-gray-400 text-xs font-normal">TZS</span></dd></div>
            </div>
        </div>

        {{-- Payment progress --}}
        @php $pct = $invoice->total > 0 ? min(100, round($invoice->amount_paid / $invoice->total * 100)) : 0; @endphp
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500">Payment Progress</span>
                <span class="text-xs font-bold text-gray-900">{{ $pct }}%</span>
            </div>
            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden mb-3">
                <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width:{{ $pct }}%"></div>
            </div>
            <div class="flex justify-between items-center"><span class="text-xs text-gray-400">Paid</span><span class="text-xs font-medium text-emerald-600">{{ number_format($invoice->amount_paid) }} TZS</span></div>
            <div class="flex justify-between items-center mt-1.5"><span class="text-xs text-gray-400">Balance</span><span class="text-xs font-medium {{ $invoice->balance > 0 ? 'text-red-500' : 'text-gray-500' }}">{{ number_format($invoice->balance) }} TZS</span></div>
        </div>

        @if($invoice->notes)
        <div class="bg-white rounded-md border border-gray-200">
            <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Notes</h4></div>
            <p class="p-5 text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $invoice->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Payment history --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Payment History</h4>
                <span class="text-[11px] text-gray-400">{{ $invoice->payments->count() }} payment(s)</span>
            </div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="text-left border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Payment No.</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Amount</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Method</th>
                    <th class="px-4 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Reference</th>
                    <th class="px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Receipt</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($invoice->payments as $p)
                <tr>
                    <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $p->payment_number }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $p->payment_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-xs text-right font-medium text-emerald-600">+{{ number_format($p->amount) }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</td>
                    <td class="px-4 py-3 text-xs font-mono text-gray-400">{{ $p->reference_number ?? '—' }}</td>
                    <td class="px-5 py-3 text-right"><button type="button" onclick="openRcptDrawer({{ $p->id }})" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</button></td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-xs">No payments recorded yet</td></tr>
                @endforelse
                </tbody>
                @if($invoice->payments->count())
                <tfoot><tr class="border-t border-gray-200 bg-gray-50/50">
                    <td colspan="2" class="px-5 py-2.5 text-[11px] font-bold uppercase tracking-widest text-gray-500">Total paid</td>
                    <td class="px-4 py-2.5 text-xs text-right font-bold text-emerald-600">{{ number_format($invoice->amount_paid) }}</td>
                    <td colspan="3"></td>
                </tr></tfoot>
                @endif
            </table></div>
        </div>
    </div>
</div>

{{-- ═══ Record Payment Drawer (slides from left) ═══ --}}
<div id="payDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closePayDrawer()"></div>
    <div id="payDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">Record Payment</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $invoice->vendor_invoice_number }} · Balance: {{ number_format($invoice->balance) }} TZS</p>
            </div>
            <button type="button" onclick="closePayDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.vendor-payments.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <input type="hidden" name="vendor_invoice_id" value="{{ $invoice->id }}">
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Payment Date <span class="text-red-500">*</span></label>
                    <input name="payment_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Amount (TZS) <span class="text-red-500">*</span></label>
                    <input name="amount" type="number" step="0.01" required value="{{ $invoice->balance }}" max="{{ $invoice->balance }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    <p class="text-[10px] text-gray-400 mt-1">Max: {{ number_format($invoice->balance) }} TZS</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        @foreach(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','mobile_money'=>'Mobile Money'] as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Reference Number</label>
                    <input name="reference_number" placeholder="Transaction / cheque number" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                    <textarea name="notes" rows="2" placeholder="Payment notes..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Record Payment</button>
                <button type="button" onclick="closePayDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ Payment Receipt Drawer (slides from left) ═══ --}}
@php
$receiptMap = $invoice->payments->mapWithKeys(function ($p) use ($invoice) {
    return [$p->id => [
        'num' => $p->payment_number,
        'date' => $p->payment_date->format('d M Y'),
        'amount' => number_format($p->amount) . ' TZS',
        'method' => ucfirst(str_replace('_',' ',$p->payment_method)),
        'ref' => $p->reference_number ?? '—',
        'supplier' => $invoice->supplier?->name ?? '—',
        'invoice' => $invoice->vendor_invoice_number,
        'recordedBy' => $p->createdBy?->name ?? '—',
        'notes' => $p->notes ?? '—',
        'status' => ucfirst($p->status),
    ]];
})->toArray();
@endphp
<div id="rcptDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeRcptDrawer()"></div>
    <div id="rcptDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">Payment Receipt</h3>
                <p class="text-[11px] text-slate-400 mt-0.5" id="rcpt-num">—</p>
            </div>
            <button type="button" onclick="closeRcptDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5">
            {{-- Receipt --}}
            <div class="border border-gray-200 rounded-md">
                <div class="px-5 py-4 border-b border-gray-200 text-center">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Payment Receipt</p>
                    <p class="text-xl font-bold text-gray-900 mt-1" id="rcpt-amount">—</p>
                    <p class="text-[11px] text-emerald-600 font-medium mt-0.5" id="rcpt-status">—</p>
                </div>
                <dl class="p-5 space-y-3">
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Receipt No.</dt><dd class="text-xs font-mono text-gray-800" id="rcpt-rnum">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Date</dt><dd class="text-xs text-gray-800" id="rcpt-date">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Invoice</dt><dd class="text-xs font-mono text-gray-800" id="rcpt-invoice">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Supplier</dt><dd class="text-xs text-gray-800 text-right" id="rcpt-supplier">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Method</dt><dd class="text-xs text-gray-800" id="rcpt-method">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Reference</dt><dd class="text-xs font-mono text-gray-800 text-right" id="rcpt-ref">—</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Recorded by</dt><dd class="text-xs text-gray-800" id="rcpt-by">—</dd></div>
                </dl>
                <div class="px-5 py-3 border-t border-dashed border-gray-200">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Notes</p>
                    <p class="text-xs text-gray-600" id="rcpt-notes">—</p>
                </div>
            </div>
            <p class="text-[10px] text-gray-400 text-center mt-4">Generated by {{ auth()->user()->company?->legal_name ?? 'ASYX ERP' }}</p>
        </div>
    </div>
</div>

<script>
const receipts = @json($receiptMap);

function openPayDrawer() {
    const d = document.getElementById('payDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('payDrawerPanel').classList.remove('-translate-x-full'));
}
function closePayDrawer() {
    const p = document.getElementById('payDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('payDrawer').classList.add('hidden'), 300);
}

function openRcptDrawer(id) {
    const p = receipts[id];
    if (!p) return;
    document.getElementById('rcpt-num').textContent = p.num;
    document.getElementById('rcpt-amount').textContent = p.amount;
    document.getElementById('rcpt-status').textContent = p.status;
    document.getElementById('rcpt-rnum').textContent = p.num;
    document.getElementById('rcpt-date').textContent = p.date;
    document.getElementById('rcpt-invoice').textContent = p.invoice;
    document.getElementById('rcpt-supplier').textContent = p.supplier;
    document.getElementById('rcpt-method').textContent = p.method;
    document.getElementById('rcpt-ref').textContent = p.ref;
    document.getElementById('rcpt-by').textContent = p.recordedBy;
    document.getElementById('rcpt-notes').textContent = p.notes;
    const d = document.getElementById('rcptDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('rcptDrawerPanel').classList.remove('-translate-x-full'));
}
function closeRcptDrawer() {
    const p = document.getElementById('rcptDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('rcptDrawer').classList.add('hidden'), 300);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closePayDrawer(); closeRcptDrawer(); }
});
</script>
@endsection
