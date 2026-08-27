@extends('layouts.admin')
@section('title', 'Cost Centers - ' . config('app.name'))
@section('page_title', 'Cost Centers')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage cost allocation centers for expenses and transactions</p>
    <div class="flex gap-2">
        <a href="{{ route('admin.cost-centers.report') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">View Report</a>
        <button onclick="showCreateModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ New Cost Center</button>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50">
                    <th class="px-5 py-3 font-medium">Code</th>
                    <th class="px-5 py-3 font-medium">Name</th>
                    <th class="px-5 py-3 font-medium">Description</th>
                    <th class="px-5 py-3 font-medium">Allocations</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($costCenters as $cc)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                    <td class="px-5 py-3 text-xs font-mono text-gray-600">{{ $cc->code ?: '—' }}</td>
                    <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $cc->name }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500 max-w-xs truncate">{{ $cc->description ?: '—' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-600">{{ $cc->allocations_count }}</td>
                    <td class="px-5 py-3 text-xs">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $cc->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-500' }}">{{ $cc->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>
                    <td class="px-5 py-3 text-xs">
                        <button onclick="showEditModal({{ $cc->id }}, '{{ addslashes($cc->name) }}', '{{ addslashes($cc->code ?? '') }}', '{{ addslashes($cc->description ?? '') }}', {{ $cc->is_active ? 'true' : 'false' }})" class="text-emerald-600 hover:text-emerald-700 font-medium mr-3">Edit</button>
                        <form id="del-cc-{{ $cc->id }}" method="POST" action="{{ route('admin.cost-centers.destroy', $cc) }}" class="inline">@csrf @method('DELETE')<button type="button" onclick="confirmDelete('del-cc-{{ $cc->id }}')" class="text-red-500 hover:text-red-700">Delete</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No cost centers yet. Create one to start allocating costs.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $costCenters->links() }}</div>
</div>

{{-- Create Modal --}}
<div id="createModal" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Cost Center</h3>
        <form method="POST" action="{{ route('admin.cost-centers.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Code</label><input name="code" placeholder="e.g. SGR, HO, CC" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="flex items-center gap-2 text-sm text-gray-600"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"> Active</label></div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Cost Center</h3>
        <form id="editForm" method="POST" class="space-y-3">@csrf @method('PATCH')
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" id="edit_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Code</label><input name="code" id="edit_code" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" id="edit_description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="flex items-center gap-2 text-sm text-gray-600"><input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"> Active</label></div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }

function showEditModal(id, name, code, description, active) {
    document.getElementById('editForm').action = '{{ route('admin.cost-centers.update', '__ID__') }}'.replace('__ID__', id);
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_is_active').checked = active;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endpush