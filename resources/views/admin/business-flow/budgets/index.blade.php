@extends('layouts.admin')
@section('title', 'Project Budgets - ' . config('app.name'))
@section('page_title', 'Project Budgets & Approval')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-xs text-gray-400">Create and approve project budgets before procurement</p>
    <button type="button" onclick="openBudDrawer()" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Budget
    </button>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left border-b border-gray-200 bg-gray-50/50">
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Budget No.</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Project</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Total Budget</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Procurement</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Office Exp.</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Approved By</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($budgets as $b)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $b->budget_number }}</td>
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $b->project?->title ?? 'N/A' }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-900">{{ number_format($b->total_budget) }} <span class="text-gray-400">TZS</span></td>
            <td class="px-5 py-3.5 text-xs text-right text-gray-500">{{ number_format($b->procurement_budget) }}</td>
            <td class="px-5 py-3.5 text-xs text-right text-gray-500">{{ number_format($b->office_expense_budget) }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $b->approvedBy?->name ?? '—' }}</td>
            <td class="px-5 py-3.5">
                @php $bd=['approved'=>'#10b981','rejected'=>'#ef4444','pending'=>'#f59e0b'][$b->status] ?? '#9ca3af'; @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $bd }}"></span>{{ ucfirst($b->status) }}</span>
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                @if($b->status === 'pending')
                <form method="POST" action="{{ route('admin.budgets.approve', $b) }}" class="inline" onsubmit="return confirmAction(this, {title:'Approve Budget?', text:'{{ $b->budget_number }} (TZS {{ number_format($b->total_budget) }}) will be approved and applied to the project.', icon:'question', confirmText:'Yes, approve', color:'#059669'})">@csrf<button class="text-xs font-medium text-gray-600 hover:text-gray-900">Approve</button></form>
                <form method="POST" action="{{ route('admin.budgets.reject', $b) }}" class="inline" onsubmit="return confirmAction(this, {title:'Reject Budget?', text:'{{ $b->budget_number }} will be marked as rejected.', icon:'warning', confirmText:'Yes, reject', color:'#d97706'})">@csrf<button class="text-xs font-medium text-amber-600 hover:text-amber-800">Reject</button></form>
                @endif
                <form method="POST" action="{{ route('admin.budgets.destroy', $b) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Budget?', text:'{{ $b->budget_number }} will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No budgets found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($budgets->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $budgets->links() }}</div>
    @endif
</div>

{{-- ═══ Create Budget Drawer (slides from left) ═══ --}}
<div id="budDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeBudDrawer()"></div>
    <div id="budDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Project Budget</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Submit a budget for approval</p>
            </div>
            <button type="button" onclick="closeBudDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.budgets.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Project</p>
            <div class="mb-5">
                <select name="project_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    <option value="">Select project...</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->title }} ({{ $p->project_number }})</option>
                    @endforeach
                </select>
            </div>

            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Budget Breakdown (TZS)</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Total Budget <span class="text-red-500">*</span></label>
                    <input name="total_budget" type="number" required min="0" step="0.01" placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Procurement</label>
                        <input name="procurement_budget" type="number" min="0" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Office Expenses</label>
                        <input name="office_expense_budget" type="number" min="0" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Labor</label>
                        <input name="labor_budget" type="number" min="0" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Contingency</label>
                        <input name="contingency" type="number" min="0" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                <textarea name="notes" rows="3" placeholder="Budget justification, scope notes..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Budget</button>
                <button type="button" onclick="closeBudDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openBudDrawer() {
    const d = document.getElementById('budDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('budDrawerPanel').classList.remove('-translate-x-full'));
}
function closeBudDrawer() {
    const p = document.getElementById('budDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('budDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeBudDrawer(); });
</script>
@endsection
