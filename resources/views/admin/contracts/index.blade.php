@extends('layouts.admin')
@section('title', 'Contracts - ' . config('app.name'))
@section('page_title', 'Contracts')
@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

@php
    $totalValue = $contracts->sum('contract_value');
    $activeCount = $contracts->where('status', 'active')->count();
    $draftCount = $contracts->where('status', 'draft')->count();
@endphp

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Manage service, supply, and project contracts</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Contract</button>
    </div>
    <div class="grid grid-cols-3 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Value</p><p class="text-lg font-bold text-gray-900 mt-0.5">TZS {{ number_format($totalValue) }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Active</p><p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $activeCount }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Draft</p><p class="text-lg font-bold text-amber-600 mt-0.5">{{ $draftCount }}</p></div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Contract #</th><th class="px-5 py-3 font-semibold">Title</th><th class="px-5 py-3 font-semibold">Contractor</th><th class="px-5 py-3 font-semibold">Type</th><th class="px-5 py-3 font-semibold">Project</th><th class="px-5 py-3 font-semibold text-right">Value</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($contracts as $c)
        @php $sc = ['active' => '#10b981', 'completed' => '#3b82f6', 'terminated' => '#ef4444', 'draft' => '#f59e0b'][$c->status] ?? '#9ca3af'; @endphp
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700">{{ $c->contract_number }}</td>
            <td class="px-5 py-3.5"><a href="{{ route('admin.contracts.show', $c) }}" class="text-xs font-medium text-gray-900 hover:text-gray-600 max-w-[200px] truncate block">{{ $c->title }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-600">{{ $c->contractor_name }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-600 capitalize">{{ $c->type }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $c->project?->title ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-mono font-bold text-gray-900">TZS {{ number_format($c->contract_value) }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc }}"></span>{{ $c->status }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.contracts.show', $c) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <form method="POST" action="{{ route('admin.contracts.destroy', $c) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete this contract?'})">
                        @csrf @method('DELETE')
                        <button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No contracts yet.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($contracts->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $contracts->links() }}</div>
    @endif
</div>

{{-- New Contract Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-xl bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Contract</h3><p class="text-[11px] text-slate-400 mt-0.5">Create a service, supply, or project contract</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.contracts.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="mb-5">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Details</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="e.g. Maintenance Services Agreement" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Contractor Name <span class="text-red-500">*</span></label><input name="contractor_name" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            <option value="service">Service</option><option value="supply">Supply</option><option value="construction">Construction</option><option value="maintenance">Maintenance</option><option value="consultancy">Consultancy</option><option value="other">Other</option>
                        </select>
                    </div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Contract Value <span class="text-red-500">*</span></label><input name="contract_value" type="number" step="0.01" min="0" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label>
                        <select name="project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            <option value="">— None —</option>
                            @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-5 pt-5 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Period</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date <span class="text-red-500">*</span></label><input name="start_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input name="end_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
            </div>
            <div class="pt-5 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Notes</h4>
                <div class="space-y-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Terms &amp; Conditions</label><textarea name="terms" rows="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-5 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save Contract</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer() {
    document.getElementById('createDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('createDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDrawer() {
    document.getElementById('createDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('createDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
</script>
@endpush
