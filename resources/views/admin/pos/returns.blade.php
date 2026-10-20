@extends('layouts.admin')
@section('title', 'POS Returns')
@section('page_title', 'POS Returns & Refunds')

@section('content')
<div class="bg-white rounded-md border border-gray-200 mb-4">
    <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-800">Returns</h3></div>
    <table class="w-full">
        <thead><tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100">
            <th class="px-5 py-2.5">Return #</th><th class="px-5 py-2.5">Sale</th><th class="px-5 py-2.5">Items</th>
            <th class="px-5 py-2.5 text-right">Refund</th><th class="px-5 py-2.5">Status</th><th class="px-5 py-2.5 text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($returns as $r)
            <tr>
                <td class="px-5 py-3 text-xs font-mono font-medium text-gray-800">{{ $r->return_number }}</td>
                <td class="px-5 py-3 text-xs font-mono text-gray-500">{{ $r->sale?->sale_number }}</td>
                <td class="px-5 py-3 text-xs text-gray-600">
                    {{ $r->items->count() }} item{{ $r->items->count() === 1 ? '' : 's' }}
                    <span class="text-[10px] text-gray-400">({{ $r->items->where('disposition','restock')->count() }} restock / {{ $r->items->where('disposition','damaged')->count() }} damaged)</span>
                </td>
                <td class="px-5 py-3 text-xs text-right font-mono">
                    {{ $r->refund_amount > 0 ? number_format($r->refund_amount, 2) : '—' }}
                    @if($r->refund_status === 'processed')<span class="text-[9px] text-amber-600 block">internal</span>@endif
                </td>
                <td class="px-5 py-3">
                    <span class="inline-flex items-center gap-1.5 text-xs"><span style="width:6px;height:6px;border-radius:99px;background:{{ $r->status === 'processed' ? '#10b981' : '#f59e0b' }}"></span>{{ ucfirst($r->status) }}</span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-end gap-3">
                        @if($r->status === 'pending' && $r->created_by !== auth()->id())
                        <form method="POST" action="{{ route('admin.pos-returns.process', $r) }}" class="inline" onsubmit="return confirmAction(this, {title:'Process return?', text:'Restock items + record refund.', confirmText:'Process', color:'#059669'})">@csrf
                            <button class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Process</button>
                        </form>
                        @endif
                        @if($r->status === 'pending')
                        <form method="POST" action="{{ route('admin.pos-returns.destroy', $r) }}" class="inline" onsubmit="return confirmAction(this, {title:'Remove return?'})">@csrf @method('DELETE')
                            <button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-8 text-center text-xs text-gray-400">No returns.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-3">{{ $returns->links() }}</div>
</div>

<div class="bg-white rounded-md border border-gray-200" x-data="{open:false}">
    <button @click="open=!open" class="w-full px-5 py-3.5 flex items-center justify-between text-left">
        <h3 class="text-sm font-semibold text-gray-800">New Return</h3>
        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" class="border-t border-gray-100 p-5">
        <form method="POST" action="{{ route('admin.pos-returns.store', $sales->first()?->id ?? 0) }}" id="posReturnForm" class="space-y-3 max-w-lg">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sale</label>
                <select id="saleSelect" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none bg-white" onchange="document.getElementById('posReturnForm').action='/admin/pos-sales/'+this.value+'/returns'">
                    @foreach($sales as $s)<option value="{{ $s->id }}">{{ $s->sale_number }} — {{ number_format($s->total_amount,2) }} ({{ $s->status }})</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sale Item ID <span class="text-red-500">*</span></label>
                <input name="items[0][pos_sale_item_id]" type="number" required placeholder="Item ID from sale detail" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                    <input name="items[0][quantity]" type="number" step="0.01" min="0.01" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Disposition</label>
                    <select name="items[0][disposition]" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none bg-white">
                        <option value="restock">Restock</option><option value="damaged">Damaged</option>
                    </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Refund Amount</label>
                    <input name="refund_amount" type="number" step="0.01" min="0" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none"></div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Reason <span class="text-red-500">*</span></label>
                <input name="reason" required maxlength="500" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none">
            </div>
            <p class="text-[10px] text-gray-400">Restock items return to the product's assigned warehouse. Damaged items do not restore stock.</p>
            <button class="px-4 py-2 bg-gray-800 text-white text-xs font-medium rounded hover:bg-gray-900">Submit Return</button>
        </form>
    </div>
</div>
@endsection
