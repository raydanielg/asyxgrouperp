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
     * Menus follow roles-sidebar-menu.md — each role sees only what its
     * function requires, plus the universal self-service baseline.
     */
    private const CURATED = [
        // ═══ Executive ═══
        'managing_director' => ['dashboard', 'companies', 'intercompany', 'reports', 'financial-reports', 'approvals', 'tenders', 'contracts', 'projects', 'employees', 'leads', 'deals', 'sales-invoices', 'purchase-invoices', 'expenses', 'revenues', 'bank-accounts', 'budgets', 'payroll', 'tickets', 'products', 'warehouses', 'pos', 'my-account', 'payslips'],
        'director' => ['dashboard', 'companies', 'reports', 'approvals', 'tenders', 'contracts', 'employees', 'projects', 'leads', 'deals', 'sales-invoices', 'purchase-invoices', 'expenses', 'revenues', 'tickets', 'products', 'warehouses', 'pos', 'my-account', 'payslips'],
        'general_manager' => ['dashboard', 'reports', 'approvals', 'projects', 'leads', 'deals', 'quotations', 'sales-invoices', 'tenders', 'contracts', 'employees', 'attendance', 'leaves', 'tickets', 'products', 'warehouses', 'budgets', 'expenses', 'revenues', 'my-account', 'payslips'],
        'technical_manager' => ['dashboard', 'projects', 'job-cards', 'tickets', 'timesheets', 'bugs', 'lpos', 'approvals', 'assets', 'employees', 'settings', 'audit-logs', 'my-account', 'payslips'],
        'operations_manager' => ['dashboard', 'vehicles', 'fuel-logs', 'maintenance', 'tickets', 'deliveries', 'shipments', 'products', 'warehouses', 'stock-movements', 'reports', 'my-account', 'payslips'],

        // ═══ Finance ═══
        'finance_manager' => ['dashboard', 'journal-entries', 'financial-reports', 'sales-invoices', 'purchase-invoices', 'expenses', 'revenues', 'bills', 'bank-accounts', 'acc-transfers', 'petty-cash', 'bank-reconciliation', 'tax-management', 'credit-limits', 'budgets', 'cost-centres', 'payroll', 'approvals', 'reports', 'my-account', 'payslips'],
        'chief_accountant' => ['dashboard', 'journal-entries', 'bank-reconciliation', 'financial-reports', 'tax-management', 'expenses', 'revenues', 'bills', 'bank-accounts', 'acc-transfers', 'cost-centres', 'reports', 'my-account', 'payslips'],
        'accountant' => ['dashboard', 'journal-entries', 'petty-cash', 'sales-invoices', 'purchase-invoices', 'expenses', 'revenues', 'bills', 'cost-centres', 'financial-reports', 'reports', 'bank-accounts', 'acc-transfers', 'my-account', 'payslips'],
        'accounts_receivable_officer' => ['dashboard', 'sales-invoices', 'receivables-aging', 'client-receipts', 'credit-notes', 'revenues', 'contacts', 'deals', 'my-account', 'payslips'],
        'accounts_payable_officer' => ['dashboard', 'purchase-invoices', 'vendor-invoices', 'vendor-payments', 'payables-aging', 'bills', 'expenses', 'suppliers', 'my-account', 'payslips'],
        'cashier' => ['dashboard', 'petty-cash', 'pos', 'pos-reports', 'client-receipts', 'vendor-payments', 'bank-accounts', 'acc-transfers', 'sales-invoices', 'my-account', 'payslips'],
        'payroll_officer' => ['dashboard', 'payroll', 'salary-records', 'deductions', 'payslips-all', 'employees', 'attendance', 'overtime', 'leaves', 'my-account', 'payslips'],
        'budget_officer' => ['dashboard', 'budgets', 'budget-vs-actual', 'cost-centre-budgets', 'cost-centres', 'project-budgets', 'reports', 'my-account', 'payslips'],
        'credit_controller' => ['dashboard', 'credit-limits', 'overdue-accounts', 'collections', 'receivables-aging', 'sales-invoices', 'contacts', 'my-account', 'payslips'],
        'auditor' => ['dashboard', 'audit-logs', 'login-history', 'reports', 'financial-reports', 'journal-entries', 'sales-invoices', 'purchase-invoices', 'expenses', 'revenues', 'bills', 'bank-accounts', 'payroll', 'projects', 'contracts', 'tenders', 'documents', 'my-account', 'payslips'],

        // ═══ Procurement ═══
        'procurement_manager' => ['dashboard', 'suppliers', 'rfqs', 'purchase-requisitions', 'lpos', 'grns', 'purchase-invoices', 'vendor-payments', 'approvals', 'products', 'warehouses', 'stock-movements', 'transfers', 'assets', 'reports', 'my-account', 'payslips'],
        'procurement_officer' => ['dashboard', 'rfqs', 'purchase-requisitions', 'lpos', 'grns', 'suppliers', 'delivery-notes', 'purchase-invoices', 'products', 'my-account', 'payslips'],
        'tender_officer' => ['dashboard', 'tenders', 'documents', 'leads', 'quotations', 'my-account', 'payslips'],

        // ═══ Inventory ═══
        'store_manager' => ['dashboard', 'warehouses', 'stock-movements', 'transfers', 'reorder-levels', 'inventory-reports', 'products', 'grns', 'deliveries', 'reports', 'my-account', 'payslips'],
        'storekeeper' => ['dashboard', 'stock-movements', 'grns', 'stock-count', 'products', 'warehouses', 'deliveries', 'my-account', 'payslips'],
        'inventory_controller' => ['dashboard', 'products', 'product-categories', 'batch-tracking', 'barcodes', 'variance-reports', 'stock-movements', 'warehouses', 'transfers', 'my-account', 'payslips'],
        'asset_officer' => ['dashboard', 'assets', 'asset-assignment', 'asset-maintenance', 'asset-disposal', 'employees', 'my-account', 'payslips'],

        // ═══ Sales ═══
        'sales_manager' => ['dashboard', 'leads', 'deals', 'contacts', 'quotations', 'sales-invoices', 'sales-forecast', 'campaigns', 'tenders', 'reports', 'my-account', 'payslips'],
        'business_development_manager' => ['dashboard', 'leads', 'deals', 'market-analysis', 'contacts', 'tenders', 'quotations', 'campaigns', 'reports', 'my-account', 'payslips'],
        'sales_executive' => ['dashboard', 'leads', 'deals', 'contacts', 'quotations', 'my-account', 'payslips'],
        'crm_officer' => ['dashboard', 'contacts', 'leads', 'calls', 'tickets', 'my-account', 'payslips'],
        'marketing_officer' => ['dashboard', 'campaigns', 'lead-source-reports', 'leads', 'documents', 'announcements', 'my-account', 'payslips'],

        // ═══ Projects ═══
        'project_director' => ['dashboard', 'project-profitability', 'resource-allocation', 'projects', 'budgets', 'deals', 'approvals', 'reports', 'my-account', 'payslips'],
        'project_manager' => ['dashboard', 'projects', 'timesheets', 'job-cards', 'bugs', 'employees', 'deals', 'budgets', 'site-reports', 'meetings', 'documents', 'reports', 'my-account', 'payslips'],
        'technical_projects_manager' => ['dashboard', 'projects', 'job-cards', 'resource-allocation', 'milestones', 'timesheets', 'bugs', 'tickets', 'employees', 'site-reports', 'meetings', 'documents', 'my-account', 'payslips'],
        'project_coordinator' => ['dashboard', 'projects', 'tasks', 'documents', 'meetings', 'timesheets', 'site-reports', 'bugs', 'my-account', 'payslips'],
        'project_engineer' => ['dashboard', 'tasks', 'site-reports', 'timesheets', 'projects', 'bugs', 'job-cards', 'my-account', 'payslips'],
        'site_supervisor' => ['dashboard', 'team-attendance', 'site-reports', 'incidents', 'timesheets', 'projects', 'bugs', 'my-account', 'payslips'],
        'team_leader' => ['dashboard', 'team-tasks', 'team-attendance', 'team-timesheets', 'team-leaves', 'timesheets', 'tasks', 'my-account', 'payslips'],
        'project_accountant' => ['dashboard', 'project-profitability', 'budgets', 'sales-invoices', 'expenses', 'cost-centres', 'cost-allocations', 'reports', 'my-account', 'payslips'],

        // ═══ Technical ═══
        'senior_systems_engineer' => ['dashboard', 'projects', 'tickets', 'team-review', 'documents', 'timesheets', 'bugs', 'job-cards', 'assets', 'my-account', 'payslips'],
        'systems_engineer' => ['dashboard', 'tickets', 'assets', 'projects', 'bugs', 'maintenance', 'documents', 'timesheets', 'my-account', 'payslips'],
        'network_engineer' => ['dashboard', 'tickets', 'assets', 'projects', 'bugs', 'maintenance', 'documents', 'timesheets', 'my-account', 'payslips'],
        'software_engineer' => ['dashboard', 'tickets', 'projects', 'bugs', 'documents', 'timesheets', 'my-account', 'payslips'],
        'cybersecurity_engineer' => ['dashboard', 'tickets', 'assets', 'audit-logs', 'login-history', 'documents', 'timesheets', 'my-account', 'payslips'],
        'support_engineer' => ['dashboard', 'site-visits', 'service-reports', 'job-cards', 'customer-assets', 'tickets', 'timesheets', 'my-account', 'payslips'],
        'field_technician' => ['dashboard', 'site-visits', 'service-reports', 'job-cards', 'customer-assets', 'tickets', 'timesheets', 'my-account', 'payslips'],
        'technician' => ['dashboard', 'projects', 'job-cards', 'tickets', 'bugs', 'my-account', 'payslips'],
        'noc_engineer' => ['dashboard', 'monitoring-dashboard', 'incidents', 'escalations', 'tickets', 'my-account', 'payslips'],
        'ict_administrator' => ['dashboard', 'users', 'roles', 'settings', 'audit-logs', 'login-history', 'tickets', 'assets', 'employees', 'my-account', 'payslips'],

        // ═══ Service Desk ═══
        'service_desk_manager' => ['dashboard', 'tickets', 'sla-reports', 'escalations', 'call-statistics', 'employees', 'reports', 'my-account', 'payslips'],
        'helpdesk_supervisor' => ['dashboard', 'tickets', 'escalations', 'call-statistics', 'knowledge-base', 'my-account', 'payslips'],
        'helpdesk_officer' => ['dashboard', 'tickets', 'knowledge-base', 'contacts', 'calls', 'my-account', 'payslips'],
        'call_center_supervisor' => ['dashboard', 'call-statistics', 'shift-schedule', 'sla-reports', 'calls', 'tickets', 'employees', 'my-account', 'payslips'],
        'call_center_agent' => ['dashboard', 'calls', 'shift-schedule', 'call-statistics', 'leads', 'contacts', 'tickets', 'my-account', 'payslips'],

        // ═══ HR ═══
        'hr_manager' => ['dashboard', 'employees', 'attendance', 'leaves', 'payroll', 'performance', 'training', 'recruitment', 'disciplinary', 'approvals', 'assets', 'policies', 'hr-events', 'my-account', 'payslips'],
        'hr_officer' => ['dashboard', 'employees', 'attendance', 'leaves', 'job-postings', 'applications', 'training', 'policies', 'hr-events', 'my-account', 'payslips'],
        'recruitment_officer' => ['dashboard', 'job-postings', 'applications', 'onboarding', 'employees', 'my-account', 'payslips'],
        'training_officer' => ['dashboard', 'training', 'training-records', 'certifications', 'employees', 'my-account', 'payslips'],
        'time_attendance_officer' => ['dashboard', 'attendance', 'shift-schedule', 'overtime', 'employees', 'my-account', 'payslips'],

        // ═══ Operations ═══
        'operations_officer' => ['dashboard', 'operations-log', 'operations-tasks', 'tickets', 'deliveries', 'vehicles', 'meetings', 'my-account', 'payslips'],
        'fleet_manager' => ['dashboard', 'vehicles', 'driver-assignment', 'fuel-logs', 'maintenance', 'trip-schedule', 'employees', 'my-account', 'payslips'],
        'logistics_officer' => ['dashboard', 'deliveries', 'shipments', 'route-planning', 'transfers', 'stock-movements', 'warehouses', 'products', 'my-account', 'payslips'],
        'receptionist' => ['dashboard', 'visitors', 'appointments', 'calls', 'correspondence', 'parcels', 'front-desk', 'departments', 'announcements', 'messages', 'salary-advance', 'leads', 'contacts', 'my-account', 'payslips'],

        // ═══ Self-Service ═══
        'employee' => ['dashboard', 'my-account', 'payslips', 'leaves', 'attendance', 'timesheets', 'announcements', 'messages', 'salary-advance'],
        'employee_self_service' => ['dashboard', 'my-account', 'payslips', 'leaves', 'attendance', 'timesheets', 'announcements', 'messages', 'salary-advance'],
        'manager_self_service' => ['dashboard', 'team-overview', 'team-leaves', 'team-attendance', 'team-timesheets', 'my-account', 'payslips', 'leaves', 'attendance', 'timesheets', 'announcements', 'messages'],
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
        'view-job-cards' => 'job-cards',
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
        'view-calls' => 'calls',
        'view-payslips' => 'payslips-all',
        'view-project-budgets' => 'project-budgets',
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
        'job-cards' => ['label' => 'Job Cards', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6'],
        'action-points-reports' => ['label' => 'My Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],

        // ── Finance & Accounting ──
        'intercompany' => ['label' => 'Intercompany', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        'acc-transfers' => ['label' => 'Account Transfers', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        'bank-reconciliation' => ['label' => 'Bank Reconciliation', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        'tax-management' => ['label' => 'Tax Management', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
        'credit-limits' => ['label' => 'Credit Limits', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'cost-centres' => ['label' => 'Cost Centres', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
        'receivables-aging' => ['label' => 'Receivables Aging', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        'payables-aging' => ['label' => 'Payables Aging', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        'client-receipts' => ['label' => 'Client Receipts', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'credit-notes' => ['label' => 'Credit Notes', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
        'vendor-invoices' => ['label' => 'Vendor Invoices', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        'vendor-payments' => ['label' => 'Vendor Payments', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
        'salary-records' => ['label' => 'Salary Records', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'deductions' => ['label' => 'Statutory Deductions', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
        'payslips-all' => ['label' => 'All Payslips', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'budget-vs-actual' => ['label' => 'Budget vs Actual', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        'cost-centre-budgets' => ['label' => 'Cost Centre Budgets', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
        'project-budgets' => ['label' => 'Project Budgets', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'overdue-accounts' => ['label' => 'Overdue Accounts', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        'collections' => ['label' => 'Collections', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],

        // ── Procurement & Inventory ──
        'purchase-requisitions' => ['label' => 'Requisitions', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'delivery-notes' => ['label' => 'Delivery Notes', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'reorder-levels' => ['label' => 'Reorder Levels', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        'inventory-reports' => ['label' => 'Inventory Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10'],
        'stock-count' => ['label' => 'Stock Count', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        'product-categories' => ['label' => 'Product Categories', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'batch-tracking' => ['label' => 'Batch/Serial Tracking', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'barcodes' => ['label' => 'Barcodes', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
        'variance-reports' => ['label' => 'Stock Variance', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10'],
        'asset-assignment' => ['label' => 'Asset Assignment', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'asset-maintenance' => ['label' => 'Asset Maintenance', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31.826-2.37 2.37a1.724 1.724 0 00-1.065-2.572c-.426-1.756-2.924-1.756-3.35 0a1.724 1.724 0 00-2.573 1.066c-1.543-.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065 2.572c1.756-.426 1.756-2.924 0-3.35a1.724 1.724 0 00-1.066-2.573c.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
        'asset-disposal' => ['label' => 'Asset Disposal', 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],

        // ── Sales & CRM ──
        'sales-forecast' => ['label' => 'Sales Forecast', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        'market-analysis' => ['label' => 'Market Analysis', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        'lead-source-reports' => ['label' => 'Lead Sources', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],

        // ── Projects ──
        'project-profitability' => ['label' => 'Project Profitability', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        'resource-allocation' => ['label' => 'Resource Allocation', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        'milestones' => ['label' => 'Milestones', 'icon' => 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 5 3 5h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9'],
        'tasks' => ['label' => 'Tasks', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        'site-reports' => ['label' => 'Site Reports', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'incidents' => ['label' => 'Incidents', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        'team-review' => ['label' => 'Team Review', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],

        // ── Service Desk / Technical ──
        'site-visits' => ['label' => 'Site Visits', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
        'service-reports' => ['label' => 'Service Reports', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'escalations' => ['label' => 'Escalations', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
        'knowledge-base' => ['label' => 'Knowledge Base', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        'call-statistics' => ['label' => 'Call Statistics', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
        'shift-schedule' => ['label' => 'Shift Schedule', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        'sla-reports' => ['label' => 'SLA Reports', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        'monitoring-dashboard' => ['label' => 'NOC Monitoring', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        'customer-assets' => ['label' => 'Customer Assets', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'maintenance' => ['label' => 'Maintenance', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31.826-2.37 2.37a1.724 1.724 0 00-1.065-2.572c-.426-1.756-2.924-1.756-3.35 0a1.724 1.724 0 00-2.573 1.066c-1.543-.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065 2.572c1.756-.426 1.756-2.924 0-3.35a1.724 1.724 0 00-1.066-2.573c.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],

        // ── HR ──
        'disciplinary' => ['label' => 'Disciplinary', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        'onboarding' => ['label' => 'Onboarding', 'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
        'training-records' => ['label' => 'Training Records', 'icon' => 'M12 14l9-5-9-5-9 5 9 5z'],
        'certifications' => ['label' => 'Certifications', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
        'overtime' => ['label' => 'Overtime', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        'hr-events' => ['label' => 'HR Events', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],

        // ── Operations / Fleet ──
        'operations-log' => ['label' => 'Operations Log', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        'operations-tasks' => ['label' => 'Operations Tasks', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        'driver-assignment' => ['label' => 'Driver Assignment', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        'trip-schedule' => ['label' => 'Trip Schedule', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        'route-planning' => ['label' => 'Route Planning', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],

        // ── Administration ──
        'audit-logs' => ['label' => 'Audit Logs', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'login-history' => ['label' => 'Login History', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];

    private const DEFAULT_ICON = 'M4 6h16M4 10h16M4 14h16M4 18h16';

    /**
     * Modules every logged-in role user gets, appended if not already present.
     */
    private const BASE_MODULES = ['dashboard', 'my-account', 'payslips'];

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
