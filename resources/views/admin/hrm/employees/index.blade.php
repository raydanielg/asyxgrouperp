@extends('layouts.admin')
@section('title', 'Employees - ' . config('app.name'))
@section('page_title', 'Employees')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage employee profiles, departments, and assignments</p>
    <a href="{{ route('admin.employees.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Employee
    </a>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] text-gray-400 uppercase">Total</p><p class="text-xl font-bold text-emerald-700">{{ $employees->total() }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] text-gray-400 uppercase">Departments</p><p class="text-xl font-bold text-sky-700">{{ count($departments) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] text-gray-400 uppercase">Active</p><p class="text-xl font-bold text-emerald-600">{{ \App\Models\Employee::where('status','active')->count() }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] text-gray-400 uppercase">On Leave</p><p class="text-xl font-bold text-amber-600">{{ \App\Models\Employee::where('status','on_leave')->count() }}</p></div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    {{-- Filters --}}
    <div class="px-5 py-4 border-b bg-gray-50/50">
        <form method="GET" action="{{ route('admin.employees.index') }}" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, ID..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-52">
            <select name="department" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <option value="">All Departments</option>
                @foreach($departments as $dept)<option value="{{ $dept }}" @selected(request('department')===$dept)>{{ $dept }}</option>@endforeach
            </select>
            <select name="status" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <option value="">All Status</option>
                @foreach(['active','inactive','on_leave','terminated'] as $st)<option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>@endforeach
            </select>
            <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700">Filter</button>
            @if(request('search')||request('department')||request('status'))<a href="{{ route('admin.employees.index') }}" class="text-xs text-gray-500 hover:text-gray-700">Clear</a>@endif
        </form>
    </div>
    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Employee ID</th>
                <th class="px-5 py-3 font-medium">Name</th>
                <th class="px-5 py-3 font-medium">Department</th>
                <th class="px-5 py-3 font-medium">Designation</th>
                <th class="px-5 py-3 font-medium">Type</th>
                <th class="px-5 py-3 font-medium">Salary</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($employees as $employee)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $employee->employee_id }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-[10px]">{{ strtoupper(substr($employee->first_name, 0, 1)) }}</div>
                            <div><p class="text-xs font-medium text-gray-900">{{ $employee->full_name }}</p><p class="text-[10px] text-gray-400">{{ $employee->email }}</p></div>
                        </div>
                    </td>
                    <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700">{{ $employee->department ?? 'N/A' }}</span></td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $employee->designation ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $employee->employment_type ?? '—' }}</td>
                    <td class="px-5 py-3 text-xs font-semibold text-gray-900">${{ number_format($employee->salary ?? 0, 2) }}</td>
                    <td class="px-5 py-3">
                        @php $colors = ['active'=>'emerald','inactive'=>'gray','terminated'=>'red','on_leave'=>'amber']; $color = $colors[$employee->status] ?? 'gray'; @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100">{{ ucfirst(str_replace('_', ' ', $employee->status)) }}</span>
                    </td>
                    <td class="px-5 py-3 flex items-center gap-2">
                        <a href="{{ route('admin.employees.show', $employee) }}" class="text-sky-600 hover:text-sky-700 text-xs">View</a>
                        <a href="{{ route('admin.employees.edit', $employee) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">Edit</a>
                        <form id="del-emp-{{ $employee->id }}" method="POST" action="{{ route('admin.employees.destroy', $employee) }}">@csrf @method('DELETE')</form>
                        <button onclick="confirmDelete('del-emp-{{ $employee->id }}', 'Delete employee?', 'This will permanently delete {{ $employee->full_name }}.')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No employees found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $employees->links() }}</div>
</div>
@endsection
