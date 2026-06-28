@extends('layouts.admin')

@section('title', 'Transfers - ' . config('app.name'))
@section('page_title', 'Stock Transfers')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage stock transfers between warehouses</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Transfer
    </button>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Product</th>
                <th class="px-5 py-3 font-medium">From</th>
                <th class="px-5 py-3 font-medium">To</th>
                <th class="px-5 py-3 font-medium">Qty</th>
                <th class="px-5 py-3 font-medium">Date</th>
                <th class="px-5 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($transfers as $transfer)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $transfer->product_name ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $transfer->fromWarehouse?->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $transfer->toWarehouse?->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-xs font-semibold text-gray-900">{{ number_format($transfer->quantity) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $transfer->date ? $transfer->date->format('d M Y') : 'N/A' }}</td>
                    <td class="px-5 py-3">
                        <form method="POST" action="{{ route('admin.transfers.destroy', $transfer) }}" class="inline" onsubmit="return confirm('Delete this transfer?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No transfers found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $transfers->links() }}</div>
</div>

<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Transfer</h3>
        <form method="POST" action="{{ route('admin.transfers.store') }}" class="space-y-3">
            @csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Product Name *</label><input name="product_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">From *</label><select name="from_warehouse" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>@foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">To *</label><select name="to_warehouse" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>@foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach</select></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Quantity *</label><input name="quantity" type="number" step="0.01" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Date</label><input name="date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
