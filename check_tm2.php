<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = App\Models\User::where('email', 'technical.manager@djanproject.com')->first();
$emp = $u->employee;
echo "Employee ID: " . $emp->id . "\n";
echo "Employee Name: " . $emp->first_name . " " . $emp->last_name . "\n";
echo "Department: " . $emp->department . "\n";
echo "Designation: " . $emp->designation . "\n";
echo "Salary: " . $emp->salary . "\n";
echo "Payrolls for this employee: " . App\Models\Payroll::where('employee_id', $emp->id)->count() . "\n";

$payrolls = App\Models\Payroll::where('employee_id', $emp->id)->get();
foreach ($payrolls as $p) {
    echo "  - " . $p->month . " " . $p->year . " | Net: " . $p->net_salary . " | Status: " . $p->status . "\n";
}
