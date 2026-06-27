@extends('layouts.admin')
@section('title', 'POS Reports - ' . config('app.name'))
@section('page_title', 'POS Reports')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div class="bg-white rounded-xl border p-6">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Today's Sales</p>
        <p class="text-2xl font-bold text-emerald-700">TZS {{ number_format($todayTotal) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-6">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">This Month's Sales</p>
        <p class="text-2xl font-bold text-emerald-700">TZS {{ number_format($monthTotal) }}</p>
    </div>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Sales History</h3></div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Sale #</th><th class="px-5 py-3 font-medium">Items</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Payment</th><th class="px-5 py-3 font-medium">Cashier</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($sales as $s)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $s->sale_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $s->items->count() }} items</td>
            <td class="px-5 py-3 text-xs font-semibold text-emerald-700">TZS {{ number_format($s->total_amount) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ ucfirst($s->payment_method) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $s->cashier?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $s->created_at->format('d M Y H:i') }}</td>
            <td class="px-5 py-3 flex items-center gap-2"><a href="{{ route('admin.pos.show', $s) }}" class="text-sky-600 hover:text-sky-700 text-xs">View</a><form id="del-pos-{{ $s->id }}" method="POST" action="{{ route('admin.pos.destroy', $s) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-pos-{{ $s->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        </tr>@empty<tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No sales recorded</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $sales->links() }}</div>
</div>
@endsection
