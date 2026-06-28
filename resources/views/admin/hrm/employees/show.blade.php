@extends('layouts.admin')
@section('title', 'Employee Details - ' . config('app.name'))
@section('page_title', 'Employee Details')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.employees.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Employees</a>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Profile Card --}}
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xl">{{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name ?? '', 0, 1)) }}</div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">{{ $employee->full_name }}</h3>
                <p class="text-xs text-gray-500">{{ $employee->designation ?? 'N/A' }}</p>
                <p class="text-xs text-gray-400">{{ $employee->department ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-gray-400">Employee ID</span><span class="font-mono text-gray-700">{{ $employee->employee_id }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Email</span><span class="text-gray-700">{{ $employee->email }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Phone</span><span class="text-gray-700">{{ $employee->phone ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Gender</span><span class="text-gray-700">{{ ucfirst($employee->gender ?? 'N/A') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Nationality</span><span class="text-gray-700">{{ $employee->nationality ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Employment Type</span><span class="text-gray-700">{{ $employee->employment_type ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Salary</span><span class="font-semibold text-gray-900">TZS {{ number_format($employee->salary ?? 0) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Joining Date</span><span class="text-gray-700">{{ $employee->joining_date?->format('d M Y') ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Manager</span><span class="text-gray-700">{{ $employee->manager?->full_name ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">{{ ucfirst(str_replace('_', ' ', $employee->status)) }}</span></div>
        </div>
        @if($employee->address)
        <div class="mt-3 pt-3 border-t"><p class="text-[10px] text-gray-400 uppercase mb-1">Address</p><p class="text-xs text-gray-600">{{ $employee->address }}</p></div>
        @endif
        <div class="mt-4 flex gap-2">
            <a href="{{ route('admin.employees.edit', $employee) }}" class="flex-1 px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 text-center">Edit</a>
            <form id="del-emp-{{ $employee->id }}" method="POST" action="{{ route('admin.employees.destroy', $employee) }}">@csrf @method('DELETE')</form>
            <button onclick="confirmDelete('del-emp-{{ $employee->id }}')" class="flex-1 px-3 py-2 border border-red-200 text-red-600 text-xs font-medium rounded-lg hover:bg-red-50">Delete</button>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="lg:col-span-2 space-y-4">
        {{-- Assigned Tasks --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between border-b pb-3 mb-3">
                <h3 class="text-sm font-bold text-gray-900">Assigned Tasks</h3>
                <span class="text-[10px] text-gray-400">{{ $assignedTasks->count() }} tasks</span>
            </div>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">Task</th><th class="py-2">Project</th><th class="py-2">Priority</th><th class="py-2">Status</th><th class="py-2">Due Date</th></tr></thead>
                <tbody>
        @forelse($assignedTasks as $t)
        <tr class="border-t border-gray-100">
                    <td class="py-2 text-gray-700 font-medium">{{ $t->title }}</td>
                    <td class="py-2 text-gray-500">{{ $t->project?->name ?? 'N/A' }}</td>
                    <td class="py-2">@php $pc=['low'=>'gray','medium'=>'amber','high'=>'red']; $pc=$pc[$t->priority]??'gray'; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-{{ $pc }}-50 text-{{ $pc }}-700">{{ ucfirst($t->priority) }}</span></td>
                    <td class="py-2">@php $sc=['todo'=>'gray','in_progress'=>'sky','done'=>'emerald','blocked'=>'red']; $sc=$sc[$t->status]??'gray'; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-{{ $sc }}-50 text-{{ $sc }}-700">{{ ucfirst(str_replace('_',' ',$t->status)) }}</span></td>
                    <td class="py-2 text-gray-500">{{ $t->due_date?->format('d M Y') ?? '—' }}</td>
                
        </tr>
        @empty
        <tr><td colspan="5" class="py-4 text-center text-gray-400">No assigned tasks</td></tr>
        @endforelse
        </tbody>
            </table></div>
        </div>

        {{-- Performance Reviews --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between border-b pb-3 mb-3">
                <h3 class="text-sm font-bold text-gray-900">Performance Reviews</h3>
                <span class="text-[10px] text-gray-400">{{ $performanceReviews->count() }} reviews</span>
            </div>
            <div class="space-y-3">
        @forelse($performanceReviews as $r)
        <div class="border rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-gray-900">Review Period: {{ $r->review_period ?? 'N/A' }}</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 text-emerald-700">Rating: {{ $r->rating }}/5</span>
                    </div>
                    <p class="text-xs text-gray-500">{{ $r->comments ?? 'No comments' }}</p>
                </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-4">No performance reviews</p>
        @endforelse
        </div>
        </div>

        {{-- Recent Attendance --}}
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Recent Attendance</h3>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">Date</th><th class="py-2">Check In</th><th class="py-2">Check Out</th><th class="py-2">Status</th></tr></thead>
                <tbody>
        @forelse($employee->attendances as $a)
        <tr class="border-t border-gray-100"><td class="py-2 text-gray-700">{{ $a->date->format('d M Y') }}</td><td class="py-2 text-gray-500">{{ $a->check_in ?? '—' }}</td><td class="py-2 text-gray-500">{{ $a->check_out ?? '—' }}</td><td class="py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 text-emerald-700">{{ ucfirst($a->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="4" class="py-4 text-center text-gray-400">No attendance records</td></tr>
        @endforelse
        </tbody>
            </table></div>
        </div>

        {{-- Payroll History --}}
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Payroll History</h3>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">Number</th><th class="py-2">Month</th><th class="py-2">Net Salary</th><th class="py-2">Status</th></tr></thead>
                <tbody>
        @forelse($employee->payrolls as $p)
        <tr class="border-t border-gray-100"><td class="py-2 font-mono text-gray-700">{{ $p->payroll_number }}</td><td class="py-2 text-gray-500">{{ $p->month }} {{ $p->year }}</td><td class="py-2 font-semibold text-gray-900">TZS {{ number_format($p->net_salary) }}</td><td class="py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-amber-50 text-amber-700">{{ ucfirst($p->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="4" class="py-4 text-center text-gray-400">No payroll records</td></tr>
        @endforelse
        </tbody>
            </table></div>
        </div>

        {{-- Leave History --}}
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Leave History</h3>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">Type</th><th class="py-2">Dates</th><th class="py-2">Days</th><th class="py-2">Status</th></tr></thead>
                <tbody>
        @forelse($employee->leaves as $l)
        <tr class="border-t border-gray-100"><td class="py-2 text-gray-700">{{ ucfirst($l->leave_type) }}</td><td class="py-2 text-gray-500">{{ $l->start_date->format('d M') }} - {{ $l->end_date->format('d M Y') }}</td><td class="py-2 text-gray-700">{{ $l->days }}</td><td class="py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] {{ ($l->status=='approved') ? 'bg-emerald-50 text-emerald-700' : (($l->status=='rejected') ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($l->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="4" class="py-4 text-center text-gray-400">No leave records</td></tr>
        @endforelse
        </tbody>
            </table></div>
        </div>

        {{-- Assets --}}
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Assigned Assets</h3>
            <div class="overflow-x-auto"><table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500"><th class="py-2">Asset</th><th class="py-2">Serial</th><th class="py-2">Assigned Date</th><th class="py-2">Status</th></tr></thead>
                <tbody>
        @forelse($employee->assets as $a)
        <tr class="border-t border-gray-100"><td class="py-2 text-gray-700">{{ $a->name }}</td><td class="py-2 font-mono text-gray-500">{{ $a->serial_number ?? '—' }}</td><td class="py-2 text-gray-500">{{ $a->assigned_date?->format('d M Y') ?? '—' }}</td><td class="py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] bg-sky-50 text-sky-700">{{ ucfirst($a->status ?? 'assigned') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="4" class="py-4 text-center text-gray-400">No assets assigned</td></tr>
        @endforelse
        </tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
