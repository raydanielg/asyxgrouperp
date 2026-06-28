@extends('layouts.admin')
@section('title', 'Revenue - ' . config('app.name'))
@section('page_title', 'Revenue')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Record and track revenue streams</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Revenue
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Revenue #</th><th class="px-5 py-3 font-medium">Category</th><th class="px-5 py-3 font-medium">Payer</th><th class="px-5 py-3 font-medium">Amount</th><th class="px-5 py-3 font-medium">Method</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($revenues as $r)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $r->revenue_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $r->category ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $r->payer ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-emerald-600">TZS {{ number_format($r->amount) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ ucfirst($r->payment_method) }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $r->revenue_date->format('d M Y') }}</td>
            <td class="px-5 py-3"><form id="del-rev-{{ $r->id }}" method="POST" action="{{ route('admin.revenues.destroy', $r) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-rev-{{ $r->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No revenue records</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $revenues->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Revenue</h3>
        <form method="POST" action="{{ route('admin.revenues.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><input name="category" placeholder="e.g. Sales, Services, Interest" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" required value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Revenue Date *</label><input name="revenue_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Bank Account</label><select name="bank_account_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">None</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->account_name }}</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label><select name="payment_method" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="cash">Cash</option><option value="bank">Bank Transfer</option><option value="card">Card</option><option value="mobile">Mobile Money</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Payer</label><input name="payer" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
