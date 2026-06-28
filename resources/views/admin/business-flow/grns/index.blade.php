@extends('layouts.admin')
@section('title', 'GRNs - ' . config('app.name'))
@section('page_title', 'Goods Received Notes (GRN)')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Verify goods received against LPOs</p>
    <button onclick="document.getElementById('grnModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create GRN
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">GRN No.</th><th class="px-5 py-3 font-medium">LPO</th><th class="px-5 py-3 font-medium">Supplier</th><th class="px-5 py-3 font-medium">Received Date</th><th class="px-5 py-3 font-medium">DN Ref</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>@forelse($grns as $g)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $g->grn_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $g->lpo?->lpo_number ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $g->supplier?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $g->received_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $g->delivery_note_number ?? '—' }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] @if($g->status==='received')bg-emerald-50 text-emerald-700@elseif($g->status==='discrepant')bg-amber-50 text-amber-700@else bg-red-50 text-red-700@endif">{{ ucfirst($g->status) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-2">
                <a href="{{ route('admin.grns.show', $g) }}" class="text-sky-600 hover:text-sky-700 text-xs">View</a>
                <form id="del-grn-{{ $g->id }}" method="POST" action="{{ route('admin.grns.destroy', $g) }}">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-grn-{{ $g->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
            </td>
        
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No GRNs found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $grns->links() }}</div>
</div>

<div id="grnModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Create GRN</h3><button onclick="document.getElementById('grnModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button></div>
        <form method="POST" action="{{ route('admin.grns.store') }}" class="p-6 space-y-4">@csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">LPO</label><select name="lpo_id" id="grnLpoSelect" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">Select LPO...</option>@foreach($lpos as $l)<option value="{{ $l->id }}">{{ $l->lpo_number }} ({{ $l->supplier?->name ?? 'N/A' }})</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select name="supplier_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">Select...</option>@foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Received Date *</label><input name="received_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Delivery Note No.</label><input name="delivery_note_number" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Items *</label><div id="grnItems" class="space-y-2"></div><button type="button" onclick="addGrnItem()" class="text-xs text-emerald-600 hover:text-emerald-700 mt-2">+ Add Item</button></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('grnModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create GRN</button></div>
        </form>
    </div>
</div>
<script>
let grnItemIdx = 0;
function addGrnItem() {
    const container = document.getElementById('grnItems');
    const div = document.createElement('div');
    div.className = 'flex flex-wrap items-center gap-2 border rounded-lg p-2';
    div.innerHTML = '<input name="items['+grnItemIdx+'][description]" placeholder="Description" required class="flex-1 min-w-[150px] px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+grnItemIdx+'][quantity_expected]" type="number" step="0.01" placeholder="Expected" class="w-24 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+grnItemIdx+'][quantity_received]" type="number" step="0.01" placeholder="Received" required class="w-24 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+grnItemIdx+'][unit]" placeholder="Unit" class="w-20 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+grnItemIdx+'][remarks]" placeholder="Remarks" class="flex-1 min-w-[100px] px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 text-xs">&times;</button>';
    container.appendChild(div);
    grnItemIdx++;
}
addGrnItem();
</script>
@endsection
