<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoice = App\Models\SalesInvoice::with(['customer','warehouse','items'])->first();
if (!$invoice) { echo "No invoices found\n"; exit(1); }

$company = App\Models\Company::where('is_group', true)->first() ?? App\Models\Company::first();
$pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['invoice' => $invoice, 'company' => $company]);
$pdf->setPaper('A4', 'portrait');
$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
$output = $pdf->output();
echo "PDF generated: " . strlen($output) . " bytes\n";
file_put_contents(__DIR__ . '/test_invoice.pdf', $output);
echo "Saved test_invoice.pdf\n";

// Also test show view
file_put_contents(__DIR__ . '/test_invoice_show.html', view('admin.invoices.sales-show', ['salesInvoice' => $invoice])->render());
echo "Show view saved\n";
