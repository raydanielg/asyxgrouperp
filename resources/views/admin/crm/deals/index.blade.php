@extends('layouts.admin')
@section('title', 'CRM Deals - ' . config('app.name'))
@section('page_title', 'Deals')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track deals through your sales pipeline</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Deal
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Deal #</th><th class="px-5 py-3 font-medium">Title</th><th class="px-5 py-3 font-medium">Lead</th><th class="px-5 py-3 font-medium">Value</th><th class="px-5 py-3 font-medium">Stage</th><th class="px-5 py-3 font-medium">Expected Close</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($deals as $d)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $d->deal_number }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $d->title }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $d->lead?->full_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($d->value) }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700">{{ ucfirst(str_replace('_', ' ', $d->stage)) }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $d->expected_close_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3">@php $c=['open'=>'emerald','won'=>'emerald','lost'=>'red','cancelled'=>'gray']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$d->status] ?? 'gray' }}-50 text-{{ $c[$d->status] ?? 'gray' }}-700">{{ ucfirst($d->status) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-2">@if($d->status==='open' && !$d->project_id)<form method="POST" action="{{ route('admin.crm-deals.convert-to-project', $d) }}">@csrf<button type="submit" class="text-emerald-600 hover:text-emerald-700 text-xs" onclick="return confirm('Convert this deal to a Project?')">→ Project</button></form>@endif<form id="del-deal-{{ $d->id }}" method="POST" action="{{ route('admin.crm-deals.destroy', $d) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-deal-{{ $d->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No deals found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $deals->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Deal</h3>
        <form method="POST" action="{{ route('admin.crm-deals.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Lead</label><select name="lead_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">None</option>@foreach($leads as $l)<option value="{{ $l->id }}">{{ $l->full_name }} ({{ $l->company ?? 'N/A' }})</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Value *</label><input name="value" type="number" step="0.01" required value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Stage</label><select name="stage" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="prospecting">Prospecting</option><option value="qualification">Qualification</option><option value="negotiation">Negotiation</option><option value="proposal">Proposal</option><option value="closed_won">Closed Won</option><option value="closed_lost">Closed Lost</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Expected Close Date</label><input name="expected_close_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label><select name="assigned_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Unassigned</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
