@extends('layouts.admin')
@section('title', 'Vendor Payments - ' . config('app.name'))
@section('page_title', 'Vendor Payments')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track all vendor payments — Total Paid: <span class="font-semibold text-emerald-700">TZS {{ number_format($totalPaid ?? 0) }}</span></p>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-3 font-medium">Payment No.</th>
                    <th class="px-5 py-3 font-medium">Supplier</th>
                    <th class="px-5 py-3 font-medium">Invoice No.</th>
                    <th class="px-5 py-3 font-medium text-right">Amount</th>
                    <th class="px-5 py-3 font-medium">Method</th>
                    <th class="px-5 py-3 font-medium">Date</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                    <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $p->payment_number }}</td>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $p->supplier?->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-xs font-mono text-gray-600">{{ $p->vendorInvoice?->vendor_invoice_number ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TZS {{ number_format($p->amount) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-600">{{ ucfirst(str_replace('_', ' ', $p->payment_method)) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $p->payment_date?->format('d M Y') ?? '—' }}</td>
                    <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $p->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($p->status) }}</span></td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $p->createdBy?->name ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No vendor payments found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $payments->links() }}</div>
</div>
@endsection
