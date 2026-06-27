@extends('layouts.admin')
@section('title', 'CRM Leads - ' . config('app.name'))
@section('page_title', 'Leads')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage sales leads and potential customers</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Lead
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Lead #</th><th class="px-5 py-3 font-medium">Name</th><th class="px-5 py-3 font-medium">Company</th><th class="px-5 py-3 font-medium">Source</th><th class="px-5 py-3 font-medium">Assigned To</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($leads as $l)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $l->lead_number }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $l->full_name }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $l->company ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $l->source ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $l->assignedTo?->name ?? 'Unassigned' }}</td>
            <td class="px-5 py-3">@php $c=['new'=>'sky','contacted'=>'amber','qualified'=>'emerald','lost'=>'red','converted'=>'emerald']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$l->status] ?? 'gray' }}-50 text-{{ $c[$l->status] ?? 'gray' }}-700">{{ ucfirst($l->status) }}</span></td>
            <td class="px-5 py-3"><form id="del-lead-{{ $l->id }}" method="POST" action="{{ route('admin.crm-leads.destroy', $l) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-lead-{{ $l->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        </tr>@empty<tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No leads found</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $leads->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Lead</h3>
        <form method="POST" action="{{ route('admin.crm-leads.store') }}" class="space-y-3">@csrf
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">First Name *</label><input name="first_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Last Name</label><input name="last_name" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" type="email" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Company</label><input name="company" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Source</label><select name="source" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="website">Website</option><option value="referral">Referral</option><option value="social_media">Social Media</option><option value="cold_call">Cold Call</option><option value="email_campaign">Email Campaign</option><option value="other">Other</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label><select name="assigned_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Unassigned</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="new">New</option><option value="contacted">Contacted</option><option value="qualified">Qualified</option><option value="converted">Converted</option><option value="lost">Lost</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
