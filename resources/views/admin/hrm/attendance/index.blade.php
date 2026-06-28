@extends('layouts.admin')
@section('title', 'Attendance - ' . config('app.name'))
@section('page_title', 'Attendance Management')
@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- ═══ Today's Stats ═══ --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><p class="text-2xl font-bold text-gray-800">{{ $todayPresent }}</p><p class="text-[10px] text-gray-400">Present</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><p class="text-2xl font-bold text-gray-800">{{ $todayLate }}</p><p class="text-[10px] text-gray-400">Late</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div><p class="text-2xl font-bold text-gray-800">{{ $todayAbsent }}</p><p class="text-[10px] text-gray-400">Absent</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div><p class="text-2xl font-bold text-gray-800">{{ $todayRemote }}</p><p class="text-[10px] text-gray-400">Remote</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            </div>
            <div><p class="text-2xl font-bold text-gray-800">{{ $currentlyClockedIn }}</p><p class="text-[10px] text-gray-400">Clocked In Now</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div><p class="text-2xl font-bold text-gray-800">{{ $notClockedIn }}</p><p class="text-[10px] text-gray-400">Not Clocked In</p></div>
        </div>
    </div>
</div>

{{-- ═══ Clock In / Clock Out Panel ═══ --}}
<div class="bg-white rounded-xl border p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-bold text-gray-800">Clock In / Clock Out</h3>
            <p class="text-xs text-gray-400 mt-0.5">Select employee and clock them in or out</p>
        </div>
        <div class="text-right">
            <p class="text-lg font-bold text-emerald-600" id="liveClock">{{ now()->format('H:i:s') }}</p>
            <p class="text-[10px] text-gray-400">{{ now()->format('d M Y, l') }}</p>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-3 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-medium text-gray-600 mb-1">Employee</label>
            <select id="clockEmployee" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                <option value="">— Select Employee —</option>
                @foreach($employees as $e)
                <option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>
                @endforeach
            </select>
        </div>
        <form method="POST" action="{{ route('admin.attendance.clock-in') }}">@csrf<input type="hidden" name="employee_id" id="clockInEmpId"><button type="submit" onclick="return setEmpId('clockInEmpId')" class="w-full md:w-auto px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            Clock In
        </button></form>
        <form method="POST" action="{{ route('admin.attendance.clock-out') }}">@csrf<input type="hidden" name="employee_id" id="clockOutEmpId"><button type="submit" onclick="return setEmpId('clockOutEmpId')" class="w-full md:w-auto px-5 py-2.5 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            Clock Out
        </button></form>
        <form method="POST" action="{{ route('admin.attendance.clock-out-all') }}">@csrf<button type="submit" onclick="return confirm('Clock out ALL employees still clocked in?')" class="w-full md:w-auto px-5 py-2.5 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Clock Out All
        </button></form>
    </div>
</div>

{{-- ═══ Filters ═══ --}}
<div class="bg-white rounded-xl border p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div><label class="block text-xs font-medium text-gray-600 mb-1">Date</label><input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
        <div><label class="block text-xs font-medium text-gray-600 mb-1">Employee</label><select name="employee_id" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"><option value="">All</option>@foreach($employees as $e)<option value="{{ $e->id }}" @selected(request('employee_id') == $e->id)>{{ $e->full_name }}</option>@endforeach</select></div>
        <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"><option value="">All</option><option value="present" @selected(request('status')=='present')>Present</option><option value="late" @selected(request('status')=='late')>Late</option><option value="absent" @selected(request('status')=='absent')>Absent</option><option value="remote" @selected(request('status')=='remote')>Remote</option><option value="half_day" @selected(request('status')=='half_day')>Half Day</option></select></div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Filter</button>
        <a href="{{ route('admin.attendance.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">Today</a>
    </form>
</div>

{{-- ═══ Attendance Table ═══ --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-3 border-b bg-gray-50/50 flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-700">Attendance Records — {{ request('date', date('Y-m-d')) }}</h3>
        <button onclick="document.getElementById('manualModal').classList.remove('hidden')" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">+ Add Manual Record</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/30">
            <th class="px-5 py-3 font-medium">Employee</th>
            <th class="px-5 py-3 font-medium">Date</th>
            <th class="px-5 py-3 font-medium">Clock In</th>
            <th class="px-5 py-3 font-medium">Clock Out</th>
            <th class="px-5 py-3 font-medium text-right">Work Hrs</th>
            <th class="px-5 py-3 font-medium text-right">OT</th>
            <th class="px-5 py-3 font-medium">Status</th>
            <th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($attendances as $a)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">{{ strtoupper(substr($a->employee?->first_name ?? '?', 0, 1)) }}</div>
                    <div>
                        <p class="text-xs font-medium text-gray-900">{{ $a->employee?->full_name ?? 'N/A' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $a->employee?->employee_id ?? '' }}</p>
                    </div>
                </div>
            </td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $a->date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs">
                @if($a->clock_in_at)
                <span class="text-emerald-600 font-medium">{{ $a->clock_in_at->format('H:i:s') }}</span>
                @else
                <span class="text-gray-300">{{ $a->check_in ?? '—' }}</span>
                @endif
            </td>
            <td class="px-5 py-3 text-xs">
                @if($a->clock_out_at)
                <span class="text-red-500 font-medium">{{ $a->clock_out_at->format('H:i:s') }}</span>
                @elseif($a->clock_in_at)
                <span class="text-amber-500 text-[10px] italic">Still working...</span>
                @else
                <span class="text-gray-300">{{ $a->check_out ?? '—' }}</span>
                @endif
            </td>
            <td class="px-5 py-3 text-xs text-right font-medium text-gray-700">{{ $a->formatted_work_hours }}</td>
            <td class="px-5 py-3 text-xs text-right @if($a->overtime_hours > 0) text-amber-600 font-medium @else text-gray-300 @endif">{{ $a->overtime_hours > 0 ? sprintf('%02d:%02d', floor($a->overtime_hours), ($a->overtime_hours - floor($a->overtime_hours)) * 60) : '00:00' }}</td>
            <td class="px-5 py-3">{!! $a->status_badge !!}</td>
            <td class="px-5 py-3">
                @if($a->clock_in_at && !$a->clock_out_at)
                <form method="POST" action="{{ route('admin.attendance.clock-out') }}" class="inline">@csrf<input type="hidden" name="employee_id" value="{{ $a->employee_id }}"><button class="text-xs text-red-500 hover:text-red-700 font-medium">Clock Out</button></form>
                @endif
                <form id="del-att-{{ $a->id }}" method="POST" action="{{ route('admin.attendance.destroy', $a) }}" class="inline">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-att-{{ $a->id }}')" class="text-xs text-gray-400 hover:text-red-500 ml-2">Delete</button>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-gray-400 text-xs">No attendance records for this date</p>
        </td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $attendances->links() }}</div>
</div>

{{-- ═══ Manual Entry Modal ═══ --}}
<div id="manualModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Manual Attendance Record</h3>
        <form method="POST" action="{{ route('admin.attendance.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Employee *</label><select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Date *</label><input name="date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Check In</label><input name="check_in" type="time" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Check Out</label><input name="check_out" type="time" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status *</label><select name="status" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="present">Present</option><option value="absent">Absent</option><option value="late">Late</option><option value="half_day">Half Day</option><option value="remote">Remote</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Note</label><textarea name="note" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('manualModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save</button></div>
        </form>
    </div>
</div>

<script>
function setEmpId(hiddenId) {
    const empId = document.getElementById('clockEmployee').value;
    if (!empId) { alert('Please select an employee first.'); return false; }
    document.getElementById(hiddenId).value = empId;
    return true;
}
// Live clock
setInterval(function() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('liveClock');
    if (el) el.textContent = h + ':' + m + ':' + s;
}, 1000);
</script>
@endsection
