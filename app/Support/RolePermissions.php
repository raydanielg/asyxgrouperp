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
 *
 * Roles follow the ASYX Group ERP Requirements Specification and
 * roles-sidebar-menu.md (52 operational roles + system admin roles).
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
        'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
        'view-attendance', 'create-attendance',
        'view-leaves', 'create-leaves', 'approve-leaves',
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
        'view-crm-contacts', 'create-crm-contacts', 'edit-crm-contacts', 'delete-crm-contacts',
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
        'view-fleet', 'view-vehicles', 'create-vehicles', 'edit-vehicles', 'delete-vehicles',
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
        'view-calls', 'create-calls', 'edit-calls', 'delete-calls', 'view-call-logs',
        'view-correspondence', 'create-correspondence', 'edit-correspondence', 'delete-correspondence',
        'view-parcels', 'create-parcels', 'edit-parcels', 'delete-parcels',
        'view-front-desk', 'create-front-desk', 'edit-front-desk', 'delete-front-desk',
        'view-departments', 'create-departments', 'edit-departments', 'delete-departments',
        'view-messages', 'create-messages', 'edit-messages', 'delete-messages',
    ];

    /**
     * Baseline self-service permissions every employee gets, layered on top of
     * their role-specific permissions (roles-sidebar-menu.md, design rule #4).
     */
    private const SELF_SERVICE = [
        'view-dashboard', 'view-self-service', 'view-my-payslips', 'apply-leave',
        'view-my-attendance', 'view-my-timesheets', 'create-my-timesheets',
        'view-announcements', 'view-messages', 'create-messages',
    ];

    /**
     * HARDCODED permissions per role name.
     * Key = value of users.role column. Value = array of permission names.
     *
     * NOTE: SELF_SERVICE baseline is merged automatically in forRole() — the
     * arrays below only need the role-specific permissions.
     */
    public const ROLE_PERMISSIONS = [
        // ══════════════════════════════════════════════════
        // SECTION A — Executive Roles
        // ══════════════════════════════════════════════════
        'managing_director' => [
            'view-reports', 'view-financial-reports', 'view-approvals', 'approve-final',
            'view-companies', 'switch-companies', 'view-tenders', 'view-crm-contracts',
            'view-employees', 'view-sales-invoices', 'view-purchase-invoices',
            'view-expenses', 'view-revenues', 'view-bills', 'view-bank-accounts',
            'view-acc-transfers', 'view-budgets', 'view-payroll', 'view-payslips',
            'view-projects', 'view-helpdesk-tickets', 'view-crm-leads', 'view-crm-deals',
            'view-products', 'view-warehouses', 'view-pos', 'view-audit-logs',
        ],
        'general_manager' => [
            'view-reports', 'view-approvals', 'approve-hr', 'approve-technical',
            'approve-procurement', 'view-projects', 'create-projects', 'edit-projects',
            'view-crm-leads', 'view-crm-deals', 'view-sales-invoices', 'view-quotations',
            'view-employees', 'view-attendance', 'view-leaves', 'view-tenders',
            'view-crm-contracts', 'view-helpdesk-tickets', 'view-products',
            'view-warehouses', 'view-budgets', 'view-expenses', 'view-revenues',
        ],
        'technical_manager' => [
            'view-projects', 'create-projects', 'edit-projects', 'view-timesheets',
            'create-timesheets',
            'view-bugs', 'create-bugs', 'view-helpdesk-tickets', 'create-helpdesk-tickets',
            'edit-helpdesk-tickets', 'view-lpos', 'approve-technical', 'view-assets', 'create-assets',
            'edit-assets', 'view-settings', 'view-employees', 'view-login-history', 'view-audit-logs',
            'view-job-cards', 'create-job-cards', 'edit-job-cards', 'delete-job-cards',
        ],
        'operations_manager' => [
            'view-fleet', 'view-vehicles', 'create-vehicles', 'edit-vehicles',
            'view-fuel-logs', 'create-fuel-logs', 'view-maintenance', 'create-maintenance',
            'view-helpdesk-tickets', 'view-deliveries', 'create-deliveries', 'view-shipments',
            'create-shipments', 'view-products', 'view-warehouses', 'view-stock-movements',
            'view-reports',
        ],

        // ══════════════════════════════════════════════════
        // SECTION B — Finance Roles
        // ══════════════════════════════════════════════════
        'finance_manager' => [
            'view-reports', 'view-sales-invoices', 'create-sales-invoices', 'edit-sales-invoices',
            'view-purchase-invoices', 'create-purchase-invoices', 'edit-purchase-invoices',
            'view-expenses', 'create-expenses', 'view-revenues', 'create-revenues',
            'view-bills', 'create-bills', 'view-bank-accounts', 'create-bank-accounts',
            'view-acc-transfers', 'create-acc-transfers',
            'view-budgets', 'create-budgets', 'edit-budgets', 'view-journal-entries',
            'create-journal-entries', 'view-financial-reports', 'view-petty-cash',
            'create-petty-cash', 'view-bank-reconciliation',
            'create-bank-reconciliation', 'view-tax-management', 'create-tax-management',
            'view-credit-limits', 'edit-credit-limits', 'view-approvals', 'approve-finance',
            'view-payroll', 'create-payroll', 'view-cost-centres',
        ],
        'chief_accountant' => [
            'view-journal-entries', 'create-journal-entries', 'view-bank-reconciliation',
            'create-bank-reconciliation', 'view-financial-reports', 'view-tax-management',
            'create-tax-management', 'view-expenses', 'view-revenues', 'view-bills',
            'view-bank-accounts', 'view-acc-transfers', 'view-cost-centres', 'view-reports',
        ],
        'accountant' => [
            'view-journal-entries', 'create-journal-entries', 'view-financial-reports',
            'view-petty-cash', 'create-petty-cash', 'view-sales-invoices', 'create-sales-invoices',
            'view-purchase-invoices', 'create-purchase-invoices', 'view-expenses', 'create-expenses',
            'view-revenues', 'create-revenues', 'view-bills', 'create-bills', 'view-cost-centres',
            'view-bank-accounts', 'view-acc-transfers', 'view-reports',
        ],
        'accounts_receivable_officer' => [
            'view-sales-invoices', 'create-sales-invoices', 'edit-sales-invoices',
            'view-revenues', 'create-revenues', 'view-crm-contacts', 'view-crm-deals',
            'view-crm-leads',
        ],
        'accounts_payable_officer' => [
            'view-purchase-invoices', 'create-purchase-invoices', 'view-bills',
            'create-bills', 'view-expenses', 'create-expenses', 'view-suppliers',
        ],
        'cashier' => [
            'view-petty-cash', 'create-petty-cash', 'view-pos', 'create-pos',
            'view-revenues', 'create-revenues', 'view-expenses', 'create-expenses',
            'view-bank-accounts', 'view-acc-transfers', 'create-acc-transfers',
            'view-sales-invoices', 'create-sales-invoices',
        ],
        'payroll_officer' => [
            'view-payroll', 'create-payroll', 'view-payslips', 'create-payslips',
            'view-employees', 'view-attendance', 'view-overtime', 'view-leaves',
        ],
        'budget_officer' => [
            'view-budgets', 'create-budgets', 'edit-budgets', 'view-project-budgets',
            'view-cost-centres', 'view-reports', 'view-expenses',
        ],
        'credit_controller' => [
            'view-credit-limits', 'edit-credit-limits', 'view-sales-invoices',
            'view-crm-contacts', 'view-crm-deals', 'view-revenues',
        ],
        'auditor' => [
            'view-reports', 'view-financial-reports', 'view-audit-logs', 'view-login-history',
            'view-journal-entries', 'view-sales-invoices', 'view-purchase-invoices',
            'view-expenses', 'view-revenues', 'view-bills', 'view-bank-accounts',
            'view-acc-transfers', 'view-payroll', 'view-payslips', 'view-projects',
            'view-crm-contracts', 'view-tenders', 'view-employees', 'view-companies',
            'view-budgets', 'view-documents',
        ],

        // ══════════════════════════════════════════════════
        // SECTION C — Procurement Roles
        // ══════════════════════════════════════════════════
        'procurement_manager' => [
            'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers',
            'view-rfqs', 'create-rfqs', 'edit-rfqs', 'view-lpos', 'create-lpos',
            'view-grns', 'create-grns',
            'view-purchase-requisitions', 'create-purchase-requisitions',
            'view-purchase-invoices', 'create-purchase-invoices',
            'view-products', 'create-products', 'edit-products', 'view-product-categories',
            'view-warehouses', 'create-warehouses', 'view-stock-movements',
            'create-stock-movements', 'view-transfers',
            'view-assets', 'create-assets', 'edit-assets', 'view-approvals',
            'approve-procurement', 'view-reports', 'view-deliveries', 'view-expenses',
        ],
        'procurement_officer' => [
            'view-rfqs', 'create-rfqs', 'edit-rfqs', 'view-purchase-requisitions',
            'create-purchase-requisitions', 'view-lpos', 'create-lpos', 'view-grns',
            'create-grns', 'view-suppliers', 'create-suppliers', 'view-deliveries',
            'create-deliveries', 'view-purchase-invoices', 'view-products',
        ],
        'tender_officer' => [
            'view-tenders', 'create-tenders', 'edit-tenders', 'view-documents',
            'create-documents', 'view-crm-leads', 'create-crm-leads', 'view-quotations',
            'create-quotations', 'view-crm-deals',
        ],

        // ══════════════════════════════════════════════════
        // SECTION D — Inventory Roles
        // ══════════════════════════════════════════════════
        'store_manager' => [
            'view-warehouses', 'create-warehouses', 'view-stock-movements',
            'create-stock-movements', 'view-transfers', 'view-products',
            'view-product-categories', 'view-grns', 'create-grns', 'view-suppliers',
            'view-reports', 'view-deliveries', 'create-deliveries',
        ],
        'storekeeper' => [
            'view-stock-movements', 'create-stock-movements', 'view-products',
            'view-warehouses', 'view-grns', 'create-grns', 'view-deliveries',
            'create-deliveries', 'view-transfers',
        ],
        'inventory_controller' => [
            'view-products', 'create-products', 'edit-products',
            'view-product-categories', 'view-stock-movements', 'view-warehouses',
            'view-transfers', 'view-grns',
        ],
        'asset_officer' => [
            'view-assets', 'create-assets', 'edit-assets', 'view-employees',
            'view-maintenance', 'create-maintenance',
        ],

        // ══════════════════════════════════════════════════
        // SECTION E — Sales Roles
        // ══════════════════════════════════════════════════
        'sales_manager' => [
            'view-crm-leads', 'create-crm-leads', 'edit-crm-leads', 'view-crm-deals',
            'create-crm-deals', 'edit-crm-deals', 'view-crm-contacts', 'create-crm-contacts',
            'edit-crm-contacts', 'view-sales-invoices', 'create-sales-invoices',
            'view-quotations', 'create-quotations', 'view-sales-proposals',
            'view-campaigns', 'create-campaigns', 'view-reports', 'view-tenders',
        ],
        'business_development_manager' => [
            'view-crm-leads', 'create-crm-leads', 'edit-crm-leads', 'view-crm-deals',
            'create-crm-deals', 'edit-crm-deals', 'view-crm-contacts', 'create-crm-contacts',
            'view-quotations', 'create-quotations', 'view-tenders', 'view-campaigns',
            'view-reports',
        ],
        'sales_executive' => [
            'view-crm-leads', 'create-crm-leads', 'edit-crm-leads', 'view-crm-deals',
            'create-crm-deals', 'view-crm-contacts', 'create-crm-contacts',
            'view-quotations', 'create-quotations',
        ],
        'crm_officer' => [
            'view-crm-contacts', 'create-crm-contacts', 'edit-crm-contacts',
            'view-crm-leads', 'create-crm-leads', 'edit-crm-leads', 'view-calls',
            'create-calls', 'view-helpdesk-tickets', 'create-helpdesk-tickets',
        ],
        'marketing_officer' => [
            'view-campaigns', 'create-campaigns', 'view-crm-leads', 'create-crm-leads',
            'view-documents', 'create-documents', 'view-announcements',
            'create-announcements',
        ],

        // ══════════════════════════════════════════════════
        // SECTION F — Project Roles
        // ══════════════════════════════════════════════════
        'project_director' => [
            'view-projects', 'view-project-budgets', 'view-budgets', 'view-timesheets',
            'view-crm-deals', 'view-reports', 'view-approvals', 'view-employees',
            'view-documents', 'view-sales-invoices', 'view-expenses',
        ],
        'project_manager' => [
            'view-projects', 'create-projects', 'edit-projects', 'view-timesheets',
            'create-timesheets', 'view-bugs', 'create-bugs', 'view-employees',
            'view-crm-deals', 'view-budgets', 'view-site-reports', 'create-site-reports',
            'view-meetings', 'create-meetings', 'view-documents', 'view-reports',
            'view-job-cards', 'create-job-cards', 'edit-job-cards',
            'view-team-timesheets', 'approve-team-timesheets',
        ],
        'technical_projects_manager' => [
            'view-projects', 'create-projects', 'edit-projects', 'view-timesheets',
            'create-timesheets', 'view-bugs', 'create-bugs', 'view-job-cards',
            'create-job-cards', 'edit-job-cards', 'view-employees', 'view-site-reports',
            'create-site-reports', 'view-meetings', 'create-meetings', 'view-documents',
            'view-helpdesk-tickets',
        ],
        'project_coordinator' => [
            'view-projects', 'view-timesheets', 'create-timesheets', 'view-documents',
            'create-documents', 'view-meetings', 'create-meetings', 'view-site-reports',
            'create-site-reports', 'view-bugs', 'create-bugs', 'view-employees',
        ],
        'project_engineer' => [
            'view-projects', 'view-timesheets', 'create-timesheets', 'view-site-reports',
            'create-site-reports', 'view-bugs', 'create-bugs', 'view-job-cards',
        ],
        'site_supervisor' => [
            'view-projects', 'view-site-reports', 'create-site-reports',
            'view-attendance', 'create-attendance', 'view-timesheets',
            'create-timesheets', 'view-helpdesk-tickets', 'create-helpdesk-tickets',
            'view-bugs', 'create-bugs',
        ],
        'team_leader' => [
            'view-team-timesheets', 'approve-team-timesheets', 'view-team-leaves',
            'approve-team-leaves', 'view-team-attendance', 'view-team-overview',
            'view-attendance', 'view-timesheets', 'create-timesheets', 'view-projects',
            'view-leaves',
        ],
        'project_accountant' => [
            'view-projects', 'view-project-budgets', 'view-budgets',
            'view-sales-invoices', 'create-sales-invoices', 'view-expenses',
            'create-expenses', 'view-cost-centres', 'view-reports',
        ],

        // ══════════════════════════════════════════════════
        // SECTION G — Technical Roles
        // ══════════════════════════════════════════════════
        'senior_systems_engineer' => [
            'view-projects', 'view-helpdesk-tickets', 'create-helpdesk-tickets',
            'edit-helpdesk-tickets', 'view-documents', 'create-documents',
            'edit-documents', 'view-timesheets', 'create-timesheets', 'view-bugs',
            'create-bugs', 'view-job-cards', 'edit-job-cards', 'view-assets',
        ],
        'systems_engineer' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
            'view-assets', 'view-projects', 'view-bugs', 'create-bugs',
            'view-maintenance', 'create-maintenance', 'view-documents',
            'view-timesheets', 'create-timesheets',
        ],
        'network_engineer' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
            'view-assets', 'view-projects', 'view-bugs', 'create-bugs',
            'view-maintenance', 'create-maintenance', 'view-documents',
            'view-timesheets', 'create-timesheets',
        ],
        'software_engineer' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
            'view-projects', 'view-bugs', 'create-bugs', 'view-documents',
            'create-documents', 'view-timesheets', 'create-timesheets',
        ],
        'cybersecurity_engineer' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
            'view-assets', 'view-audit-logs', 'view-login-history', 'view-documents',
            'view-timesheets', 'create-timesheets',
        ],
        'support_engineer' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
            'view-job-cards', 'create-job-cards', 'edit-job-cards', 'view-assets',
            'view-site-reports', 'create-site-reports', 'view-timesheets',
            'create-timesheets',
        ],
        'field_technician' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
            'view-job-cards', 'create-job-cards', 'edit-job-cards', 'view-assets',
            'view-site-reports', 'create-site-reports', 'view-timesheets',
            'create-timesheets',
        ],
        'noc_engineer' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
        ],
        'ict_administrator' => [
            'view-users', 'create-users', 'edit-users', 'view-roles', 'view-settings',
            'view-audit-logs', 'view-login-history', 'view-helpdesk-tickets',
            'create-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-assets',
            'create-assets', 'edit-assets', 'view-employees', 'view-companies',
        ],

        // ══════════════════════════════════════════════════
        // SECTION H — Service Desk Roles
        // ══════════════════════════════════════════════════
        'service_desk_manager' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
            'view-calls', 'create-calls', 'edit-calls', 'view-call-logs',
            'view-crm-contacts', 'view-employees', 'view-reports', 'view-attendance',
        ],
        'helpdesk_supervisor' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
            'view-calls', 'view-call-logs', 'view-crm-contacts', 'view-attendance',
        ],
        'helpdesk_officer' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets',
            'view-crm-contacts', 'create-crm-contacts', 'view-calls', 'create-calls',
        ],
        'call_center_supervisor' => [
            'view-calls', 'create-calls', 'edit-calls', 'delete-calls',
            'view-call-logs', 'view-helpdesk-tickets', 'create-helpdesk-tickets',
            'view-attendance', 'view-employees', 'view-crm-leads', 'view-crm-contacts',
        ],
        'call_center_agent' => [
            'view-calls', 'create-calls', 'edit-calls', 'view-call-logs',
            'view-crm-leads', 'create-crm-leads', 'view-crm-contacts',
            'create-crm-contacts', 'view-helpdesk-tickets', 'create-helpdesk-tickets',
            'view-attendance',
        ],

        // ══════════════════════════════════════════════════
        // SECTION I — HR Roles
        // ══════════════════════════════════════════════════
        'hr_manager' => [
            'view-employees', 'create-employees', 'edit-employees', 'view-attendance',
            'create-attendance', 'view-overtime', 'approve-overtime', 'view-payroll',
            'create-payroll', 'view-leaves', 'approve-leaves', 'view-performance',
            'create-performance', 'view-training', 'create-training',
            'view-certifications', 'create-certifications', 'view-recruitment',
            'create-recruitment', 'view-job-postings', 'create-job-postings',
            'view-applications', 'view-approvals', 'approve-hr', 'view-disciplinary',
            'view-assets', 'view-policies', 'create-policies', 'view-events',
            'create-events',
        ],
        'hr_officer' => [
            'view-employees', 'create-employees', 'edit-employees', 'view-attendance',
            'create-attendance', 'view-leaves', 'create-leaves', 'view-recruitment',
            'view-job-postings', 'view-applications', 'view-training', 'view-policies',
            'view-events', 'create-events', 'view-performance',
        ],
        'recruitment_officer' => [
            'view-recruitment', 'create-recruitment', 'view-job-postings',
            'create-job-postings', 'view-applications', 'view-employees',
        ],
        'training_officer' => [
            'view-training', 'create-training', 'view-certifications',
            'create-certifications', 'view-employees',
        ],
        'time_attendance_officer' => [
            'view-attendance', 'create-attendance', 'view-overtime', 'approve-overtime',
            'view-employees',
        ],

        // ══════════════════════════════════════════════════
        // SECTION J — Operations Roles
        // ══════════════════════════════════════════════════
        'operations_officer' => [
            'view-helpdesk-tickets', 'create-helpdesk-tickets', 'view-deliveries',
            'create-deliveries', 'view-vehicles', 'view-projects', 'view-meetings',
            'create-meetings',
        ],
        'fleet_manager' => [
            'view-fleet', 'view-vehicles', 'create-vehicles', 'edit-vehicles',
            'delete-vehicles', 'view-fuel-logs', 'create-fuel-logs', 'view-maintenance',
            'create-maintenance', 'view-employees', 'view-deliveries',
        ],
        'logistics_officer' => [
            'view-deliveries', 'create-deliveries', 'view-shipments', 'create-shipments',
            'view-transfers', 'view-stock-movements', 'view-warehouses', 'view-products',
        ],
        // ══════════════════════════════════════════════════
        // SECTION K — Self-Service Roles
        // ══════════════════════════════════════════════════
        'employee_self_service' => [],
        'manager_self_service' => [
            'view-team-overview', 'approve-team-leaves', 'view-team-attendance',
            'view-team-timesheets', 'approve-team-timesheets', 'view-leaves',
        ],
    ];

    /**
     * Get the hardcoded permission list for a role name.
     * admin / superadmin get everything. Every other known role gets its
     * specific permissions merged with the universal self-service baseline.
     * Unknown roles get the employee self-service baseline only (least
     * privilege default).
     */
    public static function forRole(?string $role): array
    {
        if ($role === 'admin' || $role === 'superadmin') {
            return self::ALL_PERMISSIONS;
        }

        $rolePerms = self::ROLE_PERMISSIONS[$role] ?? [];

        return array_values(array_unique(array_merge($rolePerms, self::SELF_SERVICE)));
    }

    /**
     * All role names known to the system (excluding admin/superadmin which
     * bypass permission checks entirely).
     */
    public static function roleNames(): array
    {
        return array_merge(['admin', 'superadmin'], array_keys(self::ROLE_PERMISSIONS));
    }
}
