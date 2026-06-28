@extends('layouts.admin')
@section('title', 'Estimates - ' . config('app.name'))
@section('page_title', 'Estimates')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track cost estimates for projects</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Estimate
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Estimate #</th><th class="px-5 py-3 font-medium">Client</th><th class="px-5 py-3 font-medium">Amount</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Valid Until</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($estimates ?? [] as $e)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $e->estimate_number ?? 'EST-'.str_pad($e->id,4,'0',STR_PAD_LEFT) }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $e->client_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($e->amount ?? 0) }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $e->estimate_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $e->valid_until?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3">@php $c=['draft'=>'gray','sent'=>'blue','accepted'=>'emerald','rejected'=>'red']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$e->status] ?? 'gray' }}-50 text-{{ $c[$e->status] ?? 'gray' }}-700">{{ ucfirst($e->status ?? 'draft') }}</span></td>
            <td class="px-5 py-3">
                <form id="del-estimate-{{ $e->id }}" method="POST" action="{{ route('admin.estimates.destroy', $e) }}">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-estimate-{{ $e->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No estimates found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $estimates->links() ?? '' }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Estimate</h3>
        <form method="POST" action="{{ route('admin.estimates.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Client Name *</label><input name="client_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Estimate Date *</label><input name="estimate_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Valid Until</label><input name="valid_until" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" required value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-3 pt-2"><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">Save Estimate</button><button type="button" onclick="this.closest('#createModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">Cancel</button></div>
        </form>
    </div>
</div>
@endsection
