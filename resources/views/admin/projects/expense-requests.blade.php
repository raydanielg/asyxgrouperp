@extends('layouts.admin')
@section('title', $project->title . ' - Expense Requests - ' . config('app.name'))
@section('page_title', 'Project Expense Requests')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.projects.show', $project) }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to {{ $project->title }}</a>
</div>

<div class="flex items-center justify-between mb-4">
    <div>
        <h3 class="text-sm font-bold text-gray-900">{{ $project->title }} &mdash; Expense Requests</h3>
        <p class="text-xs text-gray-500">Employee-submitted expense requests against this project</p>
    </div>
    <button onclick="document.getElementById('expenseRequestModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ New Request</button>
</div>

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3 mb-4 text-sm text-emerald-800">{{ session('success') }}</div>
@endif

@php
    $totalRequested = $requests->sum('amount');
    $pendingCount = $requests->where('approval_status', 'pending')->count();
    $approvedCount = $requests->where('approval_status', 'approved')->count();
@endphp

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Total Requested</p>
        <p class="text-xl font-bold text-gray-900 mt-1">TZS {{ number_format($totalRequested, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-amber-200 p-4">
        <p class="text-[10px] text-amber-600 uppercase tracking-wide">Pending</p>
        <p class="text-xl font-bold text-amber-700 mt-1">{{ $pendingCount }}</p>
    </div>
    <div class="bg-white rounded-xl border border-emerald-200 p-4">
        <p class="text-[10px] text-emerald-600 uppercase tracking-wide">Approved</p>
        <p class="text-xl font-bold text-emerald-700 mt-1">{{ $approvedCount }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-3 border-b flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-900">Requests</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Request #</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Employee</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Description</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $req)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-900">{{ $req->request_number }}</td>
                    <td class="px-4 py-3 text-xs text-gray-700">{{ $req->employee?->first_name }} {{ $req->employee?->last_name }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $req->category ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600 max-w-[200px] truncate">{{ $req->description }}</td>
                    <td class="px-4 py-3 text-xs text-right font-semibold text-gray-900">TZS {{ number_format($req->amount, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{
                            $req->approval_status === 'approved' ? 'bg-emerald-50 text-emerald-700' :
                            ($req->approval_status === 'rejected' ? 'bg-rose-50 text-rose-700' :
                            'bg-amber-50 text-amber-700')
                        }}">{{ ucfirst($req->approval_status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($req->approval_status === 'pending')
                        <div class="flex items-center justify-end gap-1">
                            <form method="POST" action="{{ route('admin.projects.expense-requests.approve', $req) }}" class="inline">
                                @csrf
                                <button class="px-2 py-1 text-[10px] font-semibold text-emerald-600 hover:text-emerald-700 border border-emerald-200 rounded hover:bg-emerald-50">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.projects.expense-requests.reject', $req) }}" class="inline" onsubmit="return confirm('Reject this request?')">
                                @csrf
                                <button class="px-2 py-1 text-[10px] font-semibold text-rose-600 hover:text-rose-700 border border-rose-200 rounded hover:bg-rose-50">Reject</button>
                            </form>
                        </div>
                        @elseif($req->approval_status === 'rejected' && $req->rejection_reason)
                        <span class="text-[10px] text-rose-500 italic" title="{{ $req->rejection_reason }}">{{ \Illuminate\Support\Str::limit($req->rejection_reason, 20) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">No expense requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t">{{ $requests->links() }}</div>
</div>

<div id="expenseRequestModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Expense Request</h3>
        <form method="POST" action="{{ route('admin.projects.expense-requests.store', $project) }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Employee *</label>
                <select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none">
                    <option value="">Select employee…</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" min="1" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><input name="category" placeholder="e.g. Transport" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description *</label><textarea name="description" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 outline-none" rows="3"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('expenseRequestModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Submit Request</button></div>
        </form>
    </div>
</div>
@endsection
