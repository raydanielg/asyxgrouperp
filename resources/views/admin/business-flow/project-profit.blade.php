@extends('layouts.admin')
@section('title', 'Project Profit - ' . config('app.name'))
@section('page_title', 'Profit Analysis: ' . $project->title)
@section('content')
<div class="mb-4"><a href="{{ route('admin.projects.show', $project) }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Project</a></div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] text-gray-400 uppercase">Total Revenue</p>
        <p class="text-lg font-bold text-emerald-700">TZS {{ number_format($totalRevenue) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] text-gray-400 uppercase">Procurement Cost</p>
        <p class="text-lg font-bold text-amber-700">TZS {{ number_format($totalProcurement) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] text-gray-400 uppercase">Office Expenses</p>
        <p class="text-lg font-bold text-red-700">TZS {{ number_format($totalOfficeExp) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] text-gray-400 uppercase">Net Profit</p>
        <p class="text-lg font-bold @if($profit>=0)text-emerald-700@else text-red-700@endif">TZS {{ number_format($profit) }}</p>
        <p class="text-[10px] text-gray-400">Margin: {{ number_format($margin, 1) }}%</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- Budget vs Actual --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Budget vs Actual</h3>
        <div class="space-y-3 text-xs">
            <div class="flex justify-between items-center"><span class="text-gray-500">Planned Budget</span><span class="font-semibold text-gray-900">TZS {{ number_format($project->budget) }}</span></div>
            <div class="flex justify-between items-center"><span class="text-gray-500">Total Cost (Actual)</span><span class="font-semibold text-red-600">TZS {{ number_format($totalCost) }}</span></div>
            <div class="flex justify-between items-center border-t pt-2"><span class="text-gray-500">Budget Variance</span>@php $variance = $project->budget - $totalCost; @endphp<span class="font-bold @if($variance>=0)text-emerald-600@else text-red-600@endif">TZS {{ number_format($variance) }}</span></div>
        </div>
        @if($project->budgets->isNotEmpty())
        <div class="mt-4 pt-4 border-t">
            <p class="text-[10px] text-gray-400 uppercase mb-2">Budget History</p>
        @foreach($project->budgets as $b)
        <div class="flex justify-between text-xs py-1"><span class="text-gray-500">{{ $b->budget_number }}</span><span class="text-gray-700">TZS {{ number_format($b->total_budget) }} <span class="inline-flex px-1.5 py-0.5 rounded-full text-[9px] @if($b->status==='approved')bg-emerald-50 text-emerald-700@elseif($b->status==='rejected')bg-red-50 text-red-700@else bg-amber-50 text-amber-700@endif">{{ ucfirst($b->status) }}</span></span></div>
        @endforeach
        </div>
        @endif
    </div>

    {{-- Client Receipts --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Client Receipts ({{ $project->clientReceipts->count() }})</h3>
        <div class="overflow-x-auto"><table class="w-full text-xs">
            <thead><tr class="text-left text-gray-500"><th class="py-2">Receipt No.</th><th class="py-2">Client</th><th class="py-2">Amount</th><th class="py-2">Date</th></tr></thead>
            <tbody>
        @forelse($project->clientReceipts as $r)
        <tr class="border-t border-gray-100"><td class="py-2 font-mono text-gray-700">{{ $r->receipt_number }}</td><td class="py-2 text-gray-500">{{ $r->client_name }}</td><td class="py-2 font-semibold text-emerald-600">TZS {{ number_format($r->amount) }}</td><td class="py-2 text-gray-400">{{ $r->receipt_date->format('d M Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="py-4 text-center text-gray-400">No receipts</td></tr>
        @endforelse
        </tbody>
        </table></div>
    </div>

    {{-- Vendor Invoices --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Vendor Invoices ({{ $project->vendorInvoices->count() }})</h3>
        <div class="overflow-x-auto"><table class="w-full text-xs">
            <thead><tr class="text-left text-gray-500"><th class="py-2">Invoice No.</th><th class="py-2">Supplier</th><th class="py-2">Total</th><th class="py-2">Paid</th></tr></thead>
            <tbody>
        @forelse($project->vendorInvoices as $v)
        <tr class="border-t border-gray-100"><td class="py-2 font-mono text-gray-700">{{ $v->vendor_invoice_number }}</td><td class="py-2 text-gray-500">{{ $v->supplier?->name ?? 'N/A' }}</td><td class="py-2 text-gray-700">TZS {{ number_format($v->total) }}</td><td class="py-2 text-emerald-600">TZS {{ number_format($v->amount_paid) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="py-4 text-center text-gray-400">No invoices</td></tr>
        @endforelse
        </tbody>
        </table></div>
    </div>

    {{-- Office Expenses --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Office Expenses ({{ $project->officeExpenses->count() }})</h3>
        <div class="overflow-x-auto"><table class="w-full text-xs">
            <thead><tr class="text-left text-gray-500"><th class="py-2">Expense No.</th><th class="py-2">Description</th><th class="py-2">Amount</th><th class="py-2">Status</th></tr></thead>
            <tbody>
        @forelse($project->officeExpenses as $e)
        <tr class="border-t border-gray-100"><td class="py-2 font-mono text-gray-700">{{ $e->expense_number }}</td><td class="py-2 text-gray-500">{{ $e->description }}</td><td class="py-2 text-gray-700">TZS {{ number_format($e->amount) }}</td><td class="py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] @if($e->status==='approved')bg-emerald-50 text-emerald-700@elseif($e->status==='rejected')bg-red-50 text-red-700@else bg-amber-50 text-amber-700@endif">{{ ucfirst($e->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="4" class="py-4 text-center text-gray-400">No expenses</td></tr>
        @endforelse
        </tbody>
        </table></div>
    </div>
</div>
@endsection
