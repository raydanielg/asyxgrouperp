@extends('layouts.admin')
@section('title', 'Leaves - ' . config('app.name'))
@section('page_title', 'Leave Management')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $leaves->total() }} requests</span>
        <button type="button" onclick="openLeaveDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Leave Request</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Employee</th>
                    <th class="px-5 py-3 font-semibold">Type</th>
                    <th class="px-5 py-3 font-semibold">Dates</th>
                    <th class="px-5 py-3 font-semibold">Days</th>
                    <th class="px-5 py-3 font-semibold">Reason</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($leaves as $l)
            @php $lc = ['approved' => '#10b981', 'rejected' => '#ef4444', 'pending' => '#f59e0b'][$l->status] ?? '#9ca3af'; @endphp
            <tr class="hover:bg-gray-50/60">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-[10px] uppercase">{{ substr($l->employee?->first_name ?? '?', 0, 1) }}{{ substr($l->employee?->last_name ?? '', 0, 1) }}</div>
                        <div>
                            <p class="text-xs font-medium text-gray-900">{{ $l->employee?->full_name ?? 'N/A' }}</p>
                            <p class="text-[10px] font-mono text-gray-400">{{ $l->employee?->employee_id ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5 text-xs text-gray-600">{{ ucfirst($l->leave_type) }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $l->start_date->format('d M') }} – {{ $l->end_date->format('d M Y') }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-700">{{ $l->days }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-400 max-w-[220px] truncate" title="{{ $l->reason }}">{{ $l->reason ?? '—' }}</td>
                <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $lc }}"></span>{{ ucfirst($l->status) }}</span></td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-4">
                        @if($l->status == 'pending')
                        <form method="POST" action="{{ route('admin.leaves.approve', $l) }}" class="inline" onsubmit="return confirmAction(this, {title:'Approve leave?', text:'Approve {{ $l->days }} day(s) of {{ $l->leave_type }} leave for {{ $l->employee?->full_name ?? 'this employee' }}?', icon:'question', confirmText:'Yes, approve', color:'#059669'})">@csrf @method('PATCH')
                            <button type="submit" class="text-xs font-medium text-gray-600 hover:text-gray-900">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.leaves.reject', $l) }}" class="inline" onsubmit="return confirmAction(this, {title:'Reject leave?', text:'Reject this leave request?', icon:'warning', confirmText:'Yes, reject', color:'#d97706'})">@csrf @method('PATCH')
                            <button type="submit" class="text-xs font-medium text-amber-600 hover:text-amber-800">Reject</button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('admin.leaves.destroy', $l) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete request?', text:'This leave request will be permanently removed.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No leave requests</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($leaves->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $leaves->links() }}</div>
    @endif
</div>

{{-- ═══ New Leave Drawer ═══ --}}
<div id="leaveDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeLeaveDrawer()"></div>
    <div id="leaveDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Leave Request</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Submit on behalf of an employee</p>
            </div>
            <button type="button" onclick="closeLeaveDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.leaves.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select...</option>
                        @foreach($employees as $e)
                        <option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="annual">Annual</option><option value="sick">Sick</option><option value="casual">Casual</option><option value="maternity">Maternity</option><option value="unpaid">Unpaid</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date <span class="text-red-500">*</span></label><input name="start_date" type="date" required id="leaveStart" oninput="calcDays()" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date <span class="text-red-500">*</span></label><input name="end_date" type="date" required id="leaveEnd" oninput="calcDays()" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Days <span class="text-red-500">*</span></label>
                    <input name="days" type="number" required id="leaveDays" value="1" min="1" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Reason</label>
                    <textarea name="reason" rows="3" placeholder="Optional reason..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Submit Request</button>
                <button type="button" onclick="closeLeaveDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openLeaveDrawer() {
    document.getElementById('leaveDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('leaveDrawerPanel').classList.remove('-translate-x-full'));
}
function closeLeaveDrawer() {
    document.getElementById('leaveDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('leaveDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLeaveDrawer(); });

function calcDays() {
    var s = document.getElementById('leaveStart').value;
    var e = document.getElementById('leaveEnd').value;
    if (s && e) {
        var diff = Math.ceil((new Date(e) - new Date(s)) / (1000 * 60 * 60 * 24)) + 1;
        document.getElementById('leaveDays').value = diff > 0 ? diff : 1;
    }
}
</script>
@endpush
@endsection
