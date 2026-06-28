@extends('layouts.admin')
@section('title', 'Tenders - ' . config('app.name'))
@section('page_title', 'Tenders')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage incoming tenders and convert to leads</p>
    <button onclick="document.getElementById('tenderModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Tender
    </button>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Tender No.</th><th class="px-5 py-3 font-medium">Title</th><th class="px-5 py-3 font-medium">Client</th><th class="px-5 py-3 font-medium">Est. Value</th><th class="px-5 py-3 font-medium">Closing Date</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>@forelse($tenders as $t)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $t->tender_number }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $t->title }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $t->client_name }}<br><span class="text-[10px] text-gray-400">{{ $t->client_organization }}</span></td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($t->estimated_value) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $t->closing_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3">@php $sc=['received'=>'amber','under_review'=>'sky','converted'=>'emerald','rejected'=>'red']; $c=$sc[$t->status]??'gray'; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-{{ $c }}-50 text-{{ $c }}-700">{{ ucfirst(str_replace('_',' ',$t->status)) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-2">
                <a href="{{ route('admin.tenders.show', $t) }}" class="text-sky-600 hover:text-sky-700 text-xs">View</a>
                @if($t->status==='received' || $t->status==='under_review')<form method="POST" action="{{ route('admin.tenders.convert-to-lead', $t) }}">@csrf<button type="submit" class="text-emerald-600 hover:text-emerald-700 text-xs" onclick="return confirm('Convert this tender to a Lead?')">→ Lead</button></form>@endif
                <form id="del-tnd-{{ $t->id }}" method="POST" action="{{ route('admin.tenders.destroy', $t) }}">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-tnd-{{ $t->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
            </td>
        
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No tenders found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $tenders->links() }}</div>
</div>

{{-- Create Tender Modal --}}
<div id="tenderModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Add New Tender</h3><button onclick="document.getElementById('tenderModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button></div>
        <form method="POST" action="{{ route('admin.tenders.store') }}" class="p-6 space-y-4">@csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Client Name *</label><input name="client_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Client Organization</label><input name="client_organization" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Client Email</label><input name="client_email" type="email" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Client Phone</label><input name="client_phone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Estimated Value (TZS)</label><input name="estimated_value" type="number" min="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Submission Date</label><input name="submission_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Closing Date</label><input name="closing_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Requirements</label><textarea name="requirements" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('tenderModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Tender</button></div>
        </form>
    </div>
</div>
@endsection
