@extends('layouts.admin')
@section('title', 'Project Budgets - ' . config('app.name'))
@section('page_title', 'Project Budgets & Approval')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Create and approve project budgets before procurement</p>
    <button onclick="document.getElementById('budgetModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Budget
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Budget No.</th><th class="px-5 py-3 font-medium">Project</th><th class="px-5 py-3 font-medium">Total Budget</th><th class="px-5 py-3 font-medium">Procurement</th><th class="px-5 py-3 font-medium">Office Exp.</th><th class="px-5 py-3 font-medium">Approved By</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>@forelse($budgets as $b)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $b->budget_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $b->project?->title ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($b->total_budget) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">TZS {{ number_format($b->procurement_budget) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">TZS {{ number_format($b->office_expense_budget) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $b->approvedBy?->name ?? '—' }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] @if($b->status==='approved')bg-emerald-50 text-emerald-700@elseif($b->status==='rejected')bg-red-50 text-red-700@else bg-amber-50 text-amber-700@endif">{{ ucfirst($b->status) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-2">
                @if($b->status==='pending')<form method="POST" action="{{ route('admin.budgets.approve', $b) }}">@csrf<button type="submit" class="text-emerald-600 hover:text-emerald-700 text-xs">Approve</button></form><form method="POST" action="{{ route('admin.budgets.reject', $b) }}">@csrf<button type="submit" class="text-red-500 hover:text-red-700 text-xs">Reject</button></form>@endif
                <form id="del-bud-{{ $b->id }}" method="POST" action="{{ route('admin.budgets.destroy', $b) }}">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-bud-{{ $b->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
            </td>
        </tr>@empty<tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No budgets found</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $budgets->links() }}</div>
</div>

<div id="budgetModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-xl w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Create Project Budget</h3><button onclick="document.getElementById('budgetModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button></div>
        <form method="POST" action="{{ route('admin.budgets.store') }}" class="p-6 space-y-4">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Project *</label><select name="project_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">Select Project...</option>@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach</select></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Total Budget (TZS) *</label><input name="total_budget" type="number" required min="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Procurement Budget</label><input name="procurement_budget" type="number" min="0" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Office Expense Budget</label><input name="office_expense_budget" type="number" min="0" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Labor Budget</label><input name="labor_budget" type="number" min="0" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Contingency</label><input name="contingency" type="number" min="0" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('budgetModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Budget</button></div>
        </form>
    </div>
</div>
@endsection
