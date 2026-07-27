@extends('layouts.admin')
@section('title', ucfirst(str_replace('-', ' ', $module)) . ' - ' . $roleLabel)
@section('page_title', ucfirst(str_replace('-', ' ', $module)))
@section('content')
@php
$money = fn($n) => 'TZS ' . number_format($n);
$permMap = [
    'employees' => ['create' => 'create-employees', 'edit' => 'edit-employees', 'delete' => 'delete-employees'],
    'sales-invoices' => ['create' => 'create-sales-invoices', 'edit' => 'edit-sales-invoices', 'delete' => 'delete-sales-invoices'],
    'purchase-invoices' => ['create' => 'create-purchase-invoices', 'edit' => 'edit-purchase-invoices', 'delete' => 'delete-purchase-invoices'],
    'expenses' => ['create' => 'create-expenses', 'delete' => 'delete-expenses'],
    'revenues' => ['create' => 'create-revenues', 'delete' => 'delete-revenues'],
    'tickets' => ['create' => 'create-helpdesk-tickets', 'edit' => 'edit-helpdesk-tickets', 'delete' => 'delete-helpdesk-tickets'],
    'leads' => ['create' => 'create-crm-leads', 'edit' => 'edit-crm-leads', 'delete' => 'delete-crm-leads'],
    'contacts' => ['create' => 'create-crm-contacts', 'delete' => 'delete-crm-contacts'],
    'deals' => ['create' => 'create-crm-deals', 'edit' => 'edit-crm-deals', 'delete' => 'delete-crm-deals'],
    'contracts' => ['create' => 'create-crm-contracts', 'delete' => 'delete-crm-contracts'],
    'products' => ['create' => 'create-products', 'edit' => 'edit-products', 'delete' => 'delete-products'],
    'warehouses' => ['create' => 'create-warehouses', 'edit' => 'edit-warehouses', 'delete' => 'delete-warehouses'],
    'stock-movements' => ['create' => 'create-stock-movements'],
    'suppliers' => ['create' => 'create-suppliers', 'delete' => 'delete-suppliers'],
    'inventory-transfers' => ['create' => 'create-acc-transfers', 'delete' => 'delete-acc-transfers'],
    'attendance' => ['create' => 'create-attendance', 'delete' => 'delete-attendance'],
    'leaves' => ['create' => 'create-leaves', 'delete' => 'delete-leaves', 'approve' => 'approve-leaves'],
    'users' => ['create' => 'create-users', 'edit' => 'edit-users', 'delete' => 'delete-users'],
    'roles' => ['create' => 'create-roles', 'edit' => 'edit-roles', 'delete' => 'delete-roles'],
    'bills' => ['create' => 'create-bills', 'delete' => 'delete-bills'],
    'bank-accounts' => ['create' => 'create-bank-accounts', 'delete' => 'delete-bank-accounts'],
    'transfers' => ['create' => 'create-acc-transfers', 'delete' => 'delete-acc-transfers'],
    'payroll' => ['create' => 'create-payroll', 'delete' => 'delete-payroll'],
    'pos' => ['create' => 'create-pos', 'delete' => 'delete-pos'],
    'assets' => ['create' => 'create-assets', 'delete' => 'delete-assets'],
    'bugs' => ['create' => 'create-bugs', 'delete' => 'delete-bugs'],
    'projects' => ['create' => 'create-projects', 'edit' => 'edit-projects', 'delete' => 'delete-projects'],
    'job-cards' => ['create' => 'create-job-cards', 'edit' => 'edit-job-cards', 'delete' => 'delete-job-cards'],
    'timesheets' => ['create' => 'create-timesheets', 'delete' => 'delete-timesheets'],
    'policies' => ['create' => 'create-policies', 'delete' => 'delete-policies'],
    'performance' => ['create' => 'create-performance', 'delete' => 'delete-performance'],
    'training' => ['create' => 'create-training', 'delete' => 'delete-training'],
    'recruitment' => ['create' => 'create-recruitment', 'delete' => 'delete-recruitment'],
    'settings' => ['edit' => 'edit-settings'],

    // New role modules from roles-sidebar-menu.md
    'companies' => ['create' => 'view-companies', 'edit' => 'view-companies', 'delete' => 'view-companies'],
    'approvals' => ['create' => 'view-approvals', 'edit' => 'approve-final', 'delete' => 'view-approvals'],
    'tenders' => ['create' => 'create-tenders', 'edit' => 'edit-tenders', 'delete' => 'delete-tenders'],
    'journal-entries' => ['create' => 'create-journal-entries', 'edit' => 'create-journal-entries', 'delete' => 'create-journal-entries'],
    'bank-reconciliation' => ['create' => 'create-bank-reconciliation', 'delete' => 'create-bank-reconciliation'],
    'tax-management' => ['create' => 'create-tax-management', 'delete' => 'create-tax-management'],
    'cost-centres' => ['create' => 'view-cost-centres', 'delete' => 'view-cost-centres'],
    'receivables-aging' => ['create' => 'view-sales-invoices', 'delete' => 'view-sales-invoices'],
    'payables-aging' => ['create' => 'view-purchase-invoices', 'delete' => 'view-purchase-invoices'],
    'credit-limits' => ['create' => 'edit-credit-limits', 'edit' => 'edit-credit-limits', 'delete' => 'edit-credit-limits'],
    'salary-records' => ['create' => 'view-employees', 'delete' => 'view-employees'],
    'deductions' => ['create' => 'create-payroll', 'delete' => 'create-payroll'],
    'payslips' => ['create' => 'create-payslips', 'delete' => 'create-payslips'],
    'budget-vs-actual' => ['create' => 'view-budgets', 'delete' => 'view-budgets'],
    'budgets' => ['create' => 'create-budgets', 'edit' => 'edit-budgets', 'delete' => 'edit-budgets'],
    'rfqs' => ['create' => 'create-rfqs', 'edit' => 'edit-rfqs', 'delete' => 'delete-rfqs'],
    'purchase-requisitions' => ['create' => 'create-purchase-requisitions', 'edit' => 'create-purchase-requisitions', 'delete' => 'create-purchase-requisitions'],
    'lpos' => ['create' => 'create-lpos', 'edit' => 'create-lpos', 'delete' => 'create-lpos'],
    'grns' => ['create' => 'create-grns', 'edit' => 'create-grns', 'delete' => 'create-grns'],
    'batch-tracking' => ['create' => 'view-products', 'edit' => 'edit-products', 'delete' => 'edit-products'],
    'barcodes' => ['create' => 'view-products', 'delete' => 'view-products'],
    'asset-assignment' => ['create' => 'create-assets', 'edit' => 'edit-assets', 'delete' => 'edit-assets'],
    'asset-maintenance' => ['create' => 'create-assets', 'delete' => 'create-assets'],
    'asset-disposal' => ['create' => 'create-assets', 'delete' => 'create-assets'],
    'quotations' => ['create' => 'create-quotations', 'edit' => 'create-quotations', 'delete' => 'create-quotations'],
    'sales-forecast' => ['create' => 'view-crm-deals', 'delete' => 'view-crm-deals'],
    'market-analysis' => ['create' => 'view-crm-leads', 'delete' => 'view-crm-leads'],
    'campaigns' => ['create' => 'create-campaigns', 'delete' => 'create-campaigns'],
    'lead-source-reports' => ['create' => 'view-crm-leads', 'delete' => 'view-crm-leads'],
    'project-profitability' => ['create' => 'view-projects', 'delete' => 'view-projects'],
    'resource-allocation' => ['create' => 'view-projects', 'edit' => 'edit-projects', 'delete' => 'view-projects'],
    'milestones' => ['create' => 'create-projects', 'edit' => 'edit-projects', 'delete' => 'view-projects'],
    'tasks' => ['create' => 'create-timesheets', 'edit' => 'create-timesheets', 'delete' => 'create-timesheets'],
    'site-reports' => ['create' => 'create-site-reports', 'delete' => 'create-site-reports'],
    'incidents' => ['create' => 'create-helpdesk-tickets', 'edit' => 'edit-helpdesk-tickets', 'delete' => 'create-helpdesk-tickets'],
    'team-tasks' => ['create' => 'view-team-timesheets', 'edit' => 'approve-team-timesheets', 'delete' => 'view-team-timesheets'],
    'team-attendance' => ['create' => 'create-attendance', 'edit' => 'view-team-attendance', 'delete' => 'view-team-attendance'],
    'team-timesheets' => ['create' => 'create-my-timesheets', 'edit' => 'approve-team-timesheets', 'delete' => 'create-my-timesheets'],
    'team-review' => ['create' => 'view-projects', 'delete' => 'view-projects'],
    'site-visits' => ['create' => 'create-helpdesk-tickets', 'edit' => 'edit-helpdesk-tickets', 'delete' => 'create-helpdesk-tickets'],
    'service-reports' => ['create' => 'create-helpdesk-tickets', 'delete' => 'create-helpdesk-tickets'],
    'escalations' => ['create' => 'create-helpdesk-tickets', 'edit' => 'edit-helpdesk-tickets', 'delete' => 'create-helpdesk-tickets'],
    'knowledge-base' => ['create' => 'create-helpdesk-tickets', 'delete' => 'create-helpdesk-tickets'],
    'call-statistics' => ['create' => 'view-call-logs', 'delete' => 'view-call-logs'],
    'shift-schedule' => ['create' => 'view-attendance', 'delete' => 'view-attendance'],
    'sla-reports' => ['create' => 'view-helpdesk-tickets', 'delete' => 'view-helpdesk-tickets'],
    'disciplinary' => ['create' => 'approve-hr', 'delete' => 'approve-hr'],
    'job-postings' => ['create' => 'create-recruitment', 'edit' => 'create-recruitment', 'delete' => 'create-recruitment'],
    'applications' => ['create' => 'view-recruitment', 'delete' => 'view-recruitment'],
    'onboarding' => ['create' => 'create-recruitment', 'delete' => 'create-recruitment'],
    'training-records' => ['create' => 'create-training', 'delete' => 'create-training'],
    'certifications' => ['create' => 'create-certifications', 'delete' => 'create-certifications'],
    'overtime' => ['create' => 'approve-overtime', 'edit' => 'approve-overtime', 'delete' => 'approve-overtime'],
    'operations-log' => ['create' => 'view-helpdesk-tickets', 'delete' => 'view-helpdesk-tickets'],
    'operations-tasks' => ['create' => 'view-helpdesk-tickets', 'delete' => 'view-helpdesk-tickets'],
    'vehicles' => ['create' => 'create-vehicles', 'edit' => 'edit-vehicles', 'delete' => 'delete-vehicles'],
    'driver-assignment' => ['create' => 'create-vehicles', 'edit' => 'edit-vehicles', 'delete' => 'create-vehicles'],
    'fuel-logs' => ['create' => 'create-fuel-logs', 'delete' => 'create-fuel-logs'],
    'trip-schedule' => ['create' => 'create-vehicles', 'delete' => 'create-vehicles'],
    'deliveries' => ['create' => 'create-deliveries', 'delete' => 'create-deliveries'],
    'shipments' => ['create' => 'create-shipments', 'delete' => 'create-shipments'],
    'route-planning' => ['create' => 'create-deliveries', 'delete' => 'create-deliveries'],
    'team-overview' => ['create' => 'view-team-overview', 'edit' => 'approve-team-leaves', 'delete' => 'view-team-overview'],
    'team-leaves' => ['create' => 'apply-leave', 'edit' => 'approve-team-leaves', 'delete' => 'apply-leave'],
    'announcements' => ['create' => 'view-announcements', 'delete' => 'view-announcements'],
    'my-account' => ['edit' => 'view-self-service'],
    'documents' => ['create' => 'create-documents', 'edit' => 'edit-documents', 'delete' => 'delete-documents'],
];
$routeMap = [
    'employees' => ['create' => 'admin.employees.index', 'edit' => 'admin.employees.edit', 'delete' => 'admin.employees.destroy'],
    'sales-invoices' => ['create' => 'admin.sales-invoices.create', 'edit' => 'admin.sales-invoices.edit', 'delete' => 'admin.sales-invoices.destroy'],
    'purchase-invoices' => ['create' => 'admin.purchase-invoices.create', 'edit' => 'admin.purchase-invoices.edit', 'delete' => 'admin.purchase-invoices.destroy'],
    'expenses' => ['create' => 'admin.expenses.index', 'delete' => 'admin.expenses.destroy'],
    'revenues' => ['create' => 'admin.revenues.index', 'delete' => 'admin.revenues.destroy'],
    'tickets' => ['create' => 'admin.helpdesk-tickets.index', 'delete' => 'admin.helpdesk-tickets.index'],
    'leads' => ['create' => 'admin.crm-leads.index', 'delete' => 'admin.crm-leads.destroy'],
    'contacts' => ['create' => 'admin.crm-contacts.index', 'delete' => 'admin.crm-contacts.destroy'],
    'deals' => ['create' => 'admin.crm-deals.index', 'delete' => 'admin.crm-deals.destroy'],
    'contracts' => ['create' => 'admin.crm-contracts.index', 'delete' => 'admin.crm-contracts.destroy'],
    'products' => ['create' => 'admin.products.index', 'delete' => 'admin.products.destroy'],
    'warehouses' => ['create' => 'admin.warehouses.index', 'edit' => 'admin.warehouses.edit', 'delete' => 'admin.warehouses.destroy'],
    'stock-movements' => ['create' => 'admin.stock-movements.index'],
    'suppliers' => ['create' => 'admin.suppliers.index', 'delete' => 'admin.suppliers.destroy'],
    'inventory-transfers' => ['create' => 'admin.acc-transfers.index', 'delete' => 'admin.acc-transfers.destroy'],
    'attendance' => ['create' => 'admin.attendance.index', 'delete' => 'admin.attendance.destroy'],
    'leaves' => ['create' => 'admin.leaves.index', 'approve' => 'admin.leaves.approve', 'delete' => 'admin.leaves.destroy'],
    'users' => ['create' => 'admin.users.create', 'edit' => 'admin.users.edit', 'delete' => 'admin.users.destroy'],
    'roles' => ['create' => 'admin.roles.create', 'edit' => 'admin.roles.edit', 'delete' => 'admin.roles.destroy'],
    'bills' => ['create' => 'admin.bills.index', 'delete' => 'admin.bills.destroy'],
    'bank-accounts' => ['create' => 'admin.bank-accounts.index', 'delete' => 'admin.bank-accounts.destroy'],
    'transfers' => ['create' => 'admin.acc-transfers.index', 'delete' => 'admin.acc-transfers.destroy'],
    'payroll' => ['create' => 'admin.payroll.generate-form', 'delete' => 'admin.payroll.destroy'],
    'pos' => ['create' => 'admin.pos.index', 'delete' => 'admin.pos.destroy'],
    'assets' => ['create' => 'admin.assets.index', 'delete' => 'admin.assets.destroy'],
    'bugs' => ['create' => 'admin.bugs.index', 'delete' => 'admin.bugs.destroy'],
    'projects' => ['create' => 'admin.projects.index', 'delete' => 'admin.projects.destroy'],
    'job-cards' => ['create' => 'admin.job-cards.index', 'edit' => 'admin.job-cards.show', 'delete' => 'admin.job-cards.destroy'],
    'timesheets' => ['create' => 'admin.timesheets.index', 'delete' => 'admin.timesheets.destroy'],
    'policies' => ['create' => 'admin.policies.index', 'delete' => 'admin.policies.destroy'],
    'performance' => ['create' => 'admin.performance.index', 'delete' => 'admin.performance.destroy'],
    'training' => ['create' => 'admin.training.index', 'delete' => 'admin.training.destroy'],
    'recruitment' => ['create' => 'admin.job-postings.index', 'delete' => 'admin.job-postings.destroy'],
    'settings' => ['edit' => 'admin.settings'],

    // New role module routes (mapped to closest existing admin routes)
    'companies' => ['create' => 'admin.companies.index', 'edit' => 'admin.companies.index', 'delete' => 'admin.companies.index'],
    'approvals' => ['create' => 'admin.approvals.index', 'edit' => 'admin.approvals.index', 'delete' => 'admin.approvals.index'],
    'tenders' => ['create' => 'admin.tenders.index', 'edit' => 'admin.tenders.edit', 'delete' => 'admin.tenders.destroy'],
    'journal-entries' => ['create' => 'admin.journal-entries.index', 'edit' => 'admin.journal-entries.index', 'delete' => 'admin.journal-entries.index'],
    'bank-reconciliation' => ['create' => 'admin.bank-accounts.index', 'delete' => 'admin.bank-accounts.index'],
    'tax-management' => ['create' => 'admin.expenses.index', 'delete' => 'admin.expenses.index'],
    'cost-centres' => ['create' => 'admin.expenses.index', 'delete' => 'admin.expenses.index'],
    'receivables-aging' => ['create' => 'admin.sales-invoices.index', 'delete' => 'admin.sales-invoices.index'],
    'payables-aging' => ['create' => 'admin.purchase-invoices.index', 'delete' => 'admin.purchase-invoices.index'],
    'credit-limits' => ['create' => 'admin.sales-invoices.index', 'edit' => 'admin.sales-invoices.index', 'delete' => 'admin.sales-invoices.index'],
    'salary-records' => ['create' => 'admin.payroll.generate-form', 'delete' => 'admin.payroll.generate-form'],
    'deductions' => ['create' => 'admin.payroll.generate-form', 'delete' => 'admin.payroll.generate-form'],
    'payslips' => ['create' => 'admin.payroll.generate-form', 'delete' => 'admin.payroll.generate-form'],
    'budget-vs-actual' => ['create' => 'admin.expenses.index', 'delete' => 'admin.expenses.index'],
    'budgets' => ['create' => 'admin.expenses.index', 'edit' => 'admin.expenses.index', 'delete' => 'admin.expenses.index'],
    'rfqs' => ['create' => 'admin.suppliers.index', 'edit' => 'admin.suppliers.index', 'delete' => 'admin.suppliers.index'],
    'purchase-requisitions' => ['create' => 'admin.purchase-invoices.index', 'edit' => 'admin.purchase-invoices.index', 'delete' => 'admin.purchase-invoices.index'],
    'lpos' => ['create' => 'admin.purchase-invoices.index', 'edit' => 'admin.purchase-invoices.index', 'delete' => 'admin.purchase-invoices.index'],
    'grns' => ['create' => 'admin.stock-movements.index', 'edit' => 'admin.stock-movements.index', 'delete' => 'admin.stock-movements.index'],
    'batch-tracking' => ['create' => 'admin.products.index', 'edit' => 'admin.products.index', 'delete' => 'admin.products.index'],
    'barcodes' => ['create' => 'admin.products.index', 'delete' => 'admin.products.index'],
    'asset-assignment' => ['create' => 'admin.assets.index', 'edit' => 'admin.assets.index', 'delete' => 'admin.assets.index'],
    'asset-maintenance' => ['create' => 'admin.assets.index', 'delete' => 'admin.assets.index'],
    'asset-disposal' => ['create' => 'admin.assets.index', 'delete' => 'admin.assets.index'],
    'quotations' => ['create' => 'admin.crm-deals.index', 'edit' => 'admin.crm-deals.index', 'delete' => 'admin.crm-deals.index'],
    'sales-forecast' => ['create' => 'admin.crm-deals.index', 'delete' => 'admin.crm-deals.index'],
    'market-analysis' => ['create' => 'admin.crm-leads.index', 'delete' => 'admin.crm-leads.index'],
    'campaigns' => ['create' => 'admin.crm-leads.index', 'delete' => 'admin.crm-leads.index'],
    'lead-source-reports' => ['create' => 'admin.crm-leads.index', 'delete' => 'admin.crm-leads.index'],
    'project-profitability' => ['create' => 'admin.projects.index', 'delete' => 'admin.projects.index'],
    'resource-allocation' => ['create' => 'admin.projects.index', 'edit' => 'admin.projects.index', 'delete' => 'admin.projects.index'],
    'milestones' => ['create' => 'admin.projects.index', 'edit' => 'admin.projects.index', 'delete' => 'admin.projects.index'],
    'tasks' => ['create' => 'admin.timesheets.index', 'edit' => 'admin.timesheets.index', 'delete' => 'admin.timesheets.index'],
    'site-reports' => ['create' => 'admin.projects.index', 'delete' => 'admin.projects.index'],
    'incidents' => ['create' => 'admin.helpdesk-tickets.index', 'edit' => 'admin.helpdesk-tickets.index', 'delete' => 'admin.helpdesk-tickets.index'],
    'team-tasks' => ['create' => 'admin.timesheets.index', 'edit' => 'admin.timesheets.index', 'delete' => 'admin.timesheets.index'],
    'team-attendance' => ['create' => 'admin.attendance.index', 'edit' => 'admin.attendance.index', 'delete' => 'admin.attendance.index'],
    'team-timesheets' => ['create' => 'admin.timesheets.index', 'edit' => 'admin.timesheets.index', 'delete' => 'admin.timesheets.index'],
    'team-review' => ['create' => 'admin.projects.index', 'delete' => 'admin.projects.index'],
    'site-visits' => ['create' => 'admin.helpdesk-tickets.index', 'edit' => 'admin.helpdesk-tickets.index', 'delete' => 'admin.helpdesk-tickets.index'],
    'service-reports' => ['create' => 'admin.helpdesk-tickets.index', 'delete' => 'admin.helpdesk-tickets.index'],
    'escalations' => ['create' => 'admin.helpdesk-tickets.index', 'edit' => 'admin.helpdesk-tickets.index', 'delete' => 'admin.helpdesk-tickets.index'],
    'knowledge-base' => ['create' => 'admin.helpdesk-tickets.index', 'delete' => 'admin.helpdesk-tickets.index'],
    'call-statistics' => ['create' => 'admin.calls.index', 'delete' => 'admin.calls.index'],
    'shift-schedule' => ['create' => 'admin.attendance.index', 'delete' => 'admin.attendance.index'],
    'sla-reports' => ['create' => 'admin.helpdesk-tickets.index', 'delete' => 'admin.helpdesk-tickets.index'],
    'disciplinary' => ['create' => 'admin.leaves.index', 'delete' => 'admin.leaves.index'],
    'job-postings' => ['create' => 'admin.job-postings.index', 'edit' => 'admin.job-postings.index', 'delete' => 'admin.job-postings.index'],
    'applications' => ['create' => 'admin.applications.index', 'delete' => 'admin.applications.index'],
    'onboarding' => ['create' => 'admin.job-postings.index', 'delete' => 'admin.job-postings.index'],
    'training-records' => ['create' => 'admin.training.index', 'delete' => 'admin.training.index'],
    'certifications' => ['create' => 'admin.training.index', 'delete' => 'admin.training.index'],
    'overtime' => ['create' => 'admin.attendance.index', 'edit' => 'admin.attendance.index', 'delete' => 'admin.attendance.index'],
    'operations-log' => ['create' => 'admin.helpdesk-tickets.index', 'delete' => 'admin.helpdesk-tickets.index'],
    'operations-tasks' => ['create' => 'admin.helpdesk-tickets.index', 'delete' => 'admin.helpdesk-tickets.index'],
    'vehicles' => ['create' => 'admin.vehicles.index', 'edit' => 'admin.vehicles.edit', 'delete' => 'admin.vehicles.destroy'],
    'driver-assignment' => ['create' => 'admin.vehicles.index', 'edit' => 'admin.vehicles.index', 'delete' => 'admin.vehicles.index'],
    'fuel-logs' => ['create' => 'admin.fuel-logs.index', 'delete' => 'admin.fuel-logs.index'],
    'trip-schedule' => ['create' => 'admin.vehicles.index', 'delete' => 'admin.vehicles.index'],
    'deliveries' => ['create' => 'admin.stock-movements.index', 'delete' => 'admin.stock-movements.index'],
    'shipments' => ['create' => 'admin.stock-movements.index', 'delete' => 'admin.stock-movements.index'],
    'route-planning' => ['create' => 'admin.stock-movements.index', 'delete' => 'admin.stock-movements.index'],
    'team-overview' => ['create' => 'admin.employees.index', 'edit' => 'admin.leaves.index', 'delete' => 'admin.employees.index'],
    'team-leaves' => ['create' => 'admin.leaves.index', 'edit' => 'admin.leaves.index', 'delete' => 'admin.leaves.index'],
    'announcements' => ['create' => 'admin.announcements.index', 'delete' => 'admin.announcements.index'],
    'my-account' => ['edit' => 'admin.profile'],
    'documents' => ['create' => 'admin.documents.create', 'edit' => 'admin.documents.edit', 'delete' => 'admin.documents.destroy'],
];
$canCreate = isset($permMap[$module]['create']) && auth()->user()->hasPermission($permMap[$module]['create']);
$canEdit = isset($permMap[$module]['edit']) && auth()->user()->hasPermission($permMap[$module]['edit']);
$canDelete = isset($permMap[$module]['delete']) && auth()->user()->hasPermission($permMap[$module]['delete']);
$canApprove = isset($permMap[$module]['approve']) && auth()->user()->hasPermission($permMap[$module]['approve']);
$hasActions = $canEdit || $canDelete || $canApprove;
@endphp

<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">{{ ucfirst(str_replace('-', ' ', $module)) }}</h2>
            <p class="text-emerald-100 text-sm mt-1">{{ $roleLabel }} - {{ ucfirst(str_replace('-', ' ', $module)) }}</p>
        </div>
        <div class="text-right">
            <p class="text-emerald-100 text-xs">{{ now()->format('l, d M Y') }}</p>
        </div>
    </div>
</div>
        @if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 text-sm">
    {{ session('success') }}
</div>
        @endif

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-900">{{ ucfirst(str_replace('-', ' ', $module)) }} List</h3>
        @if($canCreate && isset($routeMap[$module]['create']) && \Illuminate\Support\Facades\Route::has($routeMap[$module]['create']))
        <a href="{{ route($routeMap[$module]['create']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Create New
        </a>
        @endif
    </div>

    @switch($module)
    @case('reports')
        <div class="p-5">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-emerald-600">Total Sales</span>
                    <p class="text-xl font-bold text-emerald-900 mt-1">{{ $money($totalSales ?? 0) }}</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-amber-600">Total Expenses</span>
                    <p class="text-xl font-bold text-amber-900 mt-1">{{ $money($totalExpenses ?? 0) }}</p>
                </div>
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-sky-600">Total Revenues</span>
                    <p class="text-xl font-bold text-sky-900 mt-1">{{ $money($totalRevenues ?? 0) }}</p>
                </div>
                <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-violet-600">Total Purchases</span>
                    <p class="text-xl font-bold text-violet-900 mt-1">{{ $money($totalPurchases ?? 0) }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b"><h4 class="text-xs font-bold text-gray-700">Recent Sales</h4></div>
                    <div class="divide-y divide-gray-100">
        @foreach(($recentSales ?? collect())->take(5) as $inv)
                        <div class="px-4 py-2.5 flex justify-between text-xs"><span class="text-gray-700">{{ $inv->invoice_number }}</span><span class="font-semibold text-gray-900">TZS {{ number_format($inv->total_amount) }}</span></div>
        @endforeach
        </div>
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b"><h4 class="text-xs font-bold text-gray-700">Recent Expenses</h4></div>
                    <div class="divide-y divide-gray-100">
        @foreach(($recentExpenses ?? collect())->take(5) as $exp)
                        <div class="px-4 py-2.5 flex justify-between text-xs"><span class="text-gray-700">{{ $exp->description ?? $exp->category ?? 'Expense' }}</span><span class="font-semibold text-red-600">TZS {{ number_format($exp->amount) }}</span></div>
        @endforeach
        </div>
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b"><h4 class="text-xs font-bold text-gray-700">Recent Revenues</h4></div>
                    <div class="divide-y divide-gray-100">
        @foreach(($recentRevenues ?? collect())->take(5) as $rev)
                        <div class="px-4 py-2.5 flex justify-between text-xs"><span class="text-gray-700">{{ $rev->description ?? $rev->category ?? 'Revenue' }}</span><span class="font-semibold text-emerald-600">TZS {{ number_format($rev->amount) }}</span></div>
        @endforeach
        </div>
                </div>
            </div>
        </div>
        @break

    @case('projects')
        {{-- Stats Row --}}
        <div class="p-5 grid grid-cols-2 lg:grid-cols-4 gap-3 border-b">
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Active Projects</span>
                <p class="text-xl font-bold text-emerald-900 mt-1">{{ $activeProjects ?? 0 }}</p>
            </div>
            <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-sky-600 uppercase tracking-wider">Completed</span>
                <p class="text-xl font-bold text-sky-900 mt-1">{{ $completedProjects ?? 0 }}</p>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Total Projects</span>
                <p class="text-xl font-bold text-amber-900 mt-1">{{ ($projects ?? collect())->total() ?? 0 }}</p>
            </div>
            <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-violet-600 uppercase tracking-wider">Assigned Employees</span>
                <p class="text-xl font-bold text-violet-900 mt-1">{{ $assignedEmployeesCount ?? 0 }}</p>
            </div>
        </div>
        @if($canCreate)
        <div class="px-5 py-3 border-b">
            <button onclick="document.getElementById('roleCreateProjectModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                New Project
            </button>
        </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Project</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Manager</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Budget</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Assigned Team</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Due Date</th>
                        <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($projects ?? collect())->items() ?? [] as $project)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-gray-900">{{ $project->title }}</div>
                            <div class="text-[10px] text-gray-400">{{ $project->project_number }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $project->manager?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs font-medium text-gray-700">{{ $money($project->budget ?? 0) }}</td>
                        <td class="px-4 py-3">
                            @if($project->employees && $project->employees->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($project->employees->take(3) as $emp)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700">{{ $emp->first_name }} {{ $emp->last_name }}</span>
                                    @endforeach
                                    @if($project->employees->count() > 3)
                                        <span class="text-[10px] text-gray-400">+{{ $project->employees->count() - 3 }} more</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-[10px] text-gray-400">No team assigned</span>
                            @endif
                        </td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($project->status === 'in_progress') ? 'bg-sky-50 text-sky-700' : (($project->status === 'completed') ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $project->due_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.projects.show', $project) }}" class="text-sky-600 hover:text-sky-700 p-1 rounded hover:bg-sky-50 transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $project) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this project?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($projects ?? null)?->links() ?? '' }}</div>
        {{-- Create Project Modal --}}
        @if($canCreate)
        <div id="roleCreateProjectModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Create New Project</h3>
                <form method="POST" action="{{ route('admin.projects.store') }}" class="space-y-4">@csrf
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
                    <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input name="start_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Manager</label><select name="manager_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Unassigned</option>
                    @foreach(($managers ?? collect()) as $m)
                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                    </select></div>
                    <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="planning">Planning</option><option value="in_progress">In Progress</option><option value="completed">Completed</option><option value="on_hold">On Hold</option><option value="cancelled">Cancelled</option></select></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Priority</label><select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></div></div>
                    <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Progress (%)</label><input name="progress" type="number" min="0" max="100" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Budget</label><input name="budget" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
                    {{-- Invoicing Type --}}
                    <div class="pt-3 border-t">
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Invoicing</h4>
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <label class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 border-gray-100 hover:border-emerald-200 cursor-pointer transition-all invoicing-option-Role" data-type="recurring">
                                <input type="radio" name="invoicing_type" value="recurring" class="sr-only" onchange="selectInvoicingTypeRole('recurring')">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span class="text-xs font-medium text-gray-600">Recurring</span>
                                <span class="text-[10px] text-gray-400">Monthly auto</span>
                            </label>
                            <label class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 border-gray-100 hover:border-emerald-200 cursor-pointer transition-all invoicing-option-Role" data-type="one_time">
                                <input type="radio" name="invoicing_type" value="one_time" class="sr-only" onchange="selectInvoicingTypeRole('one_time')">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="text-xs font-medium text-gray-600">One-Time</span>
                                <span class="text-[10px] text-gray-400">Single invoice</span>
                            </label>
                            <label class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 border-gray-100 hover:border-emerald-200 cursor-pointer transition-all invoicing-option-Role" data-type="none">
                                <input type="radio" name="invoicing_type" value="none" class="sr-only" onchange="selectInvoicingTypeRole('none')" checked>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                <span class="text-xs font-medium text-gray-600">None</span>
                                <span class="text-[10px] text-gray-400">Manual only</span>
                            </label>
                        </div>
                        <div id="recurringFieldsRole" class="hidden grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Monthly Amount (TZS)</label><input name="billing_amount" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Billing Day (1-28)</label><input name="billing_day" type="number" min="1" max="28" value="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                            <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Invoicing End Date (optional)</label><input name="invoicing_end_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                        </div>
                        <div id="oneTimeFieldsRole" class="hidden grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Invoice Amount (TZS)</label><input name="one_time_amount" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Generate After</label><select name="one_time_when" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="immediately">Immediately (on create)</option><option value="manual">Manual (from project page)</option><option value="completion">When project completes</option></select></div>
                        </div>
                    </div>
                    {{-- Staff Assignment --}}
                    <div class="pt-3 border-t">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-gray-900">Assign Staff to Project</h4>
                            <span class="text-[10px] text-gray-400">Select employees & their roles</span>
                        </div>
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    @foreach(($employees ?? collect()) as $emp)
                            <div class="flex items-center gap-3 p-2.5 rounded-lg border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50/30 transition-all">
                                <input type="checkbox" name="project_employee_ids[]" value="{{ $emp->id }}" id="role-emp-{{ $emp->id }}" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 flex-shrink-0">
                                <label for="role-emp-{{ $emp->id }}" class="flex items-center gap-3 flex-1 cursor-pointer">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">{{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name ?? '', 0, 1)) }}</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $emp->full_name ?? ($emp->first_name . ' ' . $emp->last_name) }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $emp->department ?? 'N/A' }} · {{ $emp->designation ?? 'N/A' }}</p>
                                    </div>
                                </label>
                                <input type="text" name="project_employee_roles[{{ $emp->id }}]" placeholder="Role" class="w-32 px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 outline-none flex-shrink-0" value="">
                            </div>
                    @endforeach
                    @if(($employees ?? collect())->isEmpty())
                            <p class="text-xs text-gray-400 text-center py-4">No active employees.</p>
                    @endif
                        </div>
                    </div>
                    <div class="flex gap-2 pt-3"><button type="button" onclick="document.getElementById('roleCreateProjectModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Project</button></div>
                </form>
            </div>
        </div>
        <script>
        function selectInvoicingTypeRole(type) {
            document.querySelectorAll('.invoicing-option-Role').forEach(el => {
                if (el.dataset.type === type) {
                    el.classList.add('border-emerald-500', 'bg-emerald-50');
                    el.querySelector('svg').classList.add('text-emerald-600');
                    el.querySelector('svg').classList.remove('text-gray-400');
                } else {
                    el.classList.remove('border-emerald-500', 'bg-emerald-50');
                    el.querySelector('svg').classList.remove('text-emerald-600');
                    el.querySelector('svg').classList.add('text-gray-400');
                }
            });
            document.getElementById('recurringFieldsRole').classList.toggle('hidden', type !== 'recurring');
            document.getElementById('oneTimeFieldsRole').classList.toggle('hidden', type !== 'one_time');
        }
        </script>
        @endif
        @break

    @case('bugs')
        {{-- Stats Row --}}
        <div class="p-5 grid grid-cols-2 lg:grid-cols-4 gap-3 border-b">
            <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider">Open Bugs</span>
                <p class="text-xl font-bold text-rose-900 mt-1">{{ $openBugs ?? 0 }}</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Resolved</span>
                <p class="text-xl font-bold text-emerald-900 mt-1">{{ $resolvedBugs ?? 0 }}</p>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Total Bugs</span>
                <p class="text-xl font-bold text-amber-900 mt-1">{{ ($bugs ?? collect())->total() ?? 0 }}</p>
            </div>
            <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-violet-600 uppercase tracking-wider">Critical</span>
                <p class="text-xl font-bold text-violet-900 mt-1">{{ $criticalBugs ?? 0 }}</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Bug</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Project</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Severity</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Assigned To</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($bugs ?? collect())->items() ?? [] as $bug)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $bug->title }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $bug->project?->title ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php $sevColors = ['critical' => 'bg-rose-50 text-rose-700', 'high' => 'bg-orange-50 text-orange-700', 'medium' => 'bg-amber-50 text-amber-700', 'low' => 'bg-sky-50 text-sky-700']; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $sevColors[$bug->severity] ?? 'bg-gray-50 text-gray-700' }}">{{ ucfirst($bug->severity) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php $statusColors = ['open' => 'bg-rose-50 text-rose-700', 'in_progress' => 'bg-sky-50 text-sky-700', 'resolved' => 'bg-emerald-50 text-emerald-700', 'closed' => 'bg-gray-100 text-gray-600']; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $statusColors[$bug->status] ?? 'bg-gray-50 text-gray-700' }}">{{ ucfirst(str_replace('_', ' ', $bug->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $bug->assignedTo?->name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $bug->created_at?->format('d M Y') ?? '-' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $bug) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this bug?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($bugs ?? null)?->links() ?? '' }}</div>
        @break

    @case('job-cards')
        {{-- Stats Row --}}
        <div class="p-5 grid grid-cols-2 lg:grid-cols-4 gap-3 border-b">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Total</span>
                <p class="text-xl font-bold text-amber-900 mt-1">{{ $totalCards ?? 0 }}</p>
            </div>
            <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider">Open</span>
                <p class="text-xl font-bold text-rose-900 mt-1">{{ $openCards ?? 0 }}</p>
            </div>
            <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-sky-600 uppercase tracking-wider">In Progress</span>
                <p class="text-xl font-bold text-sky-900 mt-1">{{ $inProgressCards ?? 0 }}</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Resolved</span>
                <p class="text-xl font-bold text-emerald-900 mt-1">{{ $resolvedCards ?? 0 }}</p>
            </div>
        </div>
        @if($canCreate)
        <div class="px-5 py-3 border-b">
            <button onclick="document.getElementById('roleCreateJobCardModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                New Job Card
            </button>
        </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Job #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Project</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Assigned To</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Priority</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Due Date</th>
                        <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($jobCards ?? collect())->items() ?? [] as $jc)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-mono text-gray-700">{{ $jc->job_number }}</td>
                        <td class="px-4 py-3 text-xs font-medium text-gray-900 max-w-[200px]">{{ \Illuminate\Support\Str::limit($jc->title, 40) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $jc->project?->title ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $jc->assignedTo?->name ?? '—' }}</td>
                        <td class="px-4 py-3">@php $pc = ['low'=>'gray','medium'=>'amber','high'=>'rose','critical'=>'red']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $pc[$jc->priority] ?? 'gray' }}-50 text-{{ $pc[$jc->priority] ?? 'gray' }}-700">{{ ucfirst($jc->priority) }}</span></td>
                        <td class="px-4 py-3">@php $sc = ['open'=>'amber','in_progress'=>'sky','resolved'=>'emerald','closed'=>'gray']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $sc[$jc->status] ?? 'gray' }}-50 text-{{ $sc[$jc->status] ?? 'gray' }}-700">{{ str_replace('_', ' ', ucfirst($jc->status)) }}</span></td>
                        <td class="px-4 py-3 text-xs {{ $jc->due_date && $jc->due_date->isPast() && $jc->status !== 'resolved' ? 'text-red-500 font-medium' : 'text-gray-400' }}">{{ $jc->due_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.job-cards.show', $jc) }}" class="text-indigo-600 hover:text-indigo-700 p-1 rounded hover:bg-indigo-50 transition-colors" title="View / Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.job-cards.print', $jc) }}" target="_blank" class="text-gray-500 hover:text-gray-700 p-1 rounded hover:bg-gray-50 transition-colors" title="Print">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                </a>
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $jc) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this job card?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($jobCards ?? null)?->links() ?? '' }}</div>
        {{-- Create Job Card Modal --}}
        @if($canCreate)
        <div id="roleCreateJobCardModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-start justify-center p-4 overflow-y-auto" onclick="if(event.target===this)this.classList.add('hidden')">
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full my-8 p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">New Job Card / Service Call Report</h3>
                <form method="POST" action="{{ route('admin.job-cards.store') }}" class="space-y-4">@csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">CSR No</label><input name="csr_no" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Report Date</label><input name="report_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer Name</label><input name="customer_name" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer Address</label><input name="customer_address" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Branch Name</label><input name="branch_name" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Department</label><input name="department" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Equipment Type</label><input name="equipment_type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Make / Brand</label><input name="make_brand" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Model</label><input name="model" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Serial Number</label><input name="serial_number" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Call Type</label><select name="call_type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="">Select</option><option value="corrective">Corrective Maintenance</option><option value="corrective_preventive">Corrective &amp; Preventive Maintenance</option><option value="preventive">Preventive Maintenance</option><option value="installation">Installation</option></select></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label><select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="">None</option>@foreach(($projects ?? collect()) as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach</select></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Assign To</label><select name="assigned_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="">None</option>@foreach(($technicians ?? collect()) as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Priority *</label><select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                        <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></textarea></div>
                        <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Problem Reported</label><textarea name="problem_reported" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></textarea></div>
                        <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Defects Found</label><textarea name="defects_found" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></textarea></div>
                        <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Action Taken</label><textarea name="action_taken" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></textarea></div>
                        <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></textarea></div>
                    </div>
                    <div class="border rounded-lg p-4 bg-gray-50/50">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Parts Required / Replaced</label>
                        <div id="jcPartsContainer" class="space-y-2">
                            <div class="grid grid-cols-12 gap-2 jc-part-row">
                                <div class="col-span-4"><input name="parts[0][part_name]" placeholder="Part name" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                                <div class="col-span-2"><input name="parts[0][quantity]" type="number" step="0.01" value="1" placeholder="Qty" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                                <div class="col-span-3"><input name="parts[0][model]" placeholder="Model" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                                <div class="col-span-3 flex gap-1"><input name="parts[0][part_number]" placeholder="Part number" class="flex-1 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"><button type="button" onclick="this.closest('.jc-part-row').remove()" class="text-rose-500 hover:text-rose-700 px-1">×</button></div>
                            </div>
                        </div>
                        <button type="button" onclick="addJcPartRow()" class="mt-2 text-xs font-medium text-indigo-600 hover:text-indigo-800">+ Add part</button>
                    </div>
                    <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('roleCreateJobCardModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Create Job Card</button></div>
                </form>
            </div>
        </div>
        <script>
        let jcPartIndex = 1;
        function addJcPartRow() {
            const container = document.getElementById('jcPartsContainer');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-2 jc-part-row';
            row.innerHTML = `
                <div class="col-span-4"><input name="parts[${jcPartIndex}][part_name]" placeholder="Part name" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                <div class="col-span-2"><input name="parts[${jcPartIndex}][quantity]" type="number" step="0.01" value="1" placeholder="Qty" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                <div class="col-span-3"><input name="parts[${jcPartIndex}][model]" placeholder="Model" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                <div class="col-span-3 flex gap-1"><input name="parts[${jcPartIndex}][part_number]" placeholder="Part number" class="flex-1 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"><button type="button" onclick="this.closest('.jc-part-row').remove()" class="text-rose-500 hover:text-rose-700 px-1">×</button></div>
            `;
            container.appendChild(row);
            jcPartIndex++;
        }
        </script>
        @endif
        @break
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Position</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Department</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($employees ?? collect())->items() ?? [] as $emp)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $emp->first_name ?? '' }} {{ $emp->last_name ?? '' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $emp->designation ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $emp->department ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($emp->status ?? '') === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-600' }}">{{ ucfirst($emp->status ?? 'N/A') }}</span></td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canEdit && isset($routeMap[$module]['edit']))
                                <a href="{{ route($routeMap[$module]['edit'], $emp) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                @endif
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $emp) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this employee?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($employees ?? null)?->links() ?? '' }}</div>
        @break

    @case('sales-invoices')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Invoice #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($invoices ?? collect())->items() ?? [] as $inv)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $inv->invoice_number }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $inv->customer?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($inv->total_amount) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $inv->invoice_date->format('d M Y') }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canEdit && isset($routeMap[$module]['edit']))
                                <a href="{{ route($routeMap[$module]['edit'], $inv) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                @endif
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $inv) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this invoice?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($invoices ?? null)?->links() ?? '' }}</div>
        @break

    @case('purchase-invoices')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Invoice #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($invoices ?? collect())->items() ?? [] as $inv)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $inv->invoice_number }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $inv->vendor?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($inv->total_amount) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $inv->invoice_date->format('d M Y') }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canEdit && isset($routeMap[$module]['edit']))
                                <a href="{{ route($routeMap[$module]['edit'], $inv) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                @endif
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $inv) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this invoice?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($invoices ?? null)?->links() ?? '' }}</div>
        @break

    @case('expenses')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($expenses ?? collect())->items() ?? [] as $exp)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $exp->description ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $exp->category ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-red-600">TZS {{ number_format($exp->amount) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $exp->expense_date?->format('d M Y') ?? '-' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $exp) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this expense?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($expenses ?? null)?->links() ?? '' }}</div>
        @break

    @case('revenues')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($revenues ?? collect())->items() ?? [] as $rev)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $rev->description ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $rev->category ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-emerald-600">TZS {{ number_format($rev->amount) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $rev->revenue_date?->format('d M Y') ?? '-' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $rev) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this revenue?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($revenues ?? null)?->links() ?? '' }}</div>
        @break

    @case('tickets')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Subject</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Priority</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($tickets ?? collect())->items() ?? [] as $ticket)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $ticket->title ?? 'Ticket #' . $ticket->id }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($ticket->status === 'open') ? 'bg-rose-50 text-rose-700' : (($ticket->status === 'resolved') ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst(str_replace('_', ' ', $ticket->status ?? '')) }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ ucfirst($ticket->priority ?? '-') }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $ticket->created_at->format('d M Y') }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $ticket) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this ticket?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($tickets ?? null)?->links() ?? '' }}</div>
        @break

    @case('leads')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Company</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($leads ?? collect())->items() ?? [] as $lead)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $lead->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $lead->company_name ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700">{{ ucfirst($lead->status ?? 'New') }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $lead->created_at->format('d M Y') }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $lead) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this lead?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($leads ?? null)?->links() ?? '' }}</div>
        @break

    @case('contacts')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Phone</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($contacts ?? collect())->items() ?? [] as $contact)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $contact->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $contact->email ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $contact->phone ?? '-' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $contact) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this contact?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($contacts ?? null)?->links() ?? '' }}</div>
        @break

    @case('deals')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Deal</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Value</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($deals ?? collect())->items() ?? [] as $deal)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $deal->title ?? 'Deal #' . $deal->id }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($deal->value ?? 0) }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700">{{ ucfirst($deal->status ?? 'Open') }}</span></td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $deal) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this deal?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($deals ?? null)?->links() ?? '' }}</div>
        @break

    @case('contracts')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Contract</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Value</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($contracts ?? collect())->items() ?? [] as $contract)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $contract->title ?? 'Contract #' . $contract->id }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($contract->value ?? 0) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ ucfirst($contract->status ?? '-') }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $contract) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this contract?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($contracts ?? null)?->links() ?? '' }}</div>
        @break

    @case('products')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">SKU</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Price</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Stock</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($products ?? collect())->items() ?? [] as $product)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $product->sku ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($product->price ?? 0) }}</td>
                        <td class="px-4 py-3"><span class="text-xs @if(($product->stock_quantity ?? 0)
        < 10) text-rose-600 font-bold @else text-gray-700 @endif">{{ $product->stock_quantity ?? 0 }}</span></td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $product) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this product?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($products ?? null)?->links() ?? '' }}</div>
        @break

    @case('warehouses')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Location</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($warehouses ?? collect())->items() ?? [] as $wh)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $wh->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $wh->location ?? '-' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canEdit && isset($routeMap[$module]['edit']))
                                <a href="{{ route($routeMap[$module]['edit'], $wh) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                @endif
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $wh) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this warehouse?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($warehouses ?? null)?->links() ?? '' }}</div>
        @break

    @case('stock-movements')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Quantity</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($movements ?? collect())->items() ?? [] as $m)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $m->product?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ ucfirst($m->type ?? '-') }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">{{ $m->quantity ?? 0 }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $m->created_at->format('d M Y') }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($movements ?? null)?->links() ?? '' }}</div>
        @break

    @case('suppliers')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Phone</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Email</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($suppliers ?? collect())->items() ?? [] as $s)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $s->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $s->phone ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $s->email ?? '-' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $s) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this supplier?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($suppliers ?? null)?->links() ?? '' }}</div>
        @break

    @case('inventory-transfers')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Transfer #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($transfers ?? collect())->items() ?? [] as $t)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">Transfer #{{ $t->id }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ ucfirst($t->status ?? '-') }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $t->created_at->format('d M Y') }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($transfers ?? null)?->links() ?? '' }}</div>
        @break

    @case('attendance')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($records ?? collect())->items() ?? [] as $r)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $r->employee?->first_name ?? '' }} {{ $r->employee?->last_name ?? '' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $r->date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($r->status === 'present') ? 'bg-emerald-50 text-emerald-700' : (($r->status === 'absent') ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($r->status) }}</span></td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $r) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this record?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($records ?? null)?->links() ?? '' }}</div>
        @break

    @case('leaves')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Dates</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($leaves ?? collect())->items() ?? [] as $l)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $l->employee?->first_name ?? '' }} {{ $l->employee?->last_name ?? '' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $l->leave_type ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($l->status === 'approved') ? 'bg-emerald-50 text-emerald-700' : (($l->status === 'rejected') ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($l->status ?? 'Pending') }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $l->start_date?->format('d M') ?? '' }} - {{ $l->end_date?->format('d M Y') ?? '' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canApprove && isset($routeMap[$module]['approve']) && ($l->status ?? '') !== 'approved')
                                <form action="{{ route($routeMap[$module]['approve'], $l) }}" method="POST" style="display:inline">@csrf @method('PATCH')<button type="submit" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Approve"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></button></form>
                                @endif
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $l) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this leave request?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($leaves ?? null)?->links() ?? '' }}</div>
        @break

    @case('users')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Joined</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($users ?? collect())->items() ?? [] as $u)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $u->roles()->first()?->label ?? $u->role ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $u->created_at->format('d M Y') }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canEdit && isset($routeMap[$module]['edit']))
                                <a href="{{ route($routeMap[$module]['edit'], $u) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                @endif
                                @if($canDelete && isset($routeMap[$module]['delete']) && $u->id !== auth()->id())
                                <form action="{{ route($routeMap[$module]['delete'], $u) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this user?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($users ?? null)?->links() ?? '' }}</div>
        @break

    @case('roles')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Permissions</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Users</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($roles ?? collect()) as $r)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $r->label ?? ucfirst(str_replace('_', ' ', $r->name)) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $r->permissions()->count() }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $r->users()->count() }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canEdit && isset($routeMap[$module]['edit']))
                                <a href="{{ route($routeMap[$module]['edit'], $r) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                @endif
                                @if($canDelete && isset($routeMap[$module]['delete']) && $r->editable)
                                <form action="{{ route($routeMap[$module]['delete'], $r) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this role?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        @break

    @case('bills')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Bill #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Due Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($bills ?? collect())->items() ?? [] as $bill)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $bill->bill_number ?? 'Bill #' . $bill->id }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $bill->vendor?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($bill->amount ?? 0) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $bill->due_date?->format('d M Y') ?? '-' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $bill) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this bill?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($bills ?? null)?->links() ?? '' }}</div>
        @break

    @case('bank-accounts')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Bank</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Account #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Balance</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($accounts ?? collect())->items() ?? [] as $acc)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $acc->bank_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $acc->account_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-emerald-600">TZS {{ number_format($acc->balance ?? 0) }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $acc) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this bank account?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($accounts ?? null)?->links() ?? '' }}</div>
        @break

    @case('transfers')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Transfer #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($transfers ?? collect())->items() ?? [] as $t)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">Transfer #{{ $t->id }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($t->amount ?? 0) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $t->created_at->format('d M Y') }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $t) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this transfer?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($transfers ?? null)?->links() ?? '' }}</div>
        @break

    @case('payroll')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Position</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Salary</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($employees ?? collect())->items() ?? [] as $emp)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $emp->first_name ?? '' }} {{ $emp->last_name ?? '' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $emp->designation ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($emp->salary ?? 0) }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3 text-right">
                            @if($canCreate && isset($routeMap[$module]['create']))
                            <a href="{{ route($routeMap[$module]['create']) }}" class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold text-emerald-600 hover:text-emerald-700 border border-emerald-200 rounded hover:bg-emerald-50 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Generate
                            </a>
                            @endif
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($employees ?? null)?->links() ?? '' }}</div>
        @break

    @case('salary')
    @case('payslips')
        <div class="p-5 space-y-5">
            @if(!$employee)
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                <svg class="w-10 h-10 text-red-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-sm text-red-700 font-medium">Employee record not linked to your account.</p>
                <p class="text-xs text-red-500 mt-1">Contact HR to link your employee profile.</p>
            </div>
            @else
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 border border-emerald-200 rounded-xl p-4">
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Basic Salary</span>
                    <p class="text-xl font-bold text-emerald-900 mt-1">{{ $money($salary ?? 0) }}</p>
                    <p class="text-[10px] text-emerald-500 mt-0.5">Monthly base pay</p>
                </div>
                <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200 rounded-xl p-4">
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Total Overtime</span>
                    <p class="text-xl font-bold text-amber-900 mt-1">{{ $money($totalOvertime ?? 0) }}</p>
                    <p class="text-[10px] text-amber-500 mt-0.5">All-time</p>
                </div>
                <div class="bg-gradient-to-br from-sky-50 to-sky-100/50 border border-sky-200 rounded-xl p-4">
                    <span class="text-[10px] font-bold text-sky-600 uppercase tracking-wider">Year-to-Date Net</span>
                    <p class="text-xl font-bold text-sky-900 mt-1">{{ $money($yearToDate ?? 0) }}</p>
                    <p class="text-[10px] text-sky-500 mt-0.5">{{ now()->year }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200 rounded-xl p-4">
                    <span class="text-[10px] font-bold text-purple-600 uppercase tracking-wider">Total Paid</span>
                    <p class="text-xl font-bold text-purple-900 mt-1">{{ $money($totalPaid ?? 0) }}</p>
                    <p class="text-[10px] text-purple-500 mt-0.5">All paid payslips</p>
                </div>
            </div>

            {{-- Latest Net Pay Banner --}}
            @if($latestPayroll)
            <div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-5 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-16 -mt-16"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-emerald-200 uppercase tracking-wider font-semibold">Latest Payslip</p>
                        <p class="text-lg font-bold mt-0.5">{{ $latestPayroll->month }} {{ $latestPayroll->year }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold">{{ $money($latestPayroll->net_salary) }}</p>
                        <p class="text-xs text-emerald-200">Net Pay</p>
                    </div>
                    <a href="{{ route('payslip.download', $latestPayroll->id) }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-lg transition-colors inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>
            @endif

            {{-- Filters --}}
            <form method="GET" action="{{ route('role.page', ['module' => $module]) }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wider">Month</label>
                    <select name="month" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                        <option value="">All Months</option>
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                        <option value="{{ $m }}" {{ ($filterMonth ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wider">Year</label>
                    <select name="year" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                        <option value="">All Years</option>
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ ($filterYear ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wider">Status</label>
                    <select name="status" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none bg-white">
                        <option value="">All Status</option>
                        <option value="paid" {{ ($filterStatus ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ ($filterStatus ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition-colors">Filter</button>
                @if($filterMonth || $filterYear || $filterStatus)
                <a href="{{ route('role.page', ['module' => $module]) }}" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-50 transition-colors">Clear</a>
                @endif
            </form>

            {{-- Payslips Table --}}
            <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                <div class="px-4 py-3 border-b bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">My Payslips</h3>
                    <span class="text-[10px] text-gray-500 font-medium">{{ $employee->first_name ?? '' }} {{ $employee->last_name ?? '' }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider">Period</th>
                                <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-wider">Basic</th>
                                <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-wider">Overtime</th>
                                <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-wider">Allowances</th>
                                <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-wider">Deductions</th>
                                <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-wider">Net Pay</th>
                                <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-600 uppercase tracking-wider">Malipo</th>
                                <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse(($payrolls ?? collect())->items() ?? [] as $payroll)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-xs font-semibold text-gray-900 whitespace-nowrap">{{ $payroll->month }} {{ $payroll->year }}</td>
                                <td class="px-4 py-3 text-xs text-right text-gray-600">{{ $money($payroll->basic_salary) }}</td>
                                <td class="px-4 py-3 text-xs text-right text-amber-600 font-medium">{{ $money($payroll->overtime ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-right text-emerald-600">{{ $money($payroll->allowances) }}</td>
                                <td class="px-4 py-3 text-xs text-right text-red-600">{{ $money($payroll->deductions) }}</td>
                                <td class="px-4 py-3 text-xs text-right font-bold text-gray-900">{{ $money($payroll->net_salary) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($payroll->status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Imelipwa
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Hajalipwa
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('payslip.preview', $payroll->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold text-sky-600 hover:text-sky-700 border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors mr-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ona
                                    </a>
                                    <a href="{{ route('payslip.download', $payroll->id) }}" class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold text-emerald-600 hover:text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-50 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        PDF
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm text-gray-400">No payslips found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t bg-gray-50/30">{{ ($payrolls ?? null)?->links() ?? '' }}</div>
            </div>
            @endif
        </div>
        @break

    @case('pos')
        <div class="p-5">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-emerald-600">Today's Sales</span>
                    <p class="text-xl font-bold text-emerald-900 mt-1">{{ $money($todaySales ?? 0) }}</p>
                </div>
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-sky-600">Transactions</span>
                    <p class="text-xl font-bold text-sky-900 mt-1">{{ $todayCount ?? 0 }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach(($products ?? collect())->take(12) as $product)
                <div class="border rounded-lg p-3 hover:shadow-md transition-shadow cursor-pointer">
                    <p class="text-xs font-medium text-gray-900">{{ $product->name }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">Stock: {{ $product->stock_quantity ?? 0 }}</p>
                    <p class="text-sm font-bold text-emerald-600 mt-1">TZS {{ number_format($product->price ?? 0) }}</p>
                </div>
        @endforeach
        </div>
        </div>
        @break

    @case('pos-reports')
        <div class="p-5">
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-emerald-600">Total Sales</span>
                    <p class="text-xl font-bold text-emerald-900 mt-1">{{ $money($totalSales ?? 0) }}</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-amber-600">This Month</span>
                    <p class="text-xl font-bold text-amber-900 mt-1">{{ $money($monthSales ?? 0) }}</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Sale #</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
        @foreach(($sales ?? collect())->items() ?? [] as $sale)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-xs font-medium text-gray-900">Sale #{{ $sale->id }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-emerald-600">TZS {{ number_format($sale->total_amount) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $sale->created_at->format('d M Y H:i') }}</td>
                        </tr>
        @endforeach
        </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t">{{ ($sales ?? null)?->links() ?? '' }}</div>
        </div>
        @break

    @case('sales-dashboard')
        <div class="p-5">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-emerald-600">Total Proposals</span>
                    <p class="text-xl font-bold text-emerald-900 mt-1">{{ $totalProposals ?? 0 }}</p>
                </div>
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-sky-600">Accepted</span>
                    <p class="text-xl font-bold text-sky-900 mt-1">{{ $acceptedProposals ?? 0 }}</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-amber-600">Total Invoices</span>
                    <p class="text-xl font-bold text-amber-900 mt-1">{{ $totalInvoices ?? 0 }}</p>
                </div>
                <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-violet-600">Total Sales</span>
                    <p class="text-xl font-bold text-violet-900 mt-1">{{ $money($totalSales ?? 0) }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b"><h4 class="text-xs font-bold text-gray-700">Recent Proposals</h4></div>
                    <div class="divide-y divide-gray-100">
        @foreach(($recentProposals ?? collect())->take(5) as $prop)
                        <div class="px-4 py-2.5 flex justify-between text-xs"><span class="text-gray-700">{{ $prop->title ?? 'Proposal #' . $prop->id }}</span><span class="text-gray-500">{{ ucfirst($prop->status ?? '') }}</span></div>
        @endforeach
        </div>
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b"><h4 class="text-xs font-bold text-gray-700">Recent Invoices</h4></div>
                    <div class="divide-y divide-gray-100">
        @foreach(($recentInvoices ?? collect())->take(5) as $inv)
                        <div class="px-4 py-2.5 flex justify-between text-xs"><span class="text-gray-700">{{ $inv->invoice_number }}</span><span class="font-semibold text-gray-900">TZS {{ number_format($inv->total_amount) }}</span></div>
        @endforeach
        </div>
                </div>
            </div>
        </div>
        @break

    @case('settings')
        <div class="p-5">
            <div class="mb-4">
                <h4 class="text-sm font-bold text-gray-900 mb-1">System Settings</h4>
                <p class="text-xs text-gray-500">Configure preferences for {{ $roleLabel }}.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="border border-gray-100 rounded-xl p-4">
                    <h5 class="text-xs font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        General Settings
                    </h5>
                    <div class="space-y-3">
                        <div>
                            <label class="text-[10px] font-medium text-gray-500 uppercase">Company Name</label>
                            <p class="text-xs text-gray-900 font-medium mt-0.5">ASYX Group</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium text-gray-500 uppercase">Currency</label>
                            <p class="text-xs text-gray-900 font-medium mt-0.5">TZS (Tanzanian Shilling)</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium text-gray-500 uppercase">Timezone</label>
                            <p class="text-xs text-gray-900 font-medium mt-0.5">Africa/Dar_es_Salaam</p>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-100 rounded-xl p-4">
                    <h5 class="text-xs font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Your Profile
                    </h5>
                    <div class="space-y-3">
                        <div>
                            <label class="text-[10px] font-medium text-gray-500 uppercase">Name</label>
                            <p class="text-xs text-gray-900 font-medium mt-0.5">{{ auth()->user()->name }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium text-gray-500 uppercase">Role</label>
                            <p class="text-xs text-gray-900 font-medium mt-0.5">{{ $roleLabel }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium text-gray-500 uppercase">Email</label>
                            <p class="text-xs text-gray-900 font-medium mt-0.5">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 border border-gray-100 rounded-xl p-4">
                <h5 class="text-xs font-bold text-gray-700 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Your Permissions
                </h5>
                <div class="flex flex-wrap gap-1.5">
                    @php $userPerms = auth()->user()->permissionNames(); @endphp
                    @foreach($userPerms as $perm)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $perm }}</span>
                    @endforeach
                    @if(empty($userPerms))
                        <span class="text-xs text-gray-400">Full access (Admin)</span>
                    @endif
                </div>
            </div>
            @if($canEdit && isset($routeMap['settings']['edit']) && \Illuminate\Support\Facades\Route::has($routeMap['settings']['edit']))
            <div class="mt-4 flex justify-end">
                <a href="{{ route($routeMap['settings']['edit']) }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Settings
                </a>
            </div>
            @endif
        </div>
        @break

    @case('timesheets')
    @case('bugs')
    @case('assets')
    @case('policies')
    @case('performance')
    @case('training')
    @case('recruitment')
    @case('tenders')
    @case('journal-entries')
    @case('bank-reconciliation')
    @case('tax-management')
    @case('cost-centres')
    @case('receivables-aging')
    @case('payables-aging')
    @case('credit-limits')
    @case('salary-records')
    @case('deductions')
    @case('payslips')
    @case('budget-vs-actual')
    @case('budgets')
    @case('rfqs')
    @case('purchase-requisitions')
    @case('lpos')
    @case('grns')
    @case('batch-tracking')
    @case('barcodes')
    @case('asset-assignment')
    @case('asset-maintenance')
    @case('asset-disposal')
    @case('quotations')
    @case('sales-forecast')
    @case('market-analysis')
    @case('campaigns')
    @case('lead-source-reports')
    @case('project-profitability')
    @case('resource-allocation')
    @case('milestones')
    @case('tasks')
    @case('site-reports')
    @case('incidents')
    @case('team-tasks')
    @case('team-attendance')
    @case('team-timesheets')
    @case('team-review')
    @case('site-visits')
    @case('service-reports')
    @case('escalations')
    @case('knowledge-base')
    @case('call-statistics')
    @case('shift-schedule')
    @case('sla-reports')
    @case('disciplinary')
    @case('job-postings')
    @case('applications')
    @case('onboarding')
    @case('training-records')
    @case('certifications')
    @case('overtime')
    @case('operations-log')
    @case('operations-tasks')
    @case('vehicles')
    @case('driver-assignment')
    @case('fuel-logs')
    @case('trip-schedule')
    @case('deliveries')
    @case('shipments')
    @case('route-planning')
    @case('team-overview')
    @case('team-leaves')
    @case('announcements')
        <div class="p-5">
            <p class="text-sm text-gray-500 mb-4">{{ ucfirst(str_replace('-', ' ', $module)) }} module for {{ $roleLabel }}.</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Details</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                            @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
        @foreach(($employees ?? ($projects ?? collect()))->take(10) as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $item->name ?? ($item->first_name ?? '') . ' ' . ($item->last_name ?? '') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $item->position ?? $item->status ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ ($item->created_at ?? now())->format('d M Y') }}</td>
                            @if($hasActions)
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if($canDelete && isset($routeMap[$module]['delete']))
                                    <form action="{{ route($routeMap[$module]['delete'], $item) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this item?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
        @endforeach
        </tbody>
                </table>
            </div>
        </div>
        @break

    @case('my-account')
        @php $u = $user ?? []; $emp = $employee ?? null; @endphp
        <div class="p-5 space-y-5">
            {{-- Profile Header --}}
            <div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -mr-20 -mt-20"></div>
                <div class="relative z-10 flex items-center gap-5">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-bold text-2xl font-menu shadow-lg">
                        {{ strtoupper(substr($u['first_name'] ?? $u['name'] ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold">{{ $u['first_name'] ?? '' }} {{ $u['last_name'] ?? $u['name'] ?? '' }}</h2>
                        <p class="text-emerald-100 text-sm">{{ $roleLabel }}</p>
                        <div class="flex items-center gap-3 mt-1 text-xs text-emerald-200">
                            <span>{{ $u['email'] ?? '' }}</span>
                            @if($u['phone'] ?? false)<span>&middot; {{ $u['phone'] }}</span>@endif
                        </div>
                    </div>
                    <div class="text-right text-xs text-emerald-200">
                        <div>Member since</div>
                        <div class="font-medium text-white">{{ $u['created_at'] ? \Carbon\Carbon::parse($u['created_at'])->format('M Y') : '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Pending Leave</span>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingLeave ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Approved Leave</span>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $approvedLeave ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Open Tickets</span>
                    <p class="text-2xl font-bold text-rose-600 mt-1">{{ $openTickets ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Resolved Tickets</span>
                    <p class="text-2xl font-bold text-sky-600 mt-1">{{ $resolvedTickets ?? 0 }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Employee Info --}}
                <div class="bg-white rounded-xl border overflow-hidden">
                    <div class="px-4 py-3 border-b bg-gray-50/50"><h3 class="text-sm font-bold text-gray-900">Employee Details</h3></div>
                    <div class="p-4">
                        @if($emp)
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><span class="text-[10px] text-gray-400 uppercase">Employee ID</span><p class="font-medium text-gray-900 mt-0.5">{{ $emp->employee_id ?? '—' }}</p></div>
                            <div><span class="text-[10px] text-gray-400 uppercase">Designation</span><p class="font-medium text-gray-900 mt-0.5">{{ $emp->designation ?? '—' }}</p></div>
                            <div><span class="text-[10px] text-gray-400 uppercase">Department</span><p class="font-medium text-gray-900 mt-0.5">{{ $emp->department ?? '—' }}</p></div>
                            <div><span class="text-[10px] text-gray-400 uppercase">Status</span>
                                <p class="mt-0.5"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($emp->status ?? '') === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-600' }}">{{ ucfirst($emp->status ?? 'N/A') }}</span></p>
                            </div>
                            @if($emp->phone)
                            <div><span class="text-[10px] text-gray-400 uppercase">Phone</span><p class="font-medium text-gray-900 mt-0.5">{{ $emp->phone }}</p></div>
                            @endif
                            @if($emp->salary)
                            <div><span class="text-[10px] text-gray-400 uppercase">Salary</span><p class="font-medium text-emerald-700 mt-0.5">TZS {{ number_format($emp->salary, 2) }}</p></div>
                            @endif
                        </div>
                        @else
                        <div class="text-center py-4">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <p class="text-xs text-gray-400">No employee record linked.</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Recent Payslips --}}
                <div class="bg-white rounded-xl border overflow-hidden">
                    <div class="px-4 py-3 border-b bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900">Recent Payslips</h3>
                        <a href="{{ route('role.page', ['module' => 'payslips']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
                    </div>
                    <div class="p-4">
                        @if($recentPayslips->count())
                        <div class="space-y-2">
                            @foreach($recentPayslips as $p)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="text-xs font-medium text-gray-900">{{ $p->month }} {{ $p->year }}</p>
                                    <p class="text-[10px] {{ $p->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">{{ ucfirst($p->status) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-gray-900">TZS {{ number_format($p->net_salary, 2) }}</p>
                                    <a href="{{ route('payslip.download', $p->id) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">PDF</a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-xs text-gray-400 text-center py-4">No payslips yet.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Account Info --}}
            <div class="bg-white rounded-xl border overflow-hidden">
                <div class="px-4 py-3 border-b bg-gray-50/50"><h3 class="text-sm font-bold text-gray-900">Account Information</h3></div>
                <div class="p-4">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                        <div><span class="text-[10px] text-gray-400 uppercase">User ID</span><p class="font-medium text-gray-900 mt-0.5 font-mono">{{ $u['id'] ?? '—' }}</p></div>
                        <div><span class="text-[10px] text-gray-400 uppercase">Email</span><p class="font-medium text-gray-900 mt-0.5">{{ $u['email'] ?? '—' }}</p></div>
                        <div><span class="text-[10px] text-gray-400 uppercase">Phone</span><p class="font-medium text-gray-900 mt-0.5">{{ $u['phone'] ?? '—' }}</p></div>
                        <div><span class="text-[10px] text-gray-400 uppercase">Role</span><p class="font-medium text-gray-900 mt-0.5">{{ $roleLabel }}</p></div>
                    </div>
                </div>
            </div>
        </div>
        @break

    @case('documents')
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border p-4">
                <p class="text-[10px] font-medium text-gray-500 uppercase">Today</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayCount ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl border p-4">
                <p class="text-[10px] font-medium text-gray-500 uppercase">This Week</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $weekCount ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl border p-4">
                <p class="text-[10px] font-medium text-gray-500 uppercase">Pending</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $pendingCount ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl border p-4">
                <p class="text-[10px] font-medium text-gray-500 uppercase">Total</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalCount ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border overflow-hidden mb-6">
            <div class="px-5 py-4 border-b bg-gray-50/50">
                <form method="GET" action="{{ route('role.page', ['module' => 'documents']) }}" class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents..." class="w-full text-xs px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <select name="category" class="text-xs px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $key => $cat)
                        <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="text-xs px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending_signature" {{ request('status') === 'pending_signature' ? 'selected' : '' }}>Pending Signature</option>
                        <option value="signed" {{ request('status') === 'signed' ? 'selected' : '' }}>Signed</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    <button type="submit" class="px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors">Filter</button>
                    @if(request()->hasAny(['search', 'category', 'status']))
                    <a href="{{ route('role.page', ['module' => 'documents']) }}" class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">Clear</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Details</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                            @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse(($documents ?? collect())->items() ?? [] as $doc)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $loop->iteration + (($documents->currentPage() - 1) * $documents->perPage()) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-{{ ($categories[$doc->category]['color'] ?? 'slate') }}-100 flex items-center justify-center text-{{ ($categories[$doc->category]['color'] ?? 'slate') }}-700 flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-900">{{ $doc->title }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $doc->document_number }} &middot; {{ $categories[$doc->category]['label'] ?? ucfirst(str_replace('_', ' ', $doc->category)) }}</p>
                                        @if($doc->project)
                                        <p class="text-[10px] text-gray-400">Project: {{ $doc->project->title ?? $doc->project->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                    @if($doc->status === 'signed') bg-emerald-50 text-emerald-700
                                    @elseif($doc->status === 'pending_signature') bg-amber-50 text-amber-700
                                    @elseif($doc->status === 'archived') bg-gray-50 text-gray-700
                                    @elseif($doc->status === 'draft') bg-sky-50 text-sky-700
                                    @else bg-gray-50 text-gray-700 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $doc->created_at?->format('d M Y') }}</td>
                            @if($hasActions)
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if($canEdit && isset($routeMap[$module]['edit']))
                                    <a href="{{ route($routeMap[$module]['edit'], $doc) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                    @endif
                                    @if($canDelete && isset($routeMap[$module]['delete']))
                                    <form action="{{ route($routeMap[$module]['delete'], $doc) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this document?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $hasActions ? 5 : 4 }}" class="px-4 py-8 text-center text-gray-400 text-xs">
                                No records yet. Use the Add New button to create the first record.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t">{{ ($documents ?? null)?->links() ?? '' }}</div>
        </div>
        @break

    @default
        @php
        $fallbackPermCreate = 'create-' . $module;
        $fallbackPermEdit = 'edit-' . $module;
        $fallbackPermDelete = 'delete-' . $module;
        $fallbackRouteCreate = 'admin.' . $module . '.index';
        $fallbackRouteEdit = 'admin.' . $module . '.edit';
        $fallbackRouteDelete = 'admin.' . $module . '.destroy';
        $hasCreate = (isset($permMap[$module]['create']) && auth()->user()->hasPermission($permMap[$module]['create'])) || auth()->user()->hasPermission($fallbackPermCreate);
        $hasEdit = (isset($permMap[$module]['edit']) && auth()->user()->hasPermission($permMap[$module]['edit'])) || auth()->user()->hasPermission($fallbackPermEdit);
        $hasDelete = (isset($permMap[$module]['delete']) && auth()->user()->hasPermission($permMap[$module]['delete'])) || auth()->user()->hasPermission($fallbackPermDelete);
        $hasFallbackActions = $hasEdit || $hasDelete;
        $records = $items ?? collect([]);
        if ($records instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $recordRows = $records->items();
        } elseif ($records instanceof \Illuminate\Support\Collection) {
            $recordRows = $records->all();
        } elseif (is_array($records)) {
            $recordRows = $records;
        } else {
            $recordRows = [];
        }
        $displayStats = collect(['todayCount', 'weekCount', 'pendingCount', 'totalCount'])->mapWithKeys(function($k) {
            return [$k => ${$k} ?? 0];
        });
        @endphp
        <div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-2xl font-bold">{{ ucwords(str_replace('-', ' ', $module)) }}</h2>
                <p class="text-emerald-100 text-sm mt-1">Module for {{ $roleLabel }}.</p>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500 uppercase">Today</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayCount ?? 0 }}</p></div>
            <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500 uppercase">This Week</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ $weekCount ?? 0 }}</p></div>
            <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500 uppercase">Pending</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ $pendingCount ?? 0 }}</p></div>
            <div class="bg-white rounded-xl border p-4"><p class="text-[10px] font-medium text-gray-500 uppercase">Total</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalCount ?? 0 }}</p></div>
        </div>
        <div class="bg-white rounded-xl border p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Records</h3>
                @if($hasCreate)
                    @php
                        $createRoute = isset($routeMap[$module]['create']) && \Illuminate\Support\Facades\Route::has($routeMap[$module]['create']) ? $routeMap[$module]['create'] : (\Illuminate\Support\Facades\Route::has($fallbackRouteCreate) ? $fallbackRouteCreate : null);
                    @endphp
                    @if($createRoute)
                    <a href="{{ route($createRoute) }}" class="px-4 py-2 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors">Add New</a>
                    @endif
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Details</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                            @if($hasFallbackActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recordRows as $idx => $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-900">
                                {{ $item->title ?? $item->name ?? $item->subject ?? $item->description ?? ('Record #' . ($item->id ?? '-')) }}
                                @if($item->document_number ?? $item->invoice_number ?? $item->ticket_number ?? null)
                                <p class="text-[10px] text-gray-400">{{ $item->document_number ?? $item->invoice_number ?? $item->ticket_number }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php $s = $item->status ?? 'N/A'; @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ in_array($s, ['done','completed','signed','paid','active','approved']) ? 'bg-emerald-50 text-emerald-700' : (in_array($s, ['pending','in_progress','draft','pending_signature','open']) ? 'bg-amber-50 text-amber-700' : 'bg-gray-50 text-gray-700') }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ ($item->created_at ?? $item->date ?? null) ? \Carbon\Carbon::parse($item->created_at ?? $item->date)->format('d M Y') : '-' }}</td>
                            @if($hasFallbackActions)
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @php
                                        $editRoute = isset($routeMap[$module]['edit']) && \Illuminate\Support\Facades\Route::has($routeMap[$module]['edit']) ? $routeMap[$module]['edit'] : (\Illuminate\Support\Facades\Route::has($fallbackRouteEdit) ? $fallbackRouteEdit : null);
                                        $deleteRoute = isset($routeMap[$module]['delete']) && \Illuminate\Support\Facades\Route::has($routeMap[$module]['delete']) ? $routeMap[$module]['delete'] : (\Illuminate\Support\Facades\Route::has($fallbackRouteDelete) ? $fallbackRouteDelete : null);
                                    @endphp
                                    @if($hasEdit && $editRoute)
                                    <a href="{{ route($editRoute, $item) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                    @endif
                                    @if($hasDelete && $deleteRoute)
                                    <form action="{{ route($deleteRoute, $item) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this record?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $hasFallbackActions ? 5 : 4 }}" class="px-4 py-8 text-center text-xs text-gray-400">
                                No records yet. @if($hasCreate)<span>Use the <strong>Add New</strong> button to create the first record.</span>@endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($records instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="px-5 py-3 border-t">{{ $records->links() }}</div>
            @endif
        </div>
        @if(empty($recordRows) && !isset($todayCount))
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-amber-800 text-xs">
            <strong>Module in progress:</strong> Full CRUD and backend for {{ strtolower(str_replace('-', ' ', $module)) }} will be wired here. Admin has full access to all modules.
        </div>
        @endif
    @endswitch
</div>
@endsection
