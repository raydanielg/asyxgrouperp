@extends('layouts.admin')

@section('title', 'Sales Invoice - ' . config('app.name'))
@section('page_title', 'Invoice')

@section('content')
<style>
@media print {
  .no-print { display: none !important; }
  body { background: #fff !important; }
}
.doc { width: 210mm; max-width: 100%; margin: 0 auto; }
.doc-card { box-shadow: 0 1px 0 rgba(0,0,0,0.04); }
.doc-table th, .doc-table td { border-bottom: 1px solid #eef2f7; }
.ribbon {
  position: absolute; right: -40px; top: 18px; transform: rotate(45deg);
  background: #10b981; color: #fff; font-weight: 700; font-size: 10px; padding: 6px 50px;
  box-shadow: 0 1px 2px rgba(16,185,129,.3);
}
.ribbon.overdue { background: #ef4444; box-shadow: 0 1px 2px rgba(239,68,68,.3); }
.ribbon.draft { background: #9ca3af; box-shadow: 0 1px 2px rgba(156,163,175,.3); }
.ribbon.partial { background: #f59e0b; box-shadow: 0 1px 2px rgba(245,158,11,.3); }
.ribbon.posted { background: #0ea5e9; box-shadow: 0 1px 2px rgba(14,165,233,.3); }
</style>

@php
    $company = auth()->user()->company;
    $statusRibbon = [
        'paid' => 'ribbon',
        'overdue' => 'ribbon overdue',
        'draft' => 'ribbon draft',
        'partial' => 'ribbon partial',
        'posted' => 'ribbon posted',
    ][$salesInvoice->status] ?? 'ribbon';
@endphp

<div class="no-print mb-4 flex items-center justify-between">
  <div class="flex items-center gap-2">
    <a href="{{ route('admin.sales-invoices.index') }}" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">Back</a>
  </div>
  <div class="flex items-center gap-2">
    <button onclick="window.print()" class="px-3 py-1.5 text-xs rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Print / PDF</button>
    <a href="{{ route('admin.sales-invoices.edit', $salesInvoice) }}" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">Edit</a>
  </div>
  </div>

<div class="doc bg-white rounded-xl border doc-card relative">
  <div class="{{ $statusRibbon }}">{{ strtoupper($salesInvoice->status) }}</div>
  <!-- Header -->
  <div class="p-8 border-b flex items-start justify-between">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center">
        <span class="text-emerald-600 font-bold">{{ strtoupper(substr($company->name ?? 'C',0,1)) }}</span>
      </div>
      <div>
        <h1 class="text-lg font-extrabold text-gray-900">{{ $company->name ?? config('app.name') }}</h1>
        <p class="text-[11px] text-gray-500 leading-4 whitespace-pre-line">{{ $company->address ?? '' }}</p>
      </div>
    </div>
    <div class="text-right">
      <p class="text-xs text-gray-400">Invoice #</p>
      <p class="text-base font-bold text-gray-900">{{ $salesInvoice->invoice_number }}</p>
      <div class="mt-3 text-[11px] text-gray-600">
        <div><span class="text-gray-400">Invoice Date:</span> {{ $salesInvoice->invoice_date->format('d M Y') }}</div>
        <div><span class="text-gray-400">Due Date:</span> {{ $salesInvoice->due_date->format('d M Y') }}</div>
      </div>
    </div>
  </div>

  <!-- Parties -->
  <div class="p-8 grid grid-cols-2 gap-8">
    <div>
      <p class="text-xs font-semibold text-gray-500">Invoiced To</p>
      <div class="mt-2 text-sm text-gray-800">
        <p class="font-semibold">{{ $salesInvoice->customer?->name ?? 'Customer' }}</p>
        @if($salesInvoice->customer?->address)
          <p class="text-gray-500 text-xs whitespace-pre-line">{{ $salesInvoice->customer->address }}</p>
        @endif
      </div>
    </div>
    <div>
      <p class="text-xs font-semibold text-gray-500">From</p>
      <div class="mt-2 text-sm text-gray-800">
        <p class="font-semibold">{{ $company->name ?? config('app.name') }}</p>
        @if($company?->address)
          <p class="text-gray-500 text-xs whitespace-pre-line">{{ $company->address }}</p>
        @endif
      </div>
    </div>
  </div>

  <!-- Items -->
  <div class="px-8 pb-4">
    <table class="w-full text-sm doc-table">
      <thead>
        <tr class="bg-gray-50 text-xs text-gray-500">
          <th class="text-left px-4 py-2 font-semibold">Description</th>
          <th class="text-right px-4 py-2 font-semibold">Qty</th>
          <th class="text-right px-4 py-2 font-semibold">Unit Price</th>
          <th class="text-right px-4 py-2 font-semibold">Amount</th>
        </tr>
      </thead>
      <tbody>
        @forelse($salesInvoice->items as $item)
        <tr>
          <td class="px-4 py-2 text-gray-800">
            <div class="font-medium">{{ $item->product_name }}</div>
            @if(!empty($item->description))
              <div class="text-[11px] text-gray-500">{{ $item->description }}</div>
            @endif
          </td>
          <td class="px-4 py-2 text-right text-gray-600">{{ number_format($item->quantity, 2) }}</td>
          <td class="px-4 py-2 text-right text-gray-600">TZS {{ number_format($item->unit_price, 2) }}</td>
          <td class="px-4 py-2 text-right font-semibold text-gray-900">TZS {{ number_format($item->total_amount, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="px-4 py-4 text-center text-xs text-gray-400">No items</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Totals -->
  <div class="px-8 pb-8 grid grid-cols-2 gap-6">
    <div>
      @if($salesInvoice->notes)
      <div class="mt-2 p-3 rounded-lg bg-gray-50 border text-[12px] text-gray-600">
        <div class="font-semibold text-gray-700 mb-1">Notes</div>
        <div>{{ $salesInvoice->notes }}</div>
      </div>
      @endif
    </div>
    <div class="ml-auto w-full max-w-sm">
      <div class="flex justify-between text-sm py-1"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900">TZS {{ number_format($salesInvoice->subtotal, 2) }}</span></div>
      <div class="flex justify-between text-sm py-1"><span class="text-gray-500">Tax</span><span class="font-medium text-gray-900">TZS {{ number_format($salesInvoice->tax_amount, 2) }}</span></div>
      <div class="flex justify-between text-sm py-1"><span class="text-gray-500">Discount</span><span class="font-medium text-gray-900">TZS {{ number_format($salesInvoice->discount_amount, 2) }}</span></div>
      <div class="flex justify-between text-base font-bold py-2 border-t mt-2"><span class="text-gray-900">Total</span><span class="text-gray-900">TZS {{ number_format($salesInvoice->total_amount, 2) }}</span></div>
      <div class="flex justify-between text-sm py-1"><span class="text-gray-500">Paid</span><span class="font-medium text-emerald-600">TZS {{ number_format($salesInvoice->paid_amount, 2) }}</span></div>
      <div class="flex justify-between text-sm py-1"><span class="text-gray-500">Balance</span><span class="font-medium text-red-600">TZS {{ number_format($salesInvoice->balance_amount, 2) }}</span></div>
    </div>
  </div>

  <!-- Footer -->
  <div class="px-8 pb-8">
    <div class="h-px bg-gray-100 mb-3"></div>
    <div class="text-[10px] text-gray-400">This is a computer generated document. If you have any questions about this invoice, please contact us.</div>
  </div>
</div>
@endsection
