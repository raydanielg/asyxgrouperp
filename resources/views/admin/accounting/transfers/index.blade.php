@extends('layouts.admin')
@section('title', 'Transfers - ' . config('app.name'))
@section('page_title', 'Bank Transfers')
@section('content')
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Record transfers between bank accounts</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Transfer</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Transfer #</th><th class="px-5 py-3 font-semibold">From</th><th class="px-5 py-3 font-semibold">To</th><th class="px-5 py-3 font-semibold text-right">Amount</th><th class="px-5 py-3 font-semibold">Date</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($transfers as $t)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700">{{ $t->transfer_number }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-700">{{ $t->fromAccount?->account_name ?? 'N/A' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-700">{{ $t->toAccount?->account_name ?? 'N/A' }}</td>
            <td class="px-5 py-3.5 text-xs font-medium font-mono text-gray-900 text-right">{{ number_format($t->amount) }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $t->transfer_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $t->status === 'completed' ? '#10b981' : '#f59e0b' }}"></span>{{ ucfirst($t->status) }}</span></td>
            <td class="px-5 py-3.5 text-right"><form id="del-trf-{{ $t->id }}" method="POST" action="{{ route('admin.acc-transfers.destroy', $t) }}" class="inline">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-trf-{{ $t->id }}')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No transfers found</td></tr>
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
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Bank Transfer</h3><p class="text-[11px] text-slate-400 mt-0.5">Move funds between accounts</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.acc-transfers.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">From Account <span class="text-red-500">*</span></label><select name="from_account_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">Select...</option>
                @foreach($accounts as $a)
                <option value="{{ $a->id }}">{{ $a->account_name }} (TZS {{ number_format($a->current_balance) }})</option>
                @endforeach
                </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">To Account <span class="text-red-500">*</span></label><select name="to_account_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">Select...</option>
                @foreach($accounts as $a)
                <option value="{{ $a->id }}">{{ $a->account_name }}</option>
                @endforeach
                </select></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" required value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Transfer Date <span class="text-red-500">*</span></label><input name="transfer_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="3" placeholder="Optional details..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Transfer</button>
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
</script>
@endpush
