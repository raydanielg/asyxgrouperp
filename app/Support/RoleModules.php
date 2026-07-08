<?php

namespace App\Support;

/**
 * Single source of truth for what a non-admin role is allowed to see: which
 * "modules" (role.page slugs) appear in their sidebar and are reachable via
 * /role/{module}. Used by RolePageController (access gate), RoleDashboardController
 * (quick actions) and the sidebar in layouts/admin.blade.php.
 *
 * Any role NOT explicitly curated below (e.g. a brand-new custom role created
 * later in Roles & Permissions) automatically gets a sensible menu derived from
 * whatever permissions were assigned to it, via MODULE_PERMISSION_MAP. Nothing
 * needs to be hand-coded here for new roles to work.
 */
class RoleModules
{
    /**
     * Curated modules per known role, in display order.
     */
    private const CURATED = [
        'managing_director' => ['dashboard', 'companies', 'reports', 'approvals', 'tenders', 'contracts', 'employees', 'my-account', 'payslips', 'salary'],
        'general_manager' => ['dashboard', 'reports', 'approvals', 'projects', 'leads', 'employees', 'my-account', 'payslips', 'salary'],
        'technical_manager' => ['dashboard', 'tickets', 'projects', 'timesheets', 'bugs', 'lpos', 'assets', 'employees', 'my-account', 'payslips', 'salary'],
        'operations_manager' => ['dashboard', 'products', 'warehouses', 'stock-movements', 'sales-invoices', 'purchase-invoices', 'projects', 'reports', 'my-account', 'payslips', 'salary'],

        'finance_manager' => ['dashboard', 'journal-entries', 'petty-cash', 'financial-reports', 'purchase-invoices', 'sales-invoices', 'bank-accounts', 'budgets', 'reports', 'approvals', 'payroll', 'my-account', 'payslips', 'salary'],
        'chief_accountant' => ['dashboard', 'journal-entries', 'petty-cash', 'financial-reports', 'bank-reconciliation', 'reports', 'tax-management', 'sales-invoices', 'purchase-invoices', 'expenses', 'revenues', 'bills', 'my-account', 'payslips', 'salary'],
        'accountant' => ['dashboard', 'journal-entries', 'petty-cash', 'sales-invoices', 'purchase-invoices', 'expenses', 'cost-centres', 'my-account', 'payslips', 'salary'],
        'accounts_receivable_officer' => ['dashboard', 'sales-invoices', 'receivables-aging', 'revenues', 'credit-notes', 'my-account', 'payslips', 'salary'],
        'accounts_payable_officer' => ['dashboard', 'purchase-invoices', 'acc-transfers', 'payables-aging', 'bills', 'my-account', 'payslips', 'salary'],
        'payroll_officer' => ['dashboard', 'payroll', 'salary-records', 'deductions', 'payslips', 'employees', 'my-account', 'salary'],
        'budget_officer' => ['dashboard', 'budgets', 'budget-vs-actual', 'cost-centres', 'reports', 'expenses', 'my-account', 'payslips', 'salary'],
        'credit_controller' => ['dashboard', 'credit-limits', 'overdue-accounts', 'collections', 'sales-invoices', 'my-account', 'payslips', 'salary'],
        'cashier' => ['dashboard', 'pos', 'pos-reports', 'sales-invoices', 'products', 'revenues', 'my-account', 'payslips', 'salary'],

        'procurement_manager' => ['dashboard', 'suppliers', 'rfqs', 'approvals', 'reports', 'lpos', 'purchase-requisitions', 'my-account', 'payslips', 'salary'],
        'procurement_officer' => ['dashboard', 'rfqs', 'purchase-requisitions', 'lpos', 'grns', 'suppliers', 'my-account', 'payslips', 'salary'],
        'tender_officer' => ['dashboard', 'tenders', 'tender-calendar', 'documents', 'tender-costing', 'my-account', 'payslips', 'salary'],

        'store_manager' => ['dashboard', 'warehouses', 'transfers', 'reorder-levels', 'reports', 'stock-movements', 'products', 'suppliers', 'my-account', 'payslips', 'salary'],
        'storekeeper' => ['dashboard', 'stock-movements', 'grns', 'stock-count', 'products', 'my-account', 'payslips', 'salary'],
        'inventory_controller' => ['dashboard', 'products', 'batch-tracking', 'barcodes', 'reports', 'warehouses', 'my-account', 'payslips', 'salary'],
        'asset_officer' => ['dashboard', 'assets', 'asset-assignment', 'asset-maintenance', 'asset-disposal', 'employees', 'my-account', 'payslips', 'salary'],

        'sales_manager' => ['dashboard', 'deals', 'sales-forecast', 'quotations', 'reports', 'leads', 'sales-invoices', 'my-account', 'payslips', 'salary'],
        'business_development_manager' => ['dashboard', 'leads', 'deals', 'market-analysis', 'my-account', 'payslips', 'salary'],
        'sales_executive' => ['dashboard', 'leads', 'deals', 'quotations', 'calls', 'my-account', 'payslips', 'salary'],
        'crm_officer' => ['dashboard', 'contacts', 'calls', 'correspondence', 'leads', 'deals', 'my-account', 'payslips', 'salary'],
        'marketing_officer' => ['dashboard', 'campaigns', 'lead-source-reports', 'documents', 'my-account', 'payslips', 'salary'],

        'project_director' => ['dashboard', 'reports', 'projects', 'budgets', 'timesheets', 'my-account', 'payslips', 'salary'],
        'project_manager' => ['dashboard', 'projects', 'timesheets', 'bugs', 'employees', 'deals', 'reports', 'my-account', 'payslips', 'salary'],
        'technical_projects_manager' => ['dashboard', 'projects', 'resource-allocation', 'milestones', 'timesheets', 'bugs', 'my-account', 'payslips', 'salary'],
        'project_coordinator' => ['dashboard', 'tasks', 'documents', 'meetings', 'projects', 'my-account', 'payslips', 'salary'],
        'project_engineer' => ['dashboard', 'tasks', 'site-reports', 'timesheets', 'projects', 'my-account', 'payslips', 'salary'],
        'site_supervisor' => ['dashboard', 'attendance', 'site-reports', 'incidents', 'tickets', 'my-account', 'payslips', 'salary'],
        'team_leader' => ['dashboard', 'team-tasks', 'team-attendance', 'team-timesheets', 'my-account', 'payslips', 'salary'],
        'project_accountant' => ['dashboard', 'budget-vs-actual', 'sales-invoices', 'cost-centres', 'expenses', 'revenues', 'my-account', 'payslips', 'salary'],

        'senior_systems_engineer' => ['dashboard', 'projects', 'documents', 'team-review', 'tickets', 'my-account', 'payslips', 'salary'],
        'systems_engineer' => ['dashboard', 'tickets', 'assets', 'asset-maintenance', 'my-account', 'payslips', 'salary'],
        'support_engineer' => ['dashboard', 'site-visits', 'service-reports', 'assets', 'tickets', 'my-account', 'payslips', 'salary'],
        'noc_engineer' => ['dashboard', 'tickets', 'escalations', 'assets', 'my-account', 'payslips', 'salary'],
        'network_engineer' => ['dashboard', 'tickets', 'assets', 'projects', 'my-account', 'payslips', 'salary'],
        'software_engineer' => ['dashboard', 'projects', 'bugs', 'timesheets', 'my-account', 'payslips', 'salary'],
        'cybersecurity_engineer' => ['dashboard', 'tickets', 'assets', 'my-account', 'payslips', 'salary'],
        'field_technician' => ['dashboard', 'tickets', 'projects', 'timesheets', 'bugs', 'my-account', 'payslips', 'salary'],
        'technician' => ['dashboard', 'tickets', 'projects', 'timesheets', 'bugs', 'my-account', 'payslips', 'salary'],

        'service_desk_manager' => ['dashboard', 'tickets', 'sla-reports', 'reports', 'call-statistics', 'my-account', 'payslips', 'salary'],
        'helpdesk_supervisor' => ['dashboard', 'tickets', 'reports', 'escalations', 'call-statistics', 'my-account', 'payslips', 'salary'],
        'helpdesk_officer' => ['dashboard', 'tickets', 'knowledge-base', 'calls', 'my-account', 'payslips', 'salary'],
        'call_center_supervisor' => ['dashboard', 'call-statistics', 'shift-schedule', 'sla-reports', 'my-account', 'payslips', 'salary'],
        'call_center_agent' => ['dashboard', 'leads', 'contacts', 'tickets', 'my-account', 'payslips', 'salary'],
        'receptionist' => ['dashboard', 'visitors', 'appointments', 'calls', 'correspondence', 'parcels', 'front-desk', 'departments', 'announcements', 'messages', 'salary-advance', 'my-account', 'payslips', 'salary'],

        'hr_manager' => ['dashboard', 'employees', 'recruitment', 'leaves', 'payroll', 'disciplinary', 'performance', 'my-account', 'payslips', 'salary'],
        'hr_officer' => ['dashboard', 'employees', 'attendance', 'leaves', 'performance', 'training', 'recruitment', 'assets', 'policies', 'my-account', 'payslips', 'salary'],
        'recruitment_officer' => ['dashboard', 'job-postings', 'applications', 'onboarding', 'my-account', 'payslips', 'salary'],
        'training_officer' => ['dashboard', 'training', 'training-records', 'certifications', 'employees', 'my-account', 'payslips', 'salary'],
        'time_and_attendance_officer' => ['dashboard', 'attendance', 'shift-schedule', 'overtime', 'employees', 'my-account', 'payslips', 'salary'],

        'operations_officer' => ['dashboard', 'operations-log', 'operations-tasks', 'tickets', 'my-account', 'payslips', 'salary'],
        'fleet_manager' => ['dashboard', 'vehicles', 'driver-assignment', 'fuel-logs', 'trip-schedule', 'my-account', 'payslips', 'salary'],
        'logistics_officer' => ['dashboard', 'deliveries', 'shipments', 'route-planning', 'products', 'warehouses', 'my-account', 'payslips', 'salary'],

        'legal_officer' => ['dashboard', 'contracts', 'contacts', 'projects', 'reports', 'my-account', 'payslips', 'salary'],
        'supervisor' => ['dashboard', 'employees', 'attendance', 'leaves', 'projects', 'pos', 'products', 'reports', 'my-account', 'payslips', 'salary'],
        'director' => ['dashboard', 'reports', 'projects', 'sales-dashboard', 'employees', 'sales-invoices', 'purchase-invoices', 'expenses', 'tickets', 'my-account', 'payslips', 'salary'],
        'admin_manager' => ['dashboard', 'users', 'roles', 'employees', 'attendance', 'leaves', 'reports', 'settings', 'my-account', 'payslips', 'salary'],
        'administrator' => ['dashboard', 'users', 'roles', 'employees', 'projects', 'products', 'settings', 'reports', 'my-account', 'payslips', 'salary'],

        'erp_super_administrator' => ['dashboard', 'companies', 'users', 'roles', 'employees', 'projects', 'products', 'settings', 'system-control', 'database-backup', 'reports', 'my-account', 'payslips', 'salary'],
        'erp_administrator' => ['dashboard', 'users', 'roles', 'employees', 'attendance', 'leaves', 'reports', 'settings', 'my-account', 'payslips', 'salary'],
        'ict_administrator' => ['dashboard', 'tickets', 'projects', 'assets', 'settings', 'employees', 'my-account', 'payslips', 'salary'],
        'ict_officer' => ['dashboard', 'tickets', 'projects', 'bugs', 'assets', 'employees', 'my-account', 'payslips', 'salary'],
        'ict_engineer' => ['dashboard', 'tickets', 'projects', 'bugs', 'assets', 'settings', 'my-account', 'payslips', 'salary'],

        'employee_self_service' => ['dashboard', 'my-account', 'payslips', 'leaves', 'attendance', 'timesheets', 'announcements', 'salary'],
        'manager_self_service' => ['dashboard', 'my-account', 'payslips', 'leaves', 'attendance', 'timesheets', 'team-overview', 'team-leaves', 'team-timesheets', 'announcements', 'salary'],

        'sgr_agent' => ['dashboard', 'import-action-points', 'action-points-reports', 'my-account', 'payslips', 'salary'],
    ];

    /**
     * Fallback for any role NOT listed above (custom roles created later):
     * a permission => module map. If the user holds the permission, the module
     * is added to their sidebar automatically.
     */
    private const MODULE_PERMISSION_MAP = [
        'view-companies' => 'companies',
        'view-reports' => 'reports',
        'view-approvals' => 'approvals',
        'view-tenders' => 'tenders',
        'view-crm-contracts' => 'contracts',
        'view-employees' => 'employees',
        'view-projects' => 'projects',
        'view-crm-leads' => 'leads',
        'view-journal-entries' => 'journal-entries',
        'view-petty-cash' => 'petty-cash',
        'view-financial-reports' => 'financial-reports',
        'view-purchase-invoices' => 'purchase-invoices',
        'view-sales-invoices' => 'sales-invoices',
        'view-bank-accounts' => 'bank-accounts',
        'view-budgets' => 'budgets',
        'view-payroll' => 'payroll',
        'view-bank-reconciliation' => 'bank-reconciliation',
        'view-tax-management' => 'tax-management',
        'view-expenses' => 'expenses',
        'view-revenues' => 'revenues',
        'view-bills' => 'bills',
        'view-cost-centres' => 'cost-centres',
        'view-credit-limits' => 'credit-limits',
        'view-suppliers' => 'suppliers',
        'view-rfqs' => 'rfqs',
        'view-purchase-requisitions' => 'purchase-requisitions',
        'view-lpos' => 'lpos',
        'view-grns' => 'grns',
        'view-warehouses' => 'warehouses',
        'view-transfers' => 'transfers',
        'view-stock-movements' => 'stock-movements',
        'view-products' => 'products',
        'view-product-categories' => 'batch-tracking',
        'view-assets' => 'assets',
        'view-crm-deals' => 'deals',
        'view-quotations' => 'quotations',
        'view-campaigns' => 'campaigns',
        'view-crm-contacts' => 'contacts',
        'view-call-logs' => 'call-statistics',
        'view-timesheets' => 'timesheets',
        'view-bugs' => 'bugs',
        'view-site-reports' => 'site-reports',
        'view-helpdesk-tickets' => 'tickets',
        'view-training' => 'training',
        'view-certifications' => 'certifications',
        'view-overtime' => 'overtime',
        'view-vehicles' => 'vehicles',
        'view-fuel-logs' => 'fuel-logs',
        'view-maintenance' => 'asset-maintenance',
        'view-deliveries' => 'deliveries',
        'view-shipments' => 'shipments',
        'view-team-overview' => 'team-overview',
        'view-team-timesheets' => 'team-tasks',
        'view-announcements' => 'announcements',
        'view-attendance' => 'attendance',
        'view-leaves' => 'leaves',
        'view-performance' => 'performance',
        'view-recruitment' => 'recruitment',
        'view-job-postings' => 'job-postings',
        'view-applications' => 'applications',
        'view-policies' => 'policies',
        'view-events' => 'hr-events',
        'view-users' => 'users',
        'view-roles' => 'roles',
        'view-settings' => 'settings',
        'view-visitors' => 'visitors',
        'view-appointments' => 'appointments',
        'view-correspondence' => 'correspondence',
        'view-parcels' => 'parcels',
        'view-front-desk' => 'front-desk',
        'view-departments' => 'departments',
        'view-messages' => 'messages',
        'view-pos' => 'pos',
    ];

    /**
     * Friendly label + icon overrides. Anything not listed gets an
     * auto-generated label (title-cased slug) and a default icon.
     */
    private const META = [
        'dashboard' => ['label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        'my-account' => ['label' => 'My Account', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        'payslips' => ['label' => 'Payslips', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'salary' => ['label' => 'Salary', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'employees' => ['label' => 'Employees', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        'projects' => ['label' => 'Projects', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        'expenses' => ['label' => 'Expenses', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
        'revenues' => ['label' => 'Revenue', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'bills' => ['label' => 'Bills', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
        'bank-accounts' => ['label' => 'Bank Accounts', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
        'petty-cash' => ['label' => 'Petty Cash', 'icon' => 'M9 7h6m0 10v-3m-3 3v-3m-3 3v-3m9-7H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2z'],
        'financial-reports' => ['label' => 'Financial Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        'journal-entries' => ['label' => 'Journal Entries', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'sales-invoices' => ['label' => 'Sales Invoices', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'purchase-invoices' => ['label' => 'Purchase Invoices', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        'reports' => ['label' => 'Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        'approvals' => ['label' => 'Approvals', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        'attendance' => ['label' => 'Attendance', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        'leaves' => ['label' => 'Leaves', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        'payroll' => ['label' => 'Payroll', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'products' => ['label' => 'Products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'warehouses' => ['label' => 'Warehouses', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
        'stock-movements' => ['label' => 'Stock Movements', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        'suppliers' => ['label' => 'Suppliers', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
        'assets' => ['label' => 'Assets', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'leads' => ['label' => 'Leads', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        'deals' => ['label' => 'Deals', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'contacts' => ['label' => 'Contacts', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        'contracts' => ['label' => 'Contracts', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
        'tickets' => ['label' => 'Helpdesk Tickets', 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'timesheets' => ['label' => 'Timesheets', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        'bugs' => ['label' => 'Bugs', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
        'users' => ['label' => 'Users', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        'roles' => ['label' => 'Roles & Permissions', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
        'settings' => ['label' => 'Settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
        'pos' => ['label' => 'POS Terminal', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
        'pos-reports' => ['label' => 'POS Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10'],
        'sales-dashboard' => ['label' => 'Sales Dashboard', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17'],
        'quotations' => ['label' => 'Quotations', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'companies' => ['label' => 'Companies', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
        'system-control' => ['label' => 'System Control', 'icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'],
        'database-backup' => ['label' => 'Database Backup', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
        'tenders' => ['label' => 'Tenders', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'budgets' => ['label' => 'Budgets', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'lpos' => ['label' => 'LPOs', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        'grns' => ['label' => 'GRNs', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'rfqs' => ['label' => 'RFQs', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'documents' => ['label' => 'Documents', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'meetings' => ['label' => 'Meetings', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zM6 3v2m12-2v2M3 8h2m14 0h2'],
        'visitors' => ['label' => 'Visitors', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        'appointments' => ['label' => 'Appointments', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        'calls' => ['label' => 'Calls', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
        'correspondence' => ['label' => 'Correspondence', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        'parcels' => ['label' => 'Parcels', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'front-desk' => ['label' => 'Front Desk', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
        'departments' => ['label' => 'Departments', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
        'announcements' => ['label' => 'Announcements', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
        'messages' => ['label' => 'Messages', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
        'salary-advance' => ['label' => 'Salary Advance', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8'],
        'training' => ['label' => 'Training', 'icon' => 'M12 14l9-5-9-5-9 5 9 5z'],
        'recruitment' => ['label' => 'Recruitment', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        'job-postings' => ['label' => 'Job Postings', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        'applications' => ['label' => 'Applications', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        'policies' => ['label' => 'Policies', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'performance' => ['label' => 'Performance', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        'vehicles' => ['label' => 'Vehicles', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        'fuel-logs' => ['label' => 'Fuel Logs', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
        'deliveries' => ['label' => 'Deliveries', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        'shipments' => ['label' => 'Shipments', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'team-overview' => ['label' => 'Team Overview', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857'],
        'team-leaves' => ['label' => 'Team Leaves', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        'team-timesheets' => ['label' => 'Team Timesheets', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],

        'import-action-points' => ['label' => 'Upload Action Points', 'icon' => 'M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3'],
        'action-points-reports' => ['label' => 'My Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    ];

    private const DEFAULT_ICON = 'M4 6h16M4 10h16M4 14h16M4 18h16';

    /**
     * Modules every logged-in role user gets, appended if not already present.
     */
    private const BASE_MODULES = ['dashboard', 'my-account', 'payslips', 'salary'];

    public static function allowedModules(string $role, $user = null): array
    {
        $modules = self::CURATED[$role] ?? self::deriveFromPermissions($user);

        foreach (self::BASE_MODULES as $m) {
            if (!in_array($m, $modules, true)) {
                $modules[] = $m;
            }
        }

        return array_values(array_unique($modules));
    }

    private static function deriveFromPermissions($user): array
    {
        $modules = ['dashboard'];
        if (!$user) {
            return $modules;
        }

        foreach (self::MODULE_PERMISSION_MAP as $permission => $module) {
            if ($user->hasPermission($permission)) {
                $modules[] = $module;
            }
        }

        return $modules;
    }

    public static function label(string $module): string
    {
        return self::META[$module]['label'] ?? ucwords(str_replace('-', ' ', $module));
    }

    public static function icon(string $module): string
    {
        return self::META[$module]['icon'] ?? self::DEFAULT_ICON;
    }
}
