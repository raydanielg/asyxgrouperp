@extends('layouts.admin')
@section('title', 'Suppliers - ' . config('app.name'))
@section('page_title', 'Suppliers')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage suppliers and vendors</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Supplier
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Name</th><th class="px-5 py-3 font-medium">Contact Person</th><th class="px-5 py-3 font-medium">Email</th><th class="px-5 py-3 font-medium">Phone</th><th class="px-5 py-3 font-medium">Payment Terms</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($suppliers as $s)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $s->name }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $s->contact_person ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $s->email ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $s->phone ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $s->payment_terms ?? 'N/A' }}</td>
            <td class="px-5 py-3">@if($s->is_active)<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700">Active</span>@else<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600">Inactive</span>@endif</td>
            <td class="px-5 py-3"><form id="del-sup-{{ $s->id }}" method="POST" action="{{ route('admin.suppliers.destroy', $s) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-sup-{{ $s->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        </tr>@empty<tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No suppliers found</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $suppliers->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Supplier</h3>
        <form method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Contact Person</label><input name="contact_person" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input name="email" type="email" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Address</label><textarea name="address" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Terms</label><input name="payment_terms" placeholder="e.g. Net 30" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="flex items-center gap-2"><input type="checkbox" name="is_active" checked class="rounded border-gray-300 text-emerald-600"><label class="text-xs text-gray-600">Active</label></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
