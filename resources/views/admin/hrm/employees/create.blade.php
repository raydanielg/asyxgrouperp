@extends('layouts.admin')
@section('title', 'Add Employee - ' . config('app.name'))
@section('page_title', 'Add Employee')
@section('content')
<div class="max-w-5xl">
<div class="mb-4">
    <a href="{{ route('admin.employees.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Employees</a>
</div>

@if($errors->any())
<div class="mb-4 px-4 py-3 rounded bg-red-50 text-red-700 text-sm border border-red-100">
    <p class="font-medium text-xs">Please fix the following:</p>
    <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-5">
    @csrf

    {{-- Personal --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Personal Information</h4></div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Employee ID <span class="text-red-500">*</span></label><input name="employee_id" required value="{{ old('employee_id', 'EMP-' . strtoupper(\Illuminate\Support\Str::random(6))) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 font-mono"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">First Name <span class="text-red-500">*</span></label><input name="first_name" required value="{{ old('first_name') }}" placeholder="e.g. John" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Last Name</label><input name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Mushi" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Email <span class="text-red-500">*</span></label><input name="email" type="email" required value="{{ old('email') }}" placeholder="name@asyx.co.tz" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" value="{{ old('phone') }}" placeholder="+255 700 000 000" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Gender</label><select name="gender" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">—</option>
                @foreach(['male'=>'Male','female'=>'Female','other'=>'Other'] as $k=>$v)
                <option value="{{ $k }}" @selected(old('gender')===$k)>{{ $v }}</option>
                @endforeach
                </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Date of Birth</label><input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Nationality</label><select name="nationality" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">—</option>
                @foreach(['Tanzanian','Kenyan','Ugandan','Nigerian','South African','American','British','Indian','Other'] as $nat)
                <option value="{{ $nat }}" @selected(old('nationality')===$nat)>{{ $nat }}</option>
                @endforeach
                </select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Marital Status</label><select name="marital_status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">—</option>
                @foreach(['single'=>'Single','married'=>'Married','divorced'=>'Divorced','widowed'=>'Widowed'] as $k=>$v)
                <option value="{{ $k }}" @selected(old('marital_status')===$k)>{{ $v }}</option>
                @endforeach
                </select></div>
            </div>
            <div class="mt-4"><label class="block text-xs font-medium text-gray-600 mb-1">Address</label><textarea name="address" rows="2" placeholder="Street, area, city" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ old('address') }}</textarea></div>
        </div>
    </div>

    {{-- Employment --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Employment Information</h4></div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Department <span class="text-red-500">*</span></label>
                    <select name="department" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select...</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept }}" @selected(old('department')===$dept)>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Designation</label>
                    <select name="designation" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select...</option>
                        @foreach($designations as $des)
                        <option value="{{ $des }}" @selected(old('designation')===$des)>{{ $des }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Reports To (Manager)</label>
                    <select name="manager_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— No manager —</option>
                        @foreach($managers as $m)
                        <option value="{{ $m->id }}" @selected(old('manager_id')==$m->id)>{{ $m->full_name }} ({{ $m->designation ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">User Account</label>
                    <select name="user_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— No login account —</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(old('user_id')==$u->id)>{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Only users without an employee record are listed.</p>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Joining Date</label><input name="joining_date" type="date" value="{{ old('joining_date') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Employment Type</label>
                    <select name="employment_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select...</option>
                        @foreach($employmentTypes as $et)
                        <option value="{{ $et }}" @selected(old('employment_type')===$et)>{{ $et }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Salary (TZS)</label><input name="salary" type="number" step="0.01" min="0" value="{{ old('salary', 0) }}" placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        @foreach(['active'=>'Active','inactive'=>'Inactive','on_leave'=>'On Leave','terminated'=>'Terminated'] as $k=>$v)
                        <option value="{{ $k }}" @selected(old('status', 'active')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Shift</label>
                    <select name="shift" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">—</option>
                    @foreach(['morning'=>'Morning (8AM–4PM)','evening'=>'Evening (4PM–12AM)','night'=>'Night (12AM–8AM)','flexible'=>'Flexible'] as $k=>$v)<option value="{{ $k }}" @selected(old('shift')===$k)>{{ $v }}</option>
                    @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Work Location</label>
                    <select name="work_location" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">—</option>
                    @foreach(['office'=>'On-site','remote'=>'Remote','hybrid'=>'Hybrid'] as $k=>$v)
                    <option value="{{ $k }}" @selected(old('work_location')===$k)>{{ $v }}</option>
                    @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Projects --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Project Assignments</h4></div>
        <div class="p-5">
            <p class="text-xs text-gray-400 mb-3">Assign to projects for cost tracking and bonus management.</p>
            <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                @foreach($projects as $project)
                <div class="flex items-center gap-3 py-2">
                    <input type="checkbox" name="project_ids[]" value="{{ $project->id }}" id="proj-{{ $project->id }}" class="w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400" @checked(in_array($project->id, old('project_ids', [])))>
                    <label for="proj-{{ $project->id }}" class="flex-1 text-xs font-medium text-gray-700 cursor-pointer">{{ $project->title }} <span class="text-gray-400 font-normal">· {{ $project->project_number }}</span></label>
                    <input type="text" name="project_roles[{{ $project->id }}]" placeholder="Role e.g. Developer" class="w-44 px-2.5 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400" value="{{ old('project_roles.' . $project->id) }}">
                </div>
                @endforeach
                @if($projects->isEmpty())
                <p class="text-xs text-gray-400 py-4">No active projects available</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between gap-3 bg-white rounded-md border border-gray-200 px-5 py-3.5">
        <a href="{{ route('admin.employees.index') }}" class="text-xs text-gray-500 hover:text-gray-800">Cancel</a>
        <button type="submit" class="px-5 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Create Employee</button>
    </div>
</form>
</div>
@endsection
