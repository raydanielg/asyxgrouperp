@extends('layouts.admin')

@section('title', 'Transfers - ' . config('app.name'))
@section('page_title', 'Stock Transfers')

@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Manage stock transfers between warehouses</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Transfer</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Product</th><th class="px-5 py-3 font-semibold">From</th><th class="px-5 py-3 font-semibold">To</th><th class="px-5 py-3 font-semibold text-right">Qty</th><th class="px-5 py-3 font-semibold">Date</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($transfers as $transfer)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $transfer->product_name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $transfer->fromWarehouse?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $transfer->toWarehouse?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold text-gray-900">{{ number_format($transfer->quantity) }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $transfer->date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ ($transfer->status ?? 'pending') === 'completed' ? '#10b981' : '#f59e0b' }}"></span>{{ $transfer->status ?? 'pending' }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    @if(($transfer->status ?? 'pending') !== 'completed')
                    <form method="POST" action="{{ route('admin.transfers.approve', $transfer) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Approve this transfer?'})">
                        @csrf @method('PATCH')
                        <button class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Approve</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('admin.transfers.destroy', $transfer) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete this transfer?'})">
                        @csrf @method('DELETE')
                        <button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No transfers found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($transfers->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $transfers->links() }}</div>
    @endif
</div>

{{-- New Transfer Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Transfer</h3><p class="text-[11px] text-slate-400 mt-0.5">Move stock between warehouses</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.transfers.store') }}" class="flex-1 overflow-y-auto p-5" onsubmit="return validateTransfer()">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Product Name <span class="text-red-500">*</span></label><input name="product_name" required placeholder="e.g. Laptop - Dell 5530" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">From <span class="text-red-500">*</span></label>
                        <select name="from_warehouse" id="from_warehouse" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            <option value="">Select...</option>
                            @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">To <span class="text-red-500">*</span></label>
                        <select name="to_warehouse" id="to_warehouse" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            <option value="">Select...</option>
                            @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Quantity <span class="text-red-500">*</span></label><input name="quantity" type="number" step="0.01" min="0.01" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Date</label><input name="date" type="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <p id="sameWarehouseMsg" class="hidden text-[11px] text-red-500">From and To warehouses cannot be the same.</p>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Transfer</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer() {
    document.getElementById('createDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('createDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDrawer() {
    document.getElementById('createDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('createDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

function validateTransfer() {
    const from = document.getElementById('from_warehouse').value;
    const to = document.getElementById('to_warehouse').value;
    const msg = document.getElementById('sameWarehouseMsg');
    if (from && to && from === to) {
        msg.classList.remove('hidden');
        return false;
    }
    msg.classList.add('hidden');
    return true;
}
</script>
@endpush
