@extends('layouts.admin')
@section('title', 'Petty Cash - ' . config('app.name'))
@section('page_title', 'Petty Cash')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Petty cash floats and their custodians</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Petty Cash Account
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($accounts as $a)
    <a href="{{ route('admin.petty-cash.show', $a) }}" class="bg-white rounded-xl border p-5 hover:border-emerald-400 transition-colors">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3v-3m-3 3v-3m9-7H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2z"/></svg>
            </div>
            <span class="text-[10px] font-mono text-gray-400">{{ $a->code }}</span>
        </div>
        <h3 class="text-sm font-bold text-gray-900">{{ $a->name }}</h3>
        <p class="text-xs text-gray-500 mb-3">Custodian: {{ $a->custodian?->name ?? 'Unassigned' }}</p>
        <div class="border-t pt-3">
            <p class="text-[10px] text-gray-400 uppercase tracking-wide">Current Balance</p>
            <p class="text-xl font-bold text-emerald-700">TZS {{ number_format($a->current_balance, 2) }}</p>
        </div>
    </a>
    @empty
    <div class="col-span-full text-center py-8 text-gray-400 text-sm">No petty cash accounts yet</div>
    @endforelse
</div>

<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Petty Cash Account</h3>
        <form method="POST" action="{{ route('admin.petty-cash.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" required placeholder="e.g. Front Office Petty Cash" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Custodian</label>
                <select name="custodian_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                    <option value="">Unassigned</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Opening Balance</label><input name="opening_balance" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
        </form>
    </div>
</div>
@endsection
