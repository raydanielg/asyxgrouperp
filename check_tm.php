<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = App\Models\User::where('email', 'technical.manager@djanproject.com')->first();
echo "User: " . ($u ? $u->name : 'NOT FOUND') . "\n";
echo "Employee: " . ($u && $u->employee ? $u->employee->id : 'NONE') . "\n";
echo "Payrolls: " . App\Models\Payroll::count() . "\n";
echo "EmployeeProject: " . DB::table('employee_project')->count() . "\n";
echo "Projects: " . App\Models\Project::count() . "\n";
echo "Tickets: " . App\Models\HelpdeskTicket::count() . "\n";
