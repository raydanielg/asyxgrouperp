@extends('layouts.admin')
@section('title', $contract->contract_number . ' - ' . config('app.name'))
@section('page_title', 'Contract Details')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.contracts.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Contracts</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900">{{ $contract->title }}</h3>
                <p class="text-xs text-gray-500 font-mono">{{ $contract->contract_number }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{
                $contract->status === 'active' ? 'bg-emerald-50 text-emerald-700' :
                ($contract->status === 'completed' ? 'bg-sky-50 text-sky-700' :
                ($contract->status === 'terminated' ? 'bg-rose-50 text-rose-700' :
                'bg-amber-50 text-amber-700'))
            }}">{{ ucfirst($contract->status) }}</span>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4 text-sm">
                <div><span class="text-[10px] text-gray-400 uppercase">Contractor</span><p class="font-medium text-gray-900 mt-0.5">{{ $contract->contractor_name }}</p></div>
                <div><span class="text-[10px] text-gray-400 uppercase">Type</span><p class="font-medium text-gray-900 mt-0.5">{{ ucfirst($contract->type) }}</p></div>
                <div><span class="text-[10px] text-gray-400 uppercase">Start Date</span><p class="font-medium text-gray-900 mt-0.5">{{ $contract->start_date->format('d M Y') }}</p></div>
                <div><span class="text-[10px] text-gray-400 uppercase">End Date</span><p class="font-medium text-gray-900 mt-0.5">{{ $contract->end_date?->format('d M Y') ?? '—' }}</p></div>
            </div>
            <div class="mb-4">
                <span class="text-[10px] text-gray-400 uppercase">Contract Value</span>
                <p class="text-2xl font-bold text-emerald-700">TZS {{ number_format($contract->contract_value, 2) }}</p>
            </div>
            @if($contract->project)
            <div class="mb-4">
                <span class="text-[10px] text-gray-400 uppercase">Linked Project</span>
                <p class="font-medium text-gray-900 mt-0.5">
                    <a href="{{ route('admin.projects.show', $contract->project) }}" class="text-emerald-600 hover:text-emerald-700">{{ $contract->project->title }}</a>
                </p>
            </div>
            @endif
            @if($contract->description)
            <div class="mb-4">
                <span class="text-[10px] text-gray-400 uppercase">Description</span>
                <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $contract->description }}</p>
            </div>
            @endif
            @if($contract->terms)
            <div>
                <span class="text-[10px] text-gray-400 uppercase">Terms & Conditions</span>
                <div class="text-sm text-gray-700 mt-1 whitespace-pre-line bg-gray-50 border rounded-lg p-4">{{ $contract->terms }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h3 class="text-sm font-bold text-gray-800">Actions</h3></div>
        <div class="p-4 space-y-3">
            <button onclick="document.getElementById('editContractModal').classList.remove('hidden')" class="w-full px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700">Edit Contract</button>
            <form method="POST" action="{{ route('admin.contracts.destroy', $contract) }}" onsubmit="return confirm('Delete this contract?')">
                @csrf @method('DELETE')
                <button class="w-full px-4 py-2 border border-rose-200 text-rose-600 text-sm font-medium rounded-lg hover:bg-rose-50">Delete</button>
            </form>
            <div class="text-[10px] text-gray-400 pt-2 border-t">Created by {{ $contract->createdBy?->name ?? 'N/A' }}<br>{{ $contract->created_at->format('d M Y H:i') }}</div>
        </div>
    </div>
</div>

<div id="editContractModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6 my-8">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Contract</h3>
        <form method="POST" action="{{ route('admin.contracts.update', $contract) }}" class="space-y-3">@csrf @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" value="{{ $contract->title }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Contractor Name *</label><input name="contractor_name" value="{{ $contract->contractor_name }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Type *</label>
                    <select name="type" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none">
                        @foreach(['service','supply','construction','maintenance','consultancy','other'] as $t)
                        <option value="{{ $t }}" {{ $contract->type === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none">
                        @foreach(['draft','active','completed','terminated'] as $s)
                        <option value="{{ $s }}" {{ $contract->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none">
                        <option value="">None</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $contract->project_id === $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Contract Value *</label><input name="contract_value" type="number" step="0.01" min="0" value="{{ $contract->contract_value }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date *</label><input name="start_date" type="date" value="{{ $contract->start_date->format('Y-m-d') }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input name="end_date" type="date" value="{{ $contract->end_date?->format('Y-m-d') ?? '' }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none" rows="2">{{ $contract->description }}</textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Terms & Conditions</label><textarea name="terms" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none" rows="3">{{ $contract->terms }}</textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('editContractModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700">Update Contract</button></div>
        </form>
    </div>
</div>
@endsection
