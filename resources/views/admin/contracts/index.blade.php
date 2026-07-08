@extends('layouts.admin')
@section('title', 'Contracts - ' . config('app.name'))
@section('page_title', 'Contracts')
@section('content')
<div class="flex items-center justify-between mb-4">
    <div>
        <p class="text-xs text-gray-500">Manage service, supply, and project contracts</p>
    </div>
    <button onclick="document.getElementById('contractModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ New Contract</button>
</div>

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3 mb-4 text-sm text-emerald-800">{{ session('success') }}</div>
@endif

@php
    $totalValue = $contracts->sum('contract_value');
    $activeCount = $contracts->where('status', 'active')->count();
    $draftCount = $contracts->where('status', 'draft')->count();
@endphp

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Total Value</p>
        <p class="text-xl font-bold text-gray-900 mt-1">TZS {{ number_format($totalValue, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-emerald-200 p-4">
        <p class="text-[10px] text-emerald-600 uppercase tracking-wide">Active</p>
        <p class="text-xl font-bold text-emerald-700 mt-1">{{ $activeCount }}</p>
    </div>
    <div class="bg-white rounded-xl border border-amber-200 p-4">
        <p class="text-[10px] text-amber-600 uppercase tracking-wide">Draft</p>
        <p class="text-xl font-bold text-amber-700 mt-1">{{ $draftCount }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Contract #</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Title</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Contractor</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Project</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Value</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($contracts as $c)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-900">{{ $c->contract_number }}</td>
                    <td class="px-4 py-3 text-xs text-gray-900 max-w-[180px] truncate font-medium">{{ $c->title }}</td>
                    <td class="px-4 py-3 text-xs text-gray-700">{{ $c->contractor_name }}</td>
                    <td class="px-4 py-3 text-xs"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600">{{ ucfirst($c->type) }}</span></td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $c->project?->title ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-right font-semibold text-gray-900">TZS {{ number_format($c->contract_value, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{
                            $c->status === 'active' ? 'bg-emerald-50 text-emerald-700' :
                            ($c->status === 'completed' ? 'bg-sky-50 text-sky-700' :
                            ($c->status === 'terminated' ? 'bg-rose-50 text-rose-700' :
                            'bg-amber-50 text-amber-700'))
                        }}">{{ ucfirst($c->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.contracts.show', $c) }}" class="px-2 py-1 text-[10px] font-semibold text-emerald-600 hover:text-emerald-700 border border-emerald-200 rounded hover:bg-emerald-50">View</a>
                            <button onclick="openEditContract({{ $c->id }})" class="px-2 py-1 text-[10px] font-semibold text-sky-600 hover:text-sky-700 border border-sky-200 rounded hover:bg-sky-50">Edit</button>
                            <form method="POST" action="{{ route('admin.contracts.destroy', $c) }}" class="inline" onsubmit="return confirm('Delete this contract?')">
                                @csrf @method('DELETE')
                                <button class="px-2 py-1 text-[10px] font-semibold text-rose-600 hover:text-rose-700 border border-rose-200 rounded hover:bg-rose-50">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-12 text-center text-sm text-gray-400">No contracts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t">{{ $contracts->links() }}</div>
</div>

<div id="contractModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6 my-8">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Contract</h3>
        <form method="POST" action="{{ route('admin.contracts.store') }}" class="space-y-3">@csrf
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Contractor Name *</label><input name="contractor_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Type *</label>
                    <select name="type" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                        <option value="service">Service</option>
                        <option value="supply">Supply</option>
                        <option value="construction">Construction</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="consultancy">Consultancy</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Project (optional)</label>
                    <select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                        <option value="">None</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date *</label><input name="start_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input name="end_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Contract Value *</label><input name="contract_value" type="number" step="0.01" min="0" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" rows="2"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Terms & Conditions</label><textarea name="terms" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" rows="3"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('contractModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Contract</button></div>
        </form>
    </div>
</div>

<script>
function openEditContract(id) {
    window.location.href = '{{ url("admin/contracts") }}/' + id;
}
</script>
@endsection
