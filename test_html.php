<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payroll = App\Models\Payroll::with('employee','creator')->first();
$company = App\Models\Company::where('is_group', true)->first() ?? App\Models\Company::first();

$html = view('pdf.payslip', ['payroll' => $payroll, 'company' => $company])->render();
file_put_contents(__DIR__ . '/test_pdf_html.html', $html);
echo "HTML saved: " . filesize(__DIR__ . '/test_pdf_html.html') . " bytes\n";

// Check if fonts load
if (strpos($html, 'Fraunces') !== false) echo "Fraunces font found\n";
if (strpos($html, 'JetBrains') !== false) echo "JetBrains font found\n";
if (strpos($html, 'asyxgrouplogo.png') !== false) echo "Logo found\n";
