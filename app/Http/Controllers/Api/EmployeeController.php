<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Leave;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return response()->json(Employee::with('company', 'user', 'manager')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'joining_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated',
        ]);
        $employee = Employee::create($data);
        return response()->json($employee, 201);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee->load('company', 'user', 'manager', 'attendances', 'payrolls', 'leaves'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'string|max:255',
            'department' => 'string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'in:active,inactive,terminated',
        ]);
        $employee->update($data);
        return response()->json($employee);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(['message' => 'Employee deleted']);
    }

    public function attendance(Employee $employee)
    {
        return response()->json(Attendance::where('employee_id', $employee->id)->latest()->paginate(30));
    }

    public function payroll(Employee $employee)
    {
        return response()->json(Payroll::where('employee_id', $employee->id)->latest()->get());
    }

    public function leaves(Employee $employee)
    {
        return response()->json(Leave::where('employee_id', $employee->id)->latest()->get());
    }
}
