@extends('layouts.admin')
@section('title', 'Delivery Notes - ' . config('app.name'))
@section('page_title', 'Delivery Notes')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track delivery notes from vendors</p>
    <button onclick="document.getElementById('dnModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Delivery Note
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">DN No.</th><th class="px-5 py-3 font-medium">LPO</th><th class="px-5 py-3 font-medium">Supplier</th><th class="px-5 py-3 font-medium">Delivery Date</th><th class="px-5 py-3 font-medium">Delivered By</th><th class="px-5 py-3 font-medium">Vehicle</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($deliveryNotes as $d)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $d->delivery_note_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $d->lpo?->lpo_number ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $d->supplier?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $d->delivery_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $d->delivered_by ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $d->vehicle_number ?? '—' }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] {{ ($d->status==='verified') ? 'bg-emerald-50 text-emerald-700' : ($d->status==='pending_verification') ? 'bg-amber-50 text-amber-700' : 'bg-sky-50 text-sky-700' }}">{{ ucfirst(str_replace('_',' ',$d->status)) }}</span></td>
            <td class="px-5 py-3"><form id="del-dn-{{ $d->id }}" method="POST" action="{{ route('admin.delivery-notes.destroy', $d) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-dn-{{ $d->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No delivery notes found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $deliveryNotes->links() }}</div>
</div>

<div id="dnModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-xl w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Add Delivery Note</h3><button onclick="document.getElementById('dnModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button></div>
        <form method="POST" action="{{ route('admin.delivery-notes.store') }}" class="p-6 space-y-4">@csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">LPO</label><select name="lpo_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">No LPO</option>
        @foreach($lpos as $l)
        <option value="{{ $l->id }}">{{ $l->lpo_number }}</option>
        @endforeach
        </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select name="supplier_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">Select...</option>
        @foreach($suppliers as $s)
        <option value="{{ $s->id }}">{{ $s->name }}</option>
        @endforeach
        </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">GRN</label><select name="grn_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">No GRN</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Delivery Date *</label><input name="delivery_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Delivered By</label><input name="delivered_by" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Received By</label><input name="received_by" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Vehicle Number</label><input name="vehicle_number" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('dnModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Delivery Note</button></div>
        </form>
    </div>
</div>
@endsection
