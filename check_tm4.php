<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = App\Models\User::where('email', 'technical.manager@djanproject.com')->first();
echo "=== Technical Manager User ===\n";
echo "Name: " . $u->name . "\n";
echo "Employee: " . ($u->employee ? $u->employee->id . " - " . $u->employee->first_name . " " . $u->employee->last_name : 'NONE') . "\n";
echo "Department: " . ($u->employee?->department ?? 'N/A') . "\n";
echo "Designation: " . ($u->employee?->designation ?? 'N/A') . "\n";
echo "Salary: " . ($u->employee?->salary ?? 'N/A') . "\n";

echo "\n=== Payrolls for TM employee ===\n";
$payrolls = App\Models\Payroll::where('employee_id', $u->employee->id)->get();
echo "Count: " . $payrolls->count() . "\n";
foreach ($payrolls as $p) {
    echo "  {$p->month} {$p->year} | Basic: {$p->basic_salary} | Net: {$p->net_salary} | Status: {$p->status}\n";
}

echo "\n=== Employee-Project Assignments ===\n";
$epCount = DB::table('employee_project')->count();
echo "Total assignments: {$epCount}\n";
$tmEmpProjects = DB::table('employee_project')->where('employee_id', $u->employee->id)->get();
echo "TM employee assigned to " . $tmEmpProjects->count() . " projects\n";
foreach ($tmEmpProjects as $ep) {
    $proj = App\Models\Project::find($ep->project_id);
    echo "  - " . $proj?->title . " | Role: {$ep->role} | Active: " . ($ep->is_active ? 'Yes' : 'No') . "\n";
}

echo "\n=== Projects with employees ===\n";
$projects = App\Models\Project::with('employees')->get();
foreach ($projects as $proj) {
    echo "  " . $proj->title . " | Employees: " . $proj->employees->count() . " | Status: {$proj->status}\n";
}

echo "\n=== Total Counts ===\n";
echo "Projects: " . App\Models\Project::count() . "\n";
echo "Tickets: " . App\Models\HelpdeskTicket::count() . "\n";
echo "Payrolls: " . App\Models\Payroll::count() . "\n";
echo "EmployeeProject: " . DB::table('employee_project')->count() . "\n";
echo "Employees: " . App\Models\Employee::count() . "\n";
