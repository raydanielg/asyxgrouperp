@extends('layouts.admin')
@section('title', 'Vendor Payments - ' . config('app.name'))
@section('page_title', 'Vendor Payments')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <p class="text-xs text-gray-400">Track all vendor payments · Total paid: <span class="font-semibold text-gray-900">{{ number_format($totalPaid ?? 0) }} TZS</span></p>
    <form method="GET" class="flex items-center gap-2 flex-wrap">
        <input name="search" value="{{ request('search') }}" placeholder="Search payment, ref, supplier..." class="px-3 py-2 text-xs border border-gray-200 rounded outline-none focus:border-gray-400 w-52">
        <select name="method" class="px-3 py-2 text-xs border border-gray-200 rounded outline-none focus:border-gray-400">
            <option value="">All methods</option>
            @foreach(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','mobile_money'=>'Mobile Money'] as $k=>$v)
            <option value="{{ $k }}" @selected(request('method')===$k)>{{ $v }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 text-xs border border-gray-200 rounded outline-none focus:border-gray-400">
            <option value="">All statuses</option>
            @foreach(['completed'=>'Completed','pending'=>'Pending','failed'=>'Failed'] as $k=>$v)
            <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
            @endforeach
        </select>
        <button class="px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Filter</button>
        @if(request()->hasAny(['search','method','status']))
        <a href="{{ route('admin.vendor-payments.index') }}" class="text-xs text-gray-400 hover:text-gray-700">Clear</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-200 bg-gray-50/50">
                    <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Payment No.</th>
                    <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Supplier</th>
                    <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                    <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Amount</th>
                    <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Method</th>
                    <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Recorded By</th>
                    <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $p)
                <tr class="hover:bg-gray-50/60">
                    <td class="px-5 py-3.5 text-xs font-mono text-gray-700">{{ $p->payment_number }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-700">{{ $p->supplier?->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-xs font-mono text-gray-500">
                        @if($p->vendorInvoice)
                        <a href="{{ route('admin.vendor-invoices.show', $p->vendorInvoice) }}" class="hover:underline">{{ $p->vendorInvoice->vendor_invoice_number }}</a>
                        @else — @endif
                    </td>
                    <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-900">{{ number_format($p->amount) }} <span class="text-gray-400">TZS</span></td>
                    <td class="px-5 py-3.5 text-xs text-gray-600">{{ ucfirst(str_replace('_', ' ', $p->payment_method)) }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-400">{{ $p->payment_date?->format('d M Y') ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        @php $pd=['completed'=>'#10b981','pending'=>'#f59e0b','failed'=>'#ef4444'][$p->status] ?? '#9ca3af'; @endphp
                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $pd }}"></span>{{ ucfirst($p->status) }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">{{ $p->createdBy?->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-right">
                        @if($p->vendorInvoice)
                        <a href="{{ route('admin.vendor-invoices.show', $p->vendorInvoice) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Invoice →</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-5 py-10 text-center text-gray-400 text-xs">No vendor payments found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
