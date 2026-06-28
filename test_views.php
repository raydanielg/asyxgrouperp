<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $view = view('admin.hrm.payroll.index', [
        'payrolls' => new Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
        'employees' => collect(),
        'stats' => ['total'=>0,'paid'=>0,'pending'=>0,'average'=>0],
        'months' => ['January'], 'years' => [2026]
    ]);
    echo "Index view renders OK\n";
} catch (Exception $e) {
    echo "Index view ERROR: " . $e->getMessage() . "\n";
}

$payroll = App\Models\Payroll::first();
if ($payroll) {
    try {
        $view = view('admin.hrm.payroll.show', ['payroll' => $payroll]);
        echo "Show view renders OK\n";
    } catch (Exception $e) {
        echo "Show view ERROR: " . $e->getMessage() . "\n";
    }
    try {
        $view = view('pdf.payslip', ['payroll' => $payroll, 'company' => App\Models\Company::first()]);
        echo "PDF view renders OK\n";
    } catch (Exception $e) {
        echo "PDF view ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "No payroll records found (run seeder first)\n";
}
