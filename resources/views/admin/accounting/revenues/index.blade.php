@extends('layouts.admin')
@section('title', 'Revenue - ' . config('app.name'))
@section('page_title', 'Revenue')
@section('content')
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Record and track revenue streams</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Revenue</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Revenue #</th><th class="px-5 py-3 font-semibold">Category</th><th class="px-5 py-3 font-semibold">Payer</th><th class="px-5 py-3 font-semibold text-right">Amount</th><th class="px-5 py-3 font-semibold">Cost Allocation</th><th class="px-5 py-3 font-semibold">Date</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($revenues as $r)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700">{{ $r->revenue_number }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-700">{{ $r->category ?? 'N/A' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $r->payer ?? 'N/A' }}</td>
            <td class="px-5 py-3.5 text-xs font-medium font-mono text-emerald-600 text-right">+{{ number_format($r->amount) }}</td>
            <td class="px-5 py-3.5 text-xs">
                @php $allocTotal = $r->costAllocations->sum('amount'); @endphp
                @if($allocTotal > 0)
                <span class="text-gray-700 font-medium">{{ number_format($allocTotal) }}</span>
                <div class="text-[10px] text-gray-400">
                @foreach($r->costAllocations as $ca)
                <div>{{ $ca->costCenter?->name ?? '?' }}: {{ number_format($ca->percentage, 0) }}%</div>
                @endforeach
                </div>
                @else
                <span class="text-gray-400">—</span>
                @endif
            </td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $r->revenue_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <button onclick="showAllocateDrawer({{ $r->id }}, '{{ $r->revenue_number }}', {{ $r->amount }})" class="text-xs font-medium text-gray-600 hover:text-gray-900">Allocate</button>
                    <form id="del-rev-{{ $r->id }}" method="POST" action="{{ route('admin.revenues.destroy', $r) }}" class="inline">@csrf @method('DELETE')<button type="button" onclick="confirmDelete('del-rev-{{ $r->id }}')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No revenue records</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($revenues->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $revenues->links() }}</div>
    @endif
</div>

{{-- New Revenue Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Revenue</h3><p class="text-[11px] text-slate-400 mt-0.5">Record income received</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.revenues.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><input name="category" placeholder="e.g. Sales, Services, Interest" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" required value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Revenue Date <span class="text-red-500">*</span></label><input name="revenue_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Bank Account</label><select name="bank_account_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">— None —</option>
                @foreach($accounts as $a)
                <option value="{{ $a->id }}">{{ $a->account_name }}</option>
                @endforeach
                </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label><select name="payment_method" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="cash">Cash</option><option value="bank">Bank Transfer</option><option value="card">Card</option><option value="mobile">Mobile Money</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Payer</label><input name="payer" placeholder="Received from..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
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
            <div><h3 class="text-sm font-bold text-white">Allocate Revenue</h3><p class="text-[11px] text-slate-400 mt-0.5" id="alloc_rev_info"></p></div>
            <button type="button" onclick="closeAllocDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.cost-centers.allocate') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <input type="hidden" name="allocatable_type" value="App\Models\Revenue">
            <input type="hidden" name="allocatable_id" id="alloc_rev_id">
            <div id="alloc_rev_rows" class="space-y-2"></div>
            <button type="button" onclick="addRevAllocRow()" class="mt-2 text-xs font-medium text-gray-600 hover:text-gray-900">+ Add another cost center</button>
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
let revAllocRowIndex = 1;

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
    document.getElementById('alloc_rev_id').value = id;
    document.getElementById('alloc_rev_info').textContent = no + ' · TZS ' + Number(amount).toLocaleString();
    const container = document.getElementById('alloc_rev_rows');
    container.innerHTML = '';
    addRevAllocRow();
    document.getElementById('allocateDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('allocateDrawerPanel').classList.remove('-translate-x-full'));
}
function addRevAllocRow() {
    const container = document.getElementById('alloc_rev_rows');
    const idx = revAllocRowIndex++;
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
