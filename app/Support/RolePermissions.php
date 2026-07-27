<?php

namespace App\Support;

/**
 * HARDCODED role => permissions map.
 *
 * This is the SINGLE SOURCE OF TRUTH for what each role can do.
 * It is NOT read from the database (role_permission table). Editing
 * roles/permissions from the Admin > Roles UI will NOT change access
 * anymore — you must edit this file directly.
 *
 * admin / superadmin always get full access (handled as a special
 * case wherever hasPermission() is checked), so they are not listed
 * here with an exhaustive permission array.
 */
class RolePermissions
{
    /**
     * All permission names that exist in the system, grouped only for
     * readability. Used to grant "admin/superadmin" full access.
     */
    public const ALL_PERMISSIONS = [
        // Dashboard / Reports
        'view-dashboard', 'view-reports',
        // System Administration
        'view-users', 'create-users', 'edit-users', 'delete-users',
        'view-roles', 'manage-roles', 'view-settings', 'view-companies',
        'switch-companies', 'view-login-history', 'view-audit-logs',
        // Approvals
        'view-approvals', 'approve-final', 'approve-finance', 'approve-hr',
        'approve-technical', 'approve-procurement',
        // Tenders / Contracts
        'view-tenders', 'create-tenders', 'edit-tenders', 'delete-tenders',
        'view-crm-contracts', 'create-crm-contracts', 'edit-crm-contracts', 'delete-crm-contracts',
        // HR
        'view-employees', 'create-employees', 'edit-employees',
        'view-attendance', 'create-attendance',
        'view-leaves', 'approve-leaves',
        'view-payroll', 'create-payroll',
        'view-performance', 'create-performance',
        'view-training', 'create-training',
        'view-certifications', 'create-certifications',
        'view-recruitment', 'create-recruitment',
        'view-disciplinary', 'view-events', 'create-events',
        'view-policies', 'create-policies',
        'view-job-postings', 'create-job-postings', 'view-applications',
        'view-overtime', 'approve-overtime',
        // CRM / Sales
        'view-crm-leads', 'create-crm-leads', 'edit-crm-leads',
        'view-crm-deals', 'create-crm-deals', 'edit-crm-deals',
        'view-crm-contacts', 'create-crm-contacts', 'edit-crm-contacts',
        'view-sales-invoices', 'create-sales-invoices', 'edit-sales-invoices',
        'view-sales-proposals', 'view-quotations', 'create-quotations',
        'view-campaigns', 'create-campaigns',
        // Projects / Technical
        'view-projects', 'create-projects', 'edit-projects',
        'view-timesheets', 'create-timesheets',
        'view-bugs', 'create-bugs',
        'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
        'view-lpos', 'create-lpos',
        'view-job-cards', 'create-job-cards', 'edit-job-cards', 'delete-job-cards',
        'view-site-reports', 'create-site-reports',
        'view-meetings', 'create-meetings', 'edit-meetings',
        'view-documents', 'create-documents', 'edit-documents', 'delete-documents',
        'view-team-timesheets', 'approve-team-timesheets',
        'view-team-leaves', 'approve-team-leaves',
        'view-project-budgets',
        // Operations / Fleet / Logistics
        'view-fleet', 'view-vehicles', 'create-vehicles', 'edit-vehicles',
        'view-fuel-logs', 'create-fuel-logs',
        'view-maintenance', 'create-maintenance',
        'view-deliveries', 'create-deliveries',
        'view-shipments', 'create-shipments',
        // Inventory / Procurement
        'view-products', 'create-products', 'edit-products', 'view-product-categories',
        'view-warehouses', 'create-warehouses',
        'view-stock-movements', 'create-stock-movements',
        'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers',
        'view-transfers',
        'view-rfqs', 'create-rfqs', 'edit-rfqs',
        'view-purchase-requisitions', 'create-purchase-requisitions',
        'view-grns', 'create-grns',
        'view-purchase-invoices', 'create-purchase-invoices', 'edit-purchase-invoices',
        'view-assets', 'create-assets', 'edit-assets', 'delete-assets',
        // Finance
        'view-expenses', 'create-expenses',
        'view-revenues', 'create-revenues',
        'view-bills', 'create-bills',
        'view-cost-centres',
        'view-bank-accounts', 'create-bank-accounts',
        'view-acc-transfers', 'create-acc-transfers',
        'view-financial-reports',
        'view-petty-cash', 'create-petty-cash',
        'view-journal-entries', 'create-journal-entries',
        'view-bank-reconciliation', 'create-bank-reconciliation',
        'view-tax-management', 'create-tax-management',
        'view-credit-limits', 'edit-credit-limits',
        'view-budgets', 'create-budgets', 'edit-budgets',
        'view-payslips', 'create-payslips',
        // POS
        'view-pos', 'create-pos',
        // Self service
        'view-self-service', 'view-my-payslips', 'apply-leave',
        'view-my-attendance', 'view-my-timesheets', 'create-my-timesheets',
        'view-announcements', 'create-announcements', 'edit-announcements', 'delete-announcements',
        'view-team-overview', 'view-team-attendance',
        // Reception
        'view-visitors', 'create-visitors', 'edit-visitors', 'delete-visitors',
        'view-appointments', 'create-appointments', 'edit-appointments', 'delete-appointments',
        'view-calls', 'create-calls', 'edit-calls', 'delete-calls',
        'view-correspondence', 'create-correspondence', 'edit-correspondence', 'delete-correspondence',
        'view-parcels', 'create-parcels', 'edit-parcels', 'delete-parcels',
        'view-front-desk', 'create-front-desk', 'edit-front-desk', 'delete-front-desk',
        'view-departments', 'create-departments', 'edit-departments', 'delete-departments',
        'view-messages', 'create-messages', 'edit-messages', 'delete-messages',
    ];

    /**
     * HARDCODED permissions per role name.
     * Key = value of users.role column. Value = array of permission names.
     */
    public const ROLE_PERMISSIONS = [
        // Executive
        'director' => [
            'view-dashboard', 'view-reports', 'view-financial-reports', 'view-approvals',
            'approve-final', 'view-companies', 'switch-companies', 'view-tenders',
            'view-crm-contracts', 'view-employees', 'view-sales-invoices', 'view-purchase-invoices',
            'view-expenses', 'view-revenues', 'view-projects', 'view-helpdesk-tickets',
            'view-crm-leads', 'view-crm-deals', 'view-products', 'view-warehouses', 'view-pos',
            'view-self-service', 'view-my-payslips',
        ],

        // Finance
        'accountant' => [
            'view-dashboard', 'view-journal-entries', 'create-journal-entries', 'view-financial-reports',
            'view-petty-cash', 'create-petty-cash', 'view-sales-invoices', 'create-sales-invoices',
            'view-purchase-invoices', 'create-purchase-invoices', 'view-expenses', 'create-expenses',
            'view-revenues', 'create-revenues', 'view-bills', 'create-bills', 'view-cost-centres',
            'view-bank-accounts', 'view-acc-transfers', 'view-reports', 'view-self-service', 'view-my-payslips',
        ],
        'finance_manager' => [
            'view-dashboard', 'view-reports', 'view-sales-invoices', 'view-purchase-invoices',
            'view-expenses', 'view-revenues', 'view-bills', 'view-bank-accounts', 'view-acc-transfers',
            'view-budgets', 'create-budgets', 'edit-budgets', 'view-journal-entries', 'create-journal-entries',
            'view-financial-reports', 'view-petty-cash', 'create-petty-cash', 'view-bank-reconciliation',
            'create-bank-reconciliation', 'view-tax-management', 'create-tax-management', 'view-credit-limits',
            'edit-credit-limits', 'view-approvals', 'approve-finance', 'view-payroll', 'create-payroll',
            'view-self-service', 'view-my-payslips',
        ],

        // Procurement & Inventory
        'procurement_manager' => [
            'view-dashboard', 'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers',
            'view-rfqs', 'create-rfqs', 'edit-rfqs', 'view-lpos', 'create-lpos', 'view-grns', 'create-grns',
            'view-purchase-requisitions', 'create-purchase-requisitions', 'view-purchase-invoices',
            'view-products', 'create-products', 'edit-products', 'view-product-categories', 'view-warehouses',
            'create-warehouses', 'view-stock-movements', 'create-stock-movements', 'view-transfers',
            'view-assets', 'create-assets', 'edit-assets', 'view-approvals', 'approve-procurement',
            'view-reports', 'view-self-service', 'view-my-payslips',
        ],

        // Sales
        'sales_manager' => [
            'view-dashboard', 'view-crm-leads', 'create-crm-leads', 'edit-crm-leads', 'view-crm-deals',
            'create-crm-deals', 'edit-crm-deals', 'view-crm-contacts', 'create-crm-contacts', 'edit-crm-contacts',
            'view-sales-invoices', 'create-sales-invoices', 'view-quotations', 'create-quotations',
            'view-sales-proposals', 'view-campaigns', 'create-campaigns', 'view-reports',
            'view-self-service', 'view-my-payslips',
        ],

        // Projects
        'project_manager' => [
            'view-dashboard', 'view-projects', 'create-projects', 'edit-projects', 'view-timesheets',
            'create-timesheets', 'view-bugs', 'create-bugs', 'view-employees', 'view-crm-deals',
            'view-budgets', 'view-site-reports', 'create-site-reports', 'view-meetings', 'create-meetings',
            'view-documents', 'view-reports', 'view-self-service', 'view-my-payslips',
            'view-job-cards', 'create-job-cards', 'edit-job-cards',
        ],

        // Technical / IT
        'technical_manager' => [
            'view-dashboard', 'view-projects', 'create-projects', 'edit-projects', 'view-timesheets',
            'create-timesheets',
            'view-bugs', 'create-bugs', 'view-helpdesk-tickets', 'create-helpdesk-tickets',
            'edit-helpdesk-tickets', 'view-lpos', 'approve-technical', 'view-assets', 'create-assets',
            'edit-assets', 'view-settings', 'view-employees', 'view-login-history', 'view-audit-logs',
            'view-self-service', 'view-my-payslips',
            'view-job-cards', 'create-job-cards', 'edit-job-cards', 'delete-job-cards',
        ],

        // Technical field staff
        'technician' => [
            'view-dashboard', 'view-projects', 'view-job-cards', 'create-job-cards', 'edit-job-cards',
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
            'view-bugs', 'create-bugs',
            'view-self-service', 'view-my-payslips',
        ],

        // Services & Operations
        'operations_manager' => [
            'view-dashboard', 'view-fleet', 'view-vehicles', 'create-vehicles', 'edit-vehicles',
            'view-fuel-logs', 'create-fuel-logs', 'view-maintenance', 'create-maintenance',
            'view-helpdesk-tickets', 'view-deliveries', 'create-deliveries', 'view-shipments',
            'create-shipments', 'view-products', 'view-warehouses', 'view-stock-movements',
            'view-reports', 'view-self-service', 'view-my-payslips',
        ],

        // HR
        'hr_manager' => [
            'view-dashboard', 'view-employees', 'create-employees', 'edit-employees', 'view-attendance',
            'create-attendance', 'view-overtime', 'approve-overtime', 'view-payroll', 'create-payroll',
            'view-leaves', 'approve-leaves', 'view-performance', 'create-performance', 'view-training',
            'create-training', 'view-certifications', 'create-certifications', 'view-recruitment',
            'create-recruitment', 'view-job-postings', 'create-job-postings', 'view-applications',
            'view-approvals', 'approve-hr', 'view-disciplinary', 'view-assets', 'view-policies',
            'create-policies', 'view-events', 'create-events', 'view-self-service', 'view-my-payslips',
        ],
    ];

    /**
     * Get the hardcoded permission list for a role name.
     * admin / superadmin get everything.
     */
    public static function forRole(?string $role): array
    {
        if ($role === 'admin' || $role === 'superadmin') {
            return self::ALL_PERMISSIONS;
        }

        return self::ROLE_PERMISSIONS[$role] ?? [];
    }
}
