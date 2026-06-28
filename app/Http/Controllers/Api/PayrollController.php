<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with('employee')->latest();

        if ($request->month) {
            $query->where('month', $request->month);
        }
        if ($request->year) {
            $query->where('year', $request->year);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function show(Payroll $payroll)
    {
        return response()->json($payroll->load('employee'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        $employees = Employee::where('status', 'active')->get();
        $payrolls = [];

        foreach ($employees as $employee) {
            $existing = Payroll::where('employee_id', $employee->id)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->first();

            if (!$existing) {
                $payrolls[] = Payroll::create([
                    'employee_id' => $employee->id,
                    'month' => $request->month,
                    'year' => $request->year,
                    'basic_salary' => $employee->salary ?? 0,
                    'allowances' => 0,
                    'deductions' => 0,
                    'net_salary' => $employee->salary ?? 0,
                    'status' => 'draft',
                    'company_id' => $request->user()->company_id,
                ]);
            }
        }

        return response()->json([
            'message' => count($payrolls) . ' payroll records generated',
            'payrolls' => $payrolls,
        ], 201);
    }

    public function approve(Request $request, Payroll $payroll)
    {
        $payroll->update(['status' => 'approved']);
        return response()->json($payroll->load('employee'));
    }
}
