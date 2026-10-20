@extends('layouts.admin')
@section('title', $contract->contract_number . ' - ' . config('app.name'))
@section('page_title', 'Contract Details')
@section('content')
@php
    $sc = ['active' => '#10b981', 'completed' => '#3b82f6', 'terminated' => '#ef4444', 'draft' => '#f59e0b'][$contract->status] ?? '#9ca3af';
@endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.contracts.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Contracts</a>
    <div class="flex items-center gap-2">
        <button onclick="openDrawer()" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Edit Contract</button>
        <form method="POST" action="{{ route('admin.contracts.destroy', $contract) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete this contract?', color: '#dc2626'})">
            @csrf @method('DELETE')
            <button class="px-3 py-1.5 border border-gray-200 text-red-600 text-xs font-medium rounded hover:bg-red-50 hover:border-red-200">Delete</button>
        </form>
    </div>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm font-bold text-gray-900">{{ $contract->contract_number }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc }}"></span>{{ ucfirst($contract->status) }}</span>
                <span class="text-xs text-gray-400 capitalize">{{ $contract->type }}</span>
            </div>
            <h1 class="text-base font-bold text-gray-900 mt-1.5">{{ $contract->title }}</h1>
            <p class="text-[11px] text-gray-400 mt-1">{{ $contract->contractor_name }} · Created {{ $contract->created_at->format('d M Y') }} by {{ $contract->createdBy?->name ?? 'N/A' }}</p>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Contract Value</p>
            <p class="text-xl font-bold text-gray-900 mt-0.5">TZS {{ number_format($contract->contract_value) }}</p>
        </div>
    </div>
    <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-xs">
        <div><p class="text-gray-400">Contractor</p><p class="text-gray-800 mt-0.5">{{ $contract->contractor_name }}</p></div>
        <div><p class="text-gray-400">Type</p><p class="text-gray-800 mt-0.5 capitalize">{{ $contract->type }}</p></div>
        <div><p class="text-gray-400">Start Date</p><p class="text-gray-800 mt-0.5">{{ $contract->start_date->format('d M Y') }}</p></div>
        <div><p class="text-gray-400">End Date</p><p class="mt-0.5 {{ $contract->end_date && $contract->end_date->isPast() && $contract->status === 'active' ? 'text-red-600 font-medium' : 'text-gray-800' }}">{{ $contract->end_date?->format('d M Y') ?? '—' }}</p></div>
        @if($contract->project)
        <div><p class="text-gray-400">Linked Project</p><a href="{{ route('admin.projects.show', $contract->project) }}" class="text-gray-800 mt-0.5 block font-medium hover:text-gray-600">{{ $contract->project->title }}</a></div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    @if($contract->description)
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Description</h3></div>
        <div class="px-5 py-4"><p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $contract->description }}</p></div>
    </div>
    @endif
    @if($contract->terms)
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden {{ $contract->description ? '' : 'lg:col-span-2' }}">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Terms &amp; Conditions</h3></div>
        <div class="px-5 py-4"><p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $contract->terms }}</p></div>
    </div>
    @endif
</div>

{{-- Edit Contract Drawer --}}
<div id="editDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="editDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-xl bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Edit Contract</h3><p class="text-[11px] text-slate-400 mt-0.5">{{ $contract->contract_number }}</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.contracts.update', $contract) }}" class="flex-1 overflow-y-auto p-5">
            @csrf @method('PUT')
            <div class="mb-5">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Details</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" value="{{ $contract->title }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Contractor Name <span class="text-red-500">*</span></label><input name="contractor_name" value="{{ $contract->contractor_name }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            @foreach(['service','supply','construction','maintenance','consultancy','other'] as $t)
                            <option value="{{ $t }}" {{ $contract->type === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Contract Value <span class="text-red-500">*</span></label><input name="contract_value" type="number" step="0.01" min="0" value="{{ $contract->contract_value }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            @foreach(['draft','active','completed','terminated'] as $s)
                            <option value="{{ $s }}" {{ $contract->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-5 pt-5 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Period &amp; Project</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date <span class="text-red-500">*</span></label><input name="start_date" type="date" value="{{ $contract->start_date->format('Y-m-d') }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input name="end_date" type="date" value="{{ $contract->end_date?->format('Y-m-d') ?? '' }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Project</label>
                        <select name="project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            <option value="">— None —</option>
                            @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ $contract->project_id === $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="pt-5 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Notes</h4>
                <div class="space-y-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ $contract->description }}</textarea></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Terms &amp; Conditions</label><textarea name="terms" rows="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ $contract->terms }}</textarea></div>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-5 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Update Contract</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer() {
    document.getElementById('editDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('editDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDrawer() {
    document.getElementById('editDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('editDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
</script>
@endpush
