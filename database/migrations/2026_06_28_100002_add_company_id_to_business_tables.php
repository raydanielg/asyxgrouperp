<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'users',
            'employees',
            'attendances',
            'payrolls',
            'leaves',
            'performance_reviews',
            'trainings',
            'job_postings',
            'employee_assets',
            'hr_events',
            'policies',
            'crm_leads',
            'crm_deals',
            'crm_contracts',
            'crm_contacts',
            'bank_accounts',
            'bank_transfers_acc',
            'expenses',
            'revenues',
            'bills',
            'estimates',
            'projects',
            'project_tasks',
            'timesheets',
            'project_bugs',
            'product_categories',
            'products',
            'suppliers',
            'stock_movements',
            'pos_sales',
            'tenders',
            'quotations',
            'lpos',
            'grns',
            'delivery_notes',
            'vendor_invoices',
            'vendor_payments',
            'office_expenses',
            'client_receipts',
            'helpdesk_categories',
            'helpdesk_tickets',
            'warehouses',
            'plans',
            'orders',
            'purchase_invoices',
            'sales_invoices',
            'sales_proposals',
            'settings',
            'project_budgets',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('company_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('companies')
                        ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'users',
            'employees',
            'attendances',
            'payrolls',
            'leaves',
            'performance_reviews',
            'trainings',
            'job_postings',
            'employee_assets',
            'hr_events',
            'policies',
            'crm_leads',
            'crm_deals',
            'crm_contracts',
            'crm_contacts',
            'bank_accounts',
            'bank_transfers_acc',
            'expenses',
            'revenues',
            'bills',
            'estimates',
            'projects',
            'project_tasks',
            'timesheets',
            'project_bugs',
            'product_categories',
            'products',
            'suppliers',
            'stock_movements',
            'pos_sales',
            'tenders',
            'quotations',
            'lpos',
            'grns',
            'delivery_notes',
            'vendor_invoices',
            'vendor_payments',
            'office_expenses',
            'client_receipts',
            'helpdesk_categories',
            'helpdesk_tickets',
            'warehouses',
            'plans',
            'orders',
            'purchase_invoices',
            'sales_invoices',
            'sales_proposals',
            'settings',
            'project_budgets',
        ];

        foreach (array_reverse($tables) as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};
