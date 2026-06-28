@extends('layouts.admin')
@section('title', 'Add Employee - ' . config('app.name'))
@section('page_title', 'Add Employee')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.employees.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Employees</a>
</div>
<form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-4">
    @csrf
    <div class="bg-white rounded-xl border p-6 space-y-4">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3">Personal Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Employee ID *</label><input name="employee_id" required value="{{ old('employee_id', 'EMP-' . strtoupper(\Illuminate\Support\Str::random(6))) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">First Name *</label><input name="first_name" required value="{{ old('first_name') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Last Name</label><input name="last_name" value="{{ old('last_name') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Email *</label><input name="email" type="email" required value="{{ old('email') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Gender</label><select name="gender" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>
        @foreach(['male'=>'Male','female'=>'Female','other'=>'Other'] as $k=>$v)
        <option value="{{ $k }}" @selected(old('gender')===$k)>{{ $v }}</option>
        @endforeach
        </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Date of Birth</label><input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Nationality</label><select name="nationality" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>
        @foreach(['Kenyan','Tanzanian','Ugandan','Nigerian','South African','American','British','Indian','Other'] as $nat)
        <option value="{{ $nat }}" @selected(old('nationality')===$nat)>{{ $nat }}</option>
        @endforeach
        </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Marital Status</label><select name="marital_status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>
        @foreach(['single'=>'Single','married'=>'Married','divorced'=>'Divorced','widowed'=>'Widowed'] as $k=>$v)
        <option value="{{ $k }}" @selected(old('marital_status')===$k)>{{ $v }}</option>
        @endforeach
        </select></div>
        </div>
        <div><label class="block text-xs font-medium text-gray-600 mb-1">Address</label><textarea name="address" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">{{ old('address') }}</textarea></div>
    </div>
    <div class="bg-white rounded-xl border p-6 space-y-4">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3">Employment Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Department *</label>
                <select name="department" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="">Select Department...</option>
        @foreach($departments as $dept)
        <option value="{{ $dept }}" @selected(old('department')===$dept)>{{ $dept }}</option>
        @endforeach
        </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Designation</label>
                <select name="designation" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="">Select Designation...</option>
        @foreach($designations as $des)
        <option value="{{ $des }}" @selected(old('designation')===$des)>{{ $des }}</option>
        @endforeach
        </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Reports To (Manager)</label>
                <select name="manager_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="">No Manager</option>
        @foreach($managers as $m)
        <option value="{{ $m->id }}" @selected(old('manager_id')==$m->id)>{{ $m->full_name }} ({{ $m->designation ?? 'N/A' }})</option>
        @endforeach
        </select>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Joining Date</label><input name="joining_date" type="date" value="{{ old('joining_date') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Employment Type</label>
                <select name="employment_type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="">Select...</option>
        @foreach($employmentTypes as $et)
        <option value="{{ $et }}" @selected(old('employment_type')===$et)>{{ $et }}</option>
        @endforeach
        </select>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Salary</label><input name="salary" type="number" step="0.01" value="{{ old('salary', 0) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        @foreach(['active'=>'Active','inactive'=>'Inactive','on_leave'=>'On Leave','terminated'=>'Terminated'] as $k=>$v)
        <option value="{{ $k }}" @selected(old('status', 'active')===$k)>{{ $v }}</option>
        @endforeach
        </select>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Shift</label><select name="shift" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>
        @foreach(['morning'=>'Morning (8AM-4PM)','evening'=>'Evening (4PM-12AM)','night'=>'Night (12AM-8AM)','flexible'=>'Flexible'] as $k=>$v)<option value="{{ $k }}" @selected(old('shift')===$k)>{{ $v }}</option>
        @endforeach
        </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Work Location</label><select name="work_location" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>
        @foreach(['office'=>'On-site','remote'=>'Remote','hybrid'=>'Hybrid'] as $k=>$v)
        <option value="{{ $k }}" @selected(old('work_location')===$k)>{{ $v }}</option>
        @endforeach
        </select></div>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Employee</button>
    </div>
</form>
@endsection
