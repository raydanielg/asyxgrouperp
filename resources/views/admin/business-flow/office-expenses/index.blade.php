@extends('layouts.admin')
@section('title', 'Office Expenses - ' . config('app.name'))
@section('page_title', 'Office Expenses')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Submit and approve office expenses with workflow</p>
    <button onclick="document.getElementById('expenseModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Submit Expense
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Expense No.</th><th class="px-5 py-3 font-medium">Description</th><th class="px-5 py-3 font-medium">Project</th><th class="px-5 py-3 font-medium">Category</th><th class="px-5 py-3 font-medium">Amount</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Requested By</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>@forelse($expenses as $e)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $e->expense_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $e->description }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $e->project?->title ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ ucfirst($e->category ?? 'N/A') }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($e->amount) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $e->expense_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $e->requestedBy?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] @if($e->status==='approved')bg-emerald-50 text-emerald-700@elseif($e->status==='rejected')bg-red-50 text-red-700@elseif($e->status==='disbursed')bg-sky-50 text-sky-700@else bg-amber-50 text-amber-700@endif">{{ ucfirst($e->status) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-2">
                @if($e->status==='pending')<form method="POST" action="{{ route('admin.office-expenses.approve', $e) }">@csrf<button type="submit" class="text-emerald-600 hover:text-emerald-700 text-xs">Approve</button></form><form method="POST" action="{{ route('admin.office-expenses.reject', $e) }">@csrf<button type="submit" class="text-red-500 hover:text-red-700 text-xs">Reject</button></form>@endif
                <form id="del-oexp-{{ $e->id }}" method="POST" action="{{ route('admin.office-expenses.destroy', $e) }}">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-oexp-{{ $e->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
            </td>
        
        </tr>
        @empty
        <tr><td colspan="9" class="px-5 py-8 text-center text-gray-400 text-xs">No office expenses found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $expenses->links() }}</div>
</div>

<div id="expenseModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-xl w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-sm font-bold text-gray-900">Submit Office Expense</h3><button onclick="document.getElementById('expenseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button></div>
        <form method="POST" action="{{ route('admin.office-expenses.store') }}" class="p-6 space-y-4">@csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description *</label><input name="description" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label><select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"><option value="">No Project</option>@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><select name="category" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">@foreach(['transport'=>'Transport','supplies'=>'Supplies','meals'=>'Meals','utilities'=>'Utilities','misc'=>'Miscellaneous'] as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount (TZS) *</label><input name="amount" type="number" required min="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Expense Date *</label><input name="expense_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label><select name="payment_method" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">@foreach(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','mobile_money'=>'Mobile Money'] as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Receipt Number</label><input name="receipt_number" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></textarea></div>
            <div class="text-[10px] text-gray-400 bg-amber-50 border border-amber-200 rounded-lg p-2">Expenses under TZS 100,000 are auto-approved. Larger amounts require manual approval.</div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('expenseModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Submit Expense</button></div>
        </form>
    </div>
</div>
@endsection
