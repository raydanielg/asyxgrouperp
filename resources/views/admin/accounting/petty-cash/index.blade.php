@extends('layouts.admin')
@section('title', 'Petty Cash - ' . config('app.name'))
@section('page_title', 'Petty Cash')
@section('content')
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Petty cash floats and their custodians</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Petty Cash Account</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Code</th><th class="px-5 py-3 font-semibold">Name</th><th class="px-5 py-3 font-semibold">Custodian</th><th class="px-5 py-3 font-semibold text-right">Balance</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($accounts as $a)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $a->code }}</td>
            <td class="px-5 py-3.5"><a href="{{ route('admin.petty-cash.show', $a) }}" class="text-xs font-medium text-gray-900 hover:text-gray-600">{{ $a->name }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $a->custodian?->name ?? 'Unassigned' }}</td>
            <td class="px-5 py-3.5 text-xs font-medium font-mono text-gray-900 text-right">{{ number_format($a->current_balance, 2) }}</td>
            <td class="px-5 py-3.5 text-right"><a href="{{ route('admin.petty-cash.show', $a) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a></td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-xs text-gray-400">No petty cash accounts yet</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

{{-- New Petty Cash Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Petty Cash Account</h3><p class="text-[11px] text-slate-400 mt-0.5">Cash float for small expenses</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.petty-cash.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" required placeholder="e.g. Front Office Petty Cash" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Custodian</label>
                    <select name="custodian_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— Unassigned —</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Opening Balance</label><input name="opening_balance" type="number" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create</button>
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
