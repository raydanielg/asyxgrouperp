@extends('layouts.admin')
@section('title', 'Bills - ' . config('app.name'))
@section('page_title', 'Bills')
@section('content')
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Track vendor bills and payments</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Bill</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Bill #</th><th class="px-5 py-3 font-semibold">Vendor</th><th class="px-5 py-3 font-semibold text-right">Amount</th><th class="px-5 py-3 font-semibold text-right">Paid</th><th class="px-5 py-3 font-semibold">Cost Allocation</th><th class="px-5 py-3 font-semibold">Due Date</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($bills as $b)
        @php $sc = ['unpaid' => '#ef4444', 'partial' => '#f59e0b', 'paid' => '#10b981'][$b->status] ?? '#9ca3af'; @endphp
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700">{{ $b->bill_number }}</td>
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $b->vendor_name }}</td>
            <td class="px-5 py-3.5 text-xs font-medium font-mono text-gray-900 text-right">{{ number_format($b->amount) }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-emerald-600 text-right">{{ number_format($b->paid_amount) }}</td>
            <td class="px-5 py-3.5 text-xs">
                @php $allocTotal = $b->costAllocations->sum('amount'); @endphp
                @if($allocTotal > 0)
                <span class="text-gray-700 font-medium">{{ number_format($allocTotal) }}</span>
                <div class="text-[10px] text-gray-400">
                @foreach($b->costAllocations as $ca)
                <div>{{ $ca->costCenter?->name ?? '?' }}: {{ number_format($ca->percentage, 0) }}%</div>
                @endforeach
                </div>
                @else
                <span class="text-gray-400">—</span>
                @endif
            </td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $b->due_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc }}"></span>{{ ucfirst($b->status) }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <button onclick="showAllocateDrawer({{ $b->id }}, '{{ $b->bill_number }}', {{ $b->amount }})" class="text-xs font-medium text-gray-600 hover:text-gray-900">Allocate</button>
                    <form id="del-bill-{{ $b->id }}" method="POST" action="{{ route('admin.bills.destroy', $b) }}" class="inline">@csrf @method('DELETE')<button type="button" onclick="confirmDelete('del-bill-{{ $b->id }}')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-xs text-gray-400">No bills found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($bills->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $bills->links() }}</div>
    @endif
</div>

{{-- New Bill Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Bill</h3><p class="text-[11px] text-slate-400 mt-0.5">Vendor bill to pay</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.bills.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Vendor Name <span class="text-red-500">*</span></label><input name="vendor_name" required placeholder="e.g. Vodacom Tanzania" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Bill Date <span class="text-red-500">*</span></label><input name="bill_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" required value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Paid Amount</label><input name="paid_amount" type="number" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="unpaid">Unpaid</option><option value="partial">Partial</option><option value="paid">Paid</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="3" placeholder="Optional details..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Allocate Drawer --}}
<div id="allocateDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeAllocDrawer()"></div>
    <div id="allocateDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Allocate Bill Costs</h3><p class="text-[11px] text-slate-400 mt-0.5" id="alloc_bill_info"></p></div>
            <button type="button" onclick="closeAllocDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.cost-centers.allocate') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <input type="hidden" name="allocatable_type" value="App\Models\Bill">
            <input type="hidden" name="allocatable_id" id="alloc_bill_id">
            <div id="alloc_bill_rows" class="space-y-2"></div>
            <button type="button" onclick="addBillAllocRow()" class="mt-2 text-xs font-medium text-gray-600 hover:text-gray-900">+ Add another cost center</button>
            <div class="flex items-center gap-3 pt-5 mt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save Allocations</button>
                <button type="button" onclick="closeAllocDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let billAllocRowIndex = 1;

function openDrawer() {
    document.getElementById('createDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('createDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDrawer() {
    document.getElementById('createDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('createDrawer').classList.add('hidden'), 300);
}
function closeAllocDrawer() {
    document.getElementById('allocateDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('allocateDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeDrawer(); closeAllocDrawer(); } });

function showAllocateDrawer(id, no, amount) {
    document.getElementById('alloc_bill_id').value = id;
    document.getElementById('alloc_bill_info').textContent = no + ' · TZS ' + Number(amount).toLocaleString();
    const container = document.getElementById('alloc_bill_rows');
    container.innerHTML = '';
    addBillAllocRow();
    document.getElementById('allocateDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('allocateDrawerPanel').classList.remove('-translate-x-full'));
}
function addBillAllocRow() {
    const container = document.getElementById('alloc_bill_rows');
    const idx = billAllocRowIndex++;
    let opts = '<option value="">Select cost center...</option>';
    @foreach($costCenters as $cc)
    opts += '<option value="{{ $cc->id }}">{{ $cc->name }}</option>';
    @endforeach
    const div = document.createElement('div');
    div.className = 'alloc-row flex items-center gap-2';
    div.innerHTML = `
        <select name="allocations[${idx}][cost_center_id]" required class="flex-1 px-3 py-2 border border-gray-200 rounded text-sm outline-none focus:border-gray-400 bg-white">${opts}</select>
        <input type="number" name="allocations[${idx}][amount]" placeholder="Amount" step="0.01" min="0" required class="w-24 px-3 py-2 border border-gray-200 rounded text-sm outline-none focus:border-gray-400">
        <input type="number" name="allocations[${idx}][percentage]" placeholder="%" step="0.01" min="0" max="100" class="w-14 px-3 py-2 border border-gray-200 rounded text-sm outline-none focus:border-gray-400">
        <button type="button" onclick="this.closest('.alloc-row').remove()" class="text-gray-400 hover:text-red-600">&times;</button>
    `;
    container.appendChild(div);
}
</script>
@endpush
