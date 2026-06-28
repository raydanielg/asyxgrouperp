@extends('layouts.admin')
@section('title', 'Vendor Invoices - ' . config('app.name'))
@section('page_title', 'Vendor Invoices')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track vendor invoices and payments</p>
    <button onclick="document.getElementById('vinvModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Vendor Invoice
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Invoice No.</th><th class="px-5 py-3 font-medium">Supplier</th><th class="px-5 py-3 font-medium">Project</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Paid</th><th class="px-5 py-3 font-medium">Balance</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($invoices as $v)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $v->vendor_invoice_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $v->supplier?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $v->project?->title ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($v->total) }}</td>
            <td class="px-5 py-3 text-xs text-emerald-600">TZS {{ number_format($v->amount_paid) }}</td>
            <td class="px-5 py-3 text-xs text-red-600">TZS {{ number_format($v->balance) }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] {{ ($v->status==='paid') ? 'bg-emerald-50 text-emerald-700' : ($v->status==='partially_paid') ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700' }}">{{ ucfirst(str_replace('_',' ',$v->status)) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-2">
                <a href="{{ route('admin.vendor-invoices.show', $v) }}" class="text-sky-600 hover:text-sky-700 text-xs">View</a>
                <form id="del-vinv-{{ $v->id }}" method="POST" action="{{ route('admin.vendor-invoices.destroy', $v) }}">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-vinv-{{ $v->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
            </td>
        
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No vendor invoices found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $invoices->links() }}</div>
</div>

<div id="vinvModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Add Vendor Invoice</h3><button onclick="document.getElementById('vinvModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button></div>
        <form method="POST" action="{{ route('admin.vendor-invoices.store') }}" class="p-6 space-y-4">@csrf
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
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label><select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">No Project</option>
        @foreach($projects as $p)
        <option value="{{ $p->id }}">{{ $p->title }}</option>
        @endforeach
        </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier Invoice Ref</label><input name="supplier_invoice_ref" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Invoice Date *</label><input name="invoice_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Subtotal *</label><input name="subtotal" type="number" step="0.01" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Tax Amount</label><input name="tax_amount" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Total *</label><input name="total" type="number" step="0.01" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('vinvModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Invoice</button></div>
        </form>
    </div>
</div>
@endsection
