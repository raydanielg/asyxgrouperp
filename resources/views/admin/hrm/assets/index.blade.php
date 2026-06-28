@extends('layouts.admin')
@section('title', 'Employee Assets - ' . config('app.name'))
@section('page_title', 'Employee Assets')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track assets assigned to employees</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Assign Asset
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Asset</th><th class="px-5 py-3 font-medium">Employee</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Serial #</th><th class="px-5 py-3 font-medium">Assigned Date</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($assets as $a)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $a->asset_name }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $a->employee?->full_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $a->asset_type ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-mono text-gray-500">{{ $a->serial_number ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $a->assigned_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium @if($a->status=='assigned')bg-emerald-50 text-emerald-700 @elseif($a->status=='returned')bg-gray-50 text-gray-600 @else bg-amber-50 text-amber-700 @endif">{{ ucfirst($a->status) }}</span></td>
            <td class="px-5 py-3"><form id="del-asset-{{ $a->id }}" method="POST" action="{{ route('admin.assets.destroy', $a) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-asset-{{ $a->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No assets assigned</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $assets->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Assign Asset</h3>
        <form method="POST" action="{{ route('admin.assets.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Employee *</label><select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>
        @foreach($employees as $e)
        <option value="{{ $e->id }}">{{ $e->full_name }}</option>
        @endforeach
        </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Asset Name *</label><input name="asset_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Asset Type</label><input name="asset_type" placeholder="e.g. Laptop" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Serial Number</label><input name="serial_number" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Assigned Date</label><input name="assigned_date" type="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Return Date</label><input name="return_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="assigned">Assigned</option><option value="returned">Returned</option><option value="lost">Lost</option></select></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Assign</button></div>
        </form>
    </div>
</div>
@endsection
