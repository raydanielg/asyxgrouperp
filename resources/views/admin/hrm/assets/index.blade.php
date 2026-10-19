@extends('layouts.admin')
@section('title', 'Employee Assets - ' . config('app.name'))
@section('page_title', 'Employee Assets')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $assets->total() }} assigned assets</span>
        <button type="button" onclick="openAstDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Assign Asset</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Asset</th>
                    <th class="px-5 py-3 font-semibold">Employee</th>
                    <th class="px-5 py-3 font-semibold">Type</th>
                    <th class="px-5 py-3 font-semibold">Serial #</th>
                    <th class="px-5 py-3 font-semibold">Assigned</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($assets as $a)
            @php $ac = ['assigned' => '#10b981', 'returned' => '#9ca3af', 'lost' => '#ef4444'][$a->status] ?? '#9ca3af'; @endphp
            <tr class="hover:bg-gray-50/60">
                <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $a->asset_name }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-700">{{ $a->employee?->full_name ?? 'N/A' }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $a->asset_type ?? '—' }}</td>
                <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $a->serial_number ?? '—' }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-400">{{ $a->assigned_date?->format('d M Y') ?? '—' }}</td>
                <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $ac }}"></span>{{ ucfirst($a->status) }}</span></td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end">
                        <form method="POST" action="{{ route('admin.assets.destroy', $a) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete asset?', text:'{{ addslashes($a->asset_name) }} assignment will be permanently removed.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No assets assigned</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($assets->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $assets->links() }}</div>
    @endif
</div>

<div id="astDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeAstDrawer()"></div>
    <div id="astDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">Assign Asset</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Issue equipment to an employee</p>
            </div>
            <button type="button" onclick="closeAstDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.assets.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select...</option>
                        @foreach($employees as $e)
                        <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Asset Name <span class="text-red-500">*</span></label><input name="asset_name" required placeholder="e.g. MacBook Pro 14&quot;" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Asset Type</label><input name="asset_type" placeholder="e.g. Laptop" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Serial Number</label><input name="serial_number" placeholder="SN..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 font-mono"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Assigned Date</label><input name="assigned_date" type="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Return Date</label><input name="return_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="assigned">Assigned</option><option value="returned">Returned</option><option value="lost">Lost</option></select></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Assign</button>
                <button type="button" onclick="closeAstDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAstDrawer() {
    document.getElementById('astDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('astDrawerPanel').classList.remove('-translate-x-full'));
}
function closeAstDrawer() {
    document.getElementById('astDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('astDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAstDrawer(); });
</script>
@endpush
@endsection
