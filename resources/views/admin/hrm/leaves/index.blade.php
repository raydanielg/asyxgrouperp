@extends('layouts.admin')
@section('title', 'Leaves - ' . config('app.name'))
@section('page_title', 'Leave Management')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track and manage employee leave requests</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Leave Request
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Employee</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Dates</th><th class="px-5 py-3 font-medium">Days</th><th class="px-5 py-3 font-medium">Reason</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($leaves as $l)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $l->employee?->full_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ ucfirst($l->leave_type) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $l->start_date->format('d M') }} - {{ $l->end_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $l->days }}</td>
            <td class="px-5 py-3 text-xs text-gray-400 max-w-xs truncate">{{ $l->reason ?? '—' }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($l->status=='approved') ? 'bg-emerald-50 text-emerald-700' : (($l->status=='rejected') ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($l->status) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-2">
        @if($l->status=='pending')
        <form method="POST" action="{{ route('admin.leaves.approve', $l) }}">@csrf @method('PATCH')<button class="text-emerald-600 hover:text-emerald-700 text-xs">Approve</button></form><form method="POST" action="{{ route('admin.leaves.reject', $l) }}">@csrf @method('PATCH')<button class="text-amber-600 hover:text-amber-700 text-xs">Reject</button></form>
        @endif
                <form id="del-leave-{{ $l->id }}" method="POST" action="{{ route('admin.leaves.destroy', $l) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-leave-{{ $l->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
            </td>
        
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No leave requests</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $leaves->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Leave Request</h3>
        <form method="POST" action="{{ route('admin.leaves.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Employee *</label><select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>
        @foreach($employees as $e)
        <option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>
        @endforeach
        </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Leave Type *</label><select name="leave_type" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="annual">Annual</option><option value="sick">Sick</option><option value="casual">Casual</option><option value="maternity">Maternity</option><option value="unpaid">Unpaid</option></select></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date *</label><input name="start_date" type="date" required id="leaveStart" oninput="calcDays()" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">End Date *</label><input name="end_date" type="date" required id="leaveEnd" oninput="calcDays()" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Days *</label><input name="days" type="number" required id="leaveDays" value="1" min="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Reason</label><textarea name="reason" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Submit</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
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
