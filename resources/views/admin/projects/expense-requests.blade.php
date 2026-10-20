@extends('layouts.admin')
@section('title', $project->title . ' - Expense Requests - ' . config('app.name'))
@section('page_title', 'Project Expense Requests')
@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.projects.show', $project) }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Project</a>
    <button type="button" onclick="openDrawer()" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">New Request</button>
</div>

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

@php
    $totalRequested = $requests->sum('amount');
    $pendingCount = $requests->where('approval_status', 'pending')->count();
    $approvedCount = $requests->where('approval_status', 'approved')->count();
    $statusColors = ['approved' => '#10b981', 'rejected' => '#ef4444', 'pending' => '#f59e0b'];
@endphp

{{-- Header + summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-base font-bold text-gray-900">{{ $project->title }} — Expense Requests</h3>
        <p class="text-[11px] text-gray-400 mt-0.5">Employee-submitted expense requests against this project</p>
    </div>
    <div class="grid grid-cols-3 divide-x divide-gray-100">
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Requested</p><p class="text-lg font-bold text-gray-900 mt-1">TZS {{ number_format($totalRequested, 2) }}</p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Pending</p><p class="text-lg font-bold text-amber-600 mt-1">{{ $pendingCount }}</p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Approved</p><p class="text-lg font-bold text-emerald-600 mt-1">{{ $approvedCount }}</p></div>
    </div>
</div>

{{-- Requests table --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Requests</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Request #</th>
                    <th class="px-5 py-3 font-semibold">Employee</th>
                    <th class="px-5 py-3 font-semibold">Category</th>
                    <th class="px-5 py-3 font-semibold">Description</th>
                    <th class="px-5 py-3 font-semibold text-right">Amount</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $req)
                @php $rsc = $statusColors[$req->approval_status] ?? '#9ca3af'; @endphp
                <tr class="hover:bg-gray-50/60">
                    <td class="px-5 py-3.5 font-mono text-xs font-medium text-gray-900">{{ $req->request_number }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-700">{{ $req->employee?->first_name }} {{ $req->employee?->last_name }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">{{ $req->category ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-500 max-w-[200px] truncate">{{ $req->description }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono font-medium text-gray-900">{{ number_format($req->amount, 2) }}</td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                            <span style="width:6px;height:6px;border-radius:99px;background:{{ $rsc }}"></span>
                            {{ ucfirst($req->approval_status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        @if($req->approval_status === 'pending')
                        <div class="flex items-center justify-end gap-3">
                            <form method="POST" action="{{ route('admin.projects.expense-requests.approve', $req) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Approve request {{ $req->request_number }}?', text: 'TZS {{ number_format($req->amount, 2) }} for {{ $req->employee?->first_name }} {{ $req->employee?->last_name }}', confirmText: 'Approve'})">
                                @csrf
                                <button class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.projects.expense-requests.reject', $req) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Reject request {{ $req->request_number }}?', text: 'This request will be marked as rejected', confirmText: 'Reject', color: '#dc2626'})">
                                @csrf
                                <button class="text-xs font-medium text-gray-400 hover:text-red-600">Reject</button>
                            </form>
                        </div>
                        @elseif($req->approval_status === 'rejected' && $req->rejection_reason)
                        <span class="text-[10px] text-gray-400 italic" title="{{ $req->rejection_reason }}">{{ \Illuminate\Support\Str::limit($req->rejection_reason, 25) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No expense requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $requests->links() }}</div>
    @endif
</div>

{{-- New Request Drawer --}}
<div id="expenseDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="expenseDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Expense Request</h3><p class="text-[11px] text-slate-400 mt-0.5">{{ $project->title }}</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.projects.expense-requests.store', $project) }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select employee…</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount (TZS) <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" min="1" required placeholder="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><input name="category" placeholder="e.g. Transport" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description <span class="text-red-500">*</span></label><textarea name="description" required rows="3" placeholder="What is this expense for..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <p class="text-[11px] text-gray-400 leading-relaxed">Request goes for approval before funds are released.</p>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Submit Request</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer() {
    document.getElementById('expenseDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('expenseDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDrawer() {
    document.getElementById('expenseDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('expenseDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
</script>
@endpush
