<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    public function generate(string $prefix, string $table, ?int $companyId = null): string
    {
        $companyId = $companyId ?? (auth()->user()?->activeCompanyId() ?? auth()->user()?->company_id);

        $yearMonth = date('Ym');
        $prefixWithCompany = $companyId ? "{$prefix}-{$companyId}" : $prefix;

        $count = DB::table($table)
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefixWithCompany}-{$yearMonth}-{$sequence}";
    }

    public function generateInvoiceNumber(string $type = 'sales', ?int $companyId = null): string
    {
        $prefix = $type === 'sales' ? 'INV' : 'PINV';
        return $this->generate($prefix, $type === 'sales' ? 'sales_invoices' : 'purchase_invoices', $companyId);
    }

    public function generateVendorInvoiceNumber(?int $companyId = null): string
    {
        return $this->generate('VINV', 'vendor_invoices', $companyId);
    }

    public function generatePaymentNumber(?int $companyId = null): string
    {
        return $this->generate('VPAY', 'vendor_payments', $companyId);
    }

    public function generateDocumentNumber(?int $companyId = null): string
    {
        return $this->generate('DOC', 'documents', $companyId);
    }
}
