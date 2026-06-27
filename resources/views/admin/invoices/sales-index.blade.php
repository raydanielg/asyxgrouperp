@extends('layouts.admin')

@section('title', 'Sales Invoices - ' . config('app.name'))
@section('page_title', 'Sales Invoices')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage sales invoices for customers</p>
    <a href="{{ route('admin.sales-invoices.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Invoice
    </a>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Invoice #</th>
                <th class="px-5 py-3 font-medium">Customer</th>
                <th class="px-5 py-3 font-medium">Total</th>
                <th class="px-5 py-3 font-medium">Balance</th>
                <th class="px-5 py-3 font-medium">Type</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Date</th>
                <th class="px-5 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 text-xs font-mono text-gray-700"><a href="{{ route('admin.sales-invoices.show', $invoice) }}" class="hover:text-emerald-600">{{ $invoice->invoice_number }}</a></td>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $invoice->customer?->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-xs font-semibold text-gray-900">${{ number_format($invoice->total_amount, 2) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">${{ number_format($invoice->balance_amount, 2) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ ucfirst($invoice->type) }}</td>
                    <td class="px-5 py-3">
                        @php $colors = ['draft'=>'gray','posted'=>'sky','partial'=>'amber','paid'=>'emerald','overdue'=>'red']; @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $colors[$invoice->status] ?? 'gray' }}-50 text-{{ $colors[$invoice->status] ?? 'gray' }}-700 border border-{{ $colors[$invoice->status] ?? 'gray' }}-100">{{ ucfirst($invoice->status) }}</span>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $invoice->invoice_date->format('d M Y') }}</td>
                    <td class="px-5 py-3 flex items-center gap-2">
                        <a href="{{ route('admin.sales-invoices.edit', $invoice) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">Edit</a>
                        @if($invoice->status === 'draft')
                        <form method="POST" action="{{ route('admin.sales-invoices.post', $invoice) }}" class="inline">@csrf<button class="text-sky-600 hover:text-sky-700 text-xs">Post</button></form>
                        @endif
                        <form method="POST" action="{{ route('admin.sales-invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs">Delete</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No invoices found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $invoices->links() }}</div>
</div>

@endsection
