<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with('employee', 'creator')->latest();

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $payrolls = $query->paginate(15)->withQueryString();
        $employees = Employee::where('status', 'active')->get();

        $stats = [
            'total' => Payroll::count(),
            'paid' => Payroll::where('status', 'paid')->sum('net_salary'),
            'pending' => Payroll::where('status', 'pending')->count(),
            'average' => Payroll::avg('net_salary') ?? 0,
        ];

        $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        $years = range(now()->year - 2, now()->year + 1);

        return view('admin.hrm.payroll.index', compact('payrolls', 'employees', 'stats', 'months', 'years'));
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('employee', 'creator');
        return view('admin.hrm.payroll.show', compact('payroll'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_number' => 'required|string|unique:payrolls,payroll_number',
            'month' => 'required|string',
            'year' => 'required|integer',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'net_salary' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:pending,paid,cancelled',
        ]);

        $data['allowances'] = $data['allowances'] ?? 0;
        $data['deductions'] = $data['deductions'] ?? 0;
        $data['status'] = $data['status'] ?? 'pending';
        $data['created_by'] = auth()->id();
        $data['company_id'] = auth()->user()->company_id;

        Payroll::create($data);

        return redirect()->route('admin.payroll.index')->with('success', 'Payroll created successfully.');
    }

    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'net_salary' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:pending,paid,cancelled',
        ]);

        $data['allowances'] = $data['allowances'] ?? 0;
        $data['deductions'] = $data['deductions'] ?? 0;
        $data['status'] = $data['status'] ?? 'pending';

        $payroll->update($data);

        return redirect()->route('admin.payroll.show', $payroll)->with('success', 'Payroll updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('admin.payroll.index')->with('success', 'Payroll deleted.');
    }

    public function generateForm()
    {
        $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        $years = range(now()->year - 2, now()->year + 1);
        $activeEmployees = Employee::where('status', 'active')->count();
        $existingCounts = Payroll::selectRaw('month, year, count(*) as cnt')
            ->groupBy('month', 'year')->orderBy('year', 'desc')->orderByRaw("CASE month WHEN 'January' THEN 1 WHEN 'February' THEN 2 WHEN 'March' THEN 3 WHEN 'April' THEN 4 WHEN 'May' THEN 5 WHEN 'June' THEN 6 WHEN 'July' THEN 7 WHEN 'August' THEN 8 WHEN 'September' THEN 9 WHEN 'October' THEN 10 WHEN 'November' THEN 11 WHEN 'December' THEN 12 END")->get();

        return view('admin.hrm.payroll.generate', compact('months', 'years', 'activeEmployees', 'existingCounts'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|integer|min:2020|max:2099',
        ]);

        $month = $request->month;
        $year = $request->year;
        $employees = Employee::where('status', 'active')->get();
        $count = 0;

        foreach ($employees as $emp) {
            $existing = Payroll::where('employee_id', $emp->id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if ($existing) continue;

            $basic = $emp->salary ?? 0;
            $allowances = round($basic * 0.2);
            $deductions = round($basic * 0.08);
            $net = $basic + $allowances - $deductions;

            Payroll::create([
                'company_id' => auth()->user()->company_id,
                'employee_id' => $emp->id,
                'payroll_number' => 'PAY-' . $year . str_pad(date('m', strtotime("1 $month")), 2, '0', STR_PAD_LEFT) . '-' . str_pad($emp->id, 4, '0', STR_PAD_LEFT),
                'month' => $month,
                'year' => $year,
                'basic_salary' => $basic,
                'allowances' => $allowances,
                'deductions' => $deductions,
                'net_salary' => $net,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);
            $count++;
        }

        return redirect()->route('admin.payroll.index', ['month' => $month, 'year' => $year])
            ->with('success', "Generated $count payroll records for $month $year.");
    }

    public function pdf(Payroll $payroll)
    {
        $payroll->load('employee', 'creator');
        $company = auth()->user()->company;

        $pdf = Pdf::loadView('pdf.payslip', compact('payroll', 'company'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        $filename = 'payslip-' . $payroll->payroll_number . '.pdf';
        return $pdf->download($filename);
    }
}
