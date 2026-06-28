<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payroll = App\Models\Payroll::with('employee','creator')->first();
if (!$payroll) { echo "No payroll records\n"; exit(1); }

$company = App\Models\Company::where('is_group', true)->first() ?? App\Models\Company::first();
$pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.payslip', ['payroll' => $payroll, 'company' => $company]);
$pdf->setPaper('A4', 'portrait');
$output = $pdf->output();
echo "PDF generated: " . strlen($output) . " bytes\n";
file_put_contents(__DIR__ . '/test_payslip.pdf', $output);
echo "Saved test_payslip.pdf\n";
