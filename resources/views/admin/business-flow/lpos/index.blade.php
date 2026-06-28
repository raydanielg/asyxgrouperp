@extends('layouts.admin')
@section('title', 'LPOs - ' . config('app.name'))
@section('page_title', 'Local Purchase Orders (LPOs)')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Create and manage Local Purchase Orders for vendors</p>
    <button onclick="document.getElementById('lpoModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create LPO
    </button>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">LPO No.</th><th class="px-5 py-3 font-medium">Project</th><th class="px-5 py-3 font-medium">Supplier</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($lpos as $l)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $l->lpo_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $l->project?->title ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $l->supplier?->name ?? $l->supplier_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $l->lpo_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($l->total) }}</td>
            <td class="px-5 py-3">@php $sc=['draft'=>'gray','sent'=>'sky','partially_received'=>'amber','received'=>'emerald','closed'=>'emerald','cancelled'=>'red']; $c=$sc[$l->status]??'gray'; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-{{ $c }}-50 text-{{ $c }}-700">{{ ucfirst(str_replace('_',' ',$l->status)) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-2">
                <a href="{{ route('admin.lpos.show', $l) }}" class="text-sky-600 hover:text-sky-700 text-xs">View</a>
                <form id="del-lpo-{{ $l->id }}" method="POST" action="{{ route('admin.lpos.destroy', $l) }}">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-lpo-{{ $l->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
            </td>
        
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No LPOs found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $lpos->links() }}</div>
</div>

{{-- Create LPO Modal --}}
<div id="lpoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Create LPO</h3><button onclick="document.getElementById('lpoModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button></div>
        <form method="POST" action="{{ route('admin.lpos.store') }}" class="p-6 space-y-4">@csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label><select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">No Project</option>
        @foreach($projects as $p)
        <option value="{{ $p->id }}">{{ $p->title }}</option>
        @endforeach
        </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select name="supplier_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">Select Supplier...</option>
        @foreach($suppliers as $s)
        <option value="{{ $s->id }}">{{ $s->name }}</option>
        @endforeach
        </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">LPO Date *</label><input name="lpo_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Expected Delivery</label><input name="expected_delivery_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Terms</label><textarea name="terms" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Items *</label><div id="lpoItems" class="space-y-2"></div><button type="button" onclick="addLpoItem()" class="text-xs text-emerald-600 hover:text-emerald-700 mt-2">+ Add Item</button></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('lpoModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create LPO</button></div>
        </form>
    </div>
</div>
<script>
let lpoItemIdx = 0;
function addLpoItem() {
    const container = document.getElementById('lpoItems');
    const div = document.createElement('div');
    div.className = 'flex flex-wrap items-center gap-2 border rounded-lg p-2';
    div.innerHTML = '<input name="items['+lpoItemIdx+'][description]" placeholder="Description" required class="flex-1 min-w-[150px] px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+lpoItemIdx+'][quantity_ordered]" type="number" step="0.01" placeholder="Qty" required class="w-20 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+lpoItemIdx+'][unit]" placeholder="Unit" class="w-20 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<input name="items['+lpoItemIdx+'][unit_price]" type="number" step="0.01" placeholder="Unit Price" required class="w-28 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none">'+
        '<button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 text-xs">&times;</button>';
    container.appendChild(div);
    lpoItemIdx++;
}
addLpoItem();
</script>
@endsection
