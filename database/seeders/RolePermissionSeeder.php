<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Ensure all role-related permissions exist
        $permissionSets = [
            'Dashboard' => ['view-dashboard'],
            'Reports' => ['view-reports'],
            'Approvals' => ['view-approvals', 'approve-final', 'approve-finance', 'approve-hr', 'approve-technical', 'approve-procurement'],
            'Companies' => ['view-companies', 'switch-companies'],
            'Tenders' => ['view-tenders', 'create-tenders', 'edit-tenders', 'delete-tenders'],
            'Contracts' => ['view-crm-contracts', 'create-crm-contracts', 'edit-crm-contracts', 'delete-crm-contracts'],
            'HR Overview' => ['view-employees', 'view-attendance', 'view-leaves', 'view-payroll', 'view-performance', 'view-training', 'view-recruitment'],
            'Company Dashboard' => ['view-dashboard'],
            'Department Reports' => ['view-reports'],
            'Departmental Approvals' => ['view-approvals', 'approve-finance', 'approve-hr', 'approve-technical', 'approve-procurement'],
            'Sales and CRM' => ['view-crm-leads', 'create-crm-leads', 'edit-crm-leads', 'view-crm-deals', 'create-crm-deals', 'edit-crm-deals', 'view-sales-invoices', 'view-sales-proposals', 'view-quotations'],
            'Projects' => ['view-projects', 'create-projects', 'edit-projects', 'view-timesheets', 'view-bugs'],
            'Technical' => ['view-projects', 'view-timesheets', 'view-bugs', 'create-bugs', 'view-helpdesk-tickets', 'edit-helpdesk-tickets'],
            'LPO Approval' => ['view-lpos', 'approve-technical'],
            'Operations' => ['view-fleet', 'view-vehicles', 'view-fuel-logs', 'view-maintenance', 'view-helpdesk-tickets', 'view-inventory', 'view-products', 'view-warehouses'],
            'Service Desk' => ['view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets'],
            'Inventory' => ['view-products', 'view-warehouses', 'view-stock-movements', 'view-suppliers', 'view-transfers'],
            'Finance' => ['view-sales-invoices', 'view-purchase-invoices', 'view-expenses', 'view-revenues', 'view-bills', 'view-bank-accounts', 'view-acc-transfers', 'view-reports'],
            'GL' => ['view-journal-entries', 'create-journal-entries'],
            'Bank' => ['view-bank-reconciliation', 'create-bank-reconciliation'],
            'Tax' => ['view-tax-management', 'create-tax-management'],
            'Payroll' => ['view-payroll', 'create-payroll', 'view-payslips', 'create-payslips'],
            'Budget' => ['view-budgets', 'create-budgets', 'edit-budgets'],
            'Credit' => ['view-credit-limits', 'edit-credit-limits'],
            'Procurement' => ['view-suppliers', 'create-suppliers', 'view-lpos', 'create-lpos', 'view-grns', 'create-grns', 'view-purchase-invoices'],
            'RFQ' => ['view-rfqs', 'create-rfqs', 'edit-rfqs'],
            'Purchase Requisitions' => ['view-purchase-requisitions', 'create-purchase-requisitions'],
            'Goods' => ['view-grns', 'create-grns'],
            'Store' => ['view-warehouses', 'create-warehouses', 'view-stock-movements', 'create-stock-movements', 'view-products', 'view-suppliers'],
            'Asset' => ['view-assets', 'create-assets', 'edit-assets', 'delete-assets'],
            'Sales' => ['view-crm-leads', 'create-crm-leads', 'view-crm-deals', 'create-crm-deals', 'view-sales-invoices', 'create-sales-invoices', 'view-quotations', 'create-quotations'],
            'CRM' => ['view-crm-contacts', 'create-crm-contacts', 'edit-crm-contacts', 'view-crm-leads', 'create-crm-leads'],
            'Marketing' => ['view-campaigns', 'create-campaigns'],
            'Project Management' => ['view-projects', 'create-projects', 'edit-projects', 'view-timesheets', 'create-timesheets', 'view-bugs', 'create-bugs'],
            'Site' => ['view-site-reports', 'create-site-reports', 'view-attendance'],
            'Team Lead' => ['view-team-timesheets', 'approve-team-timesheets', 'view-team-leaves', 'approve-team-leaves'],
            'Project Finance' => ['view-project-budgets', 'view-sales-invoices', 'create-sales-invoices'],
            'Engineer' => ['view-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-assets', 'view-projects'],
            'NOC' => ['view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets'],
            'Call Center' => ['view-call-logs', 'create-call-logs', 'view-crm-leads', 'create-crm-leads', 'view-helpdesk-tickets', 'create-helpdesk-tickets'],
            'Training' => ['view-training', 'create-training', 'view-certifications', 'create-certifications'],
            'Time Attendance' => ['view-attendance', 'create-attendance', 'view-overtime', 'approve-overtime'],
            'Fleet' => ['view-vehicles', 'create-vehicles', 'edit-vehicles', 'view-fuel-logs', 'create-fuel-logs', 'view-maintenance', 'create-maintenance'],
            'Logistics' => ['view-deliveries', 'create-deliveries', 'view-shipments', 'create-shipments'],
            'Self Service' => ['view-self-service', 'view-my-payslips', 'apply-leave', 'view-my-attendance', 'view-my-timesheets', 'create-my-timesheets', 'view-announcements'],
            'Manager Self Service' => ['view-team-overview', 'approve-team-leaves', 'view-team-attendance', 'approve-team-timesheets'],
            'Reception' => ['view-visitors', 'create-visitors', 'view-appointments', 'create-appointments', 'view-calls', 'create-calls', 'view-correspondence', 'create-correspondence', 'view-parcels', 'create-parcels', 'view-front-desk', 'view-departments', 'view-announcements'],
            'Documents' => ['view-documents', 'create-documents', 'edit-documents', 'delete-documents'],
            'Meetings' => ['view-meetings', 'create-meetings', 'edit-meetings'],
        ];

        $allPermissions = [];
        foreach ($permissionSets as $module => $perms) {
            foreach ($perms as $perm) {
                $allPermissions[$perm] = ucwords(str_replace('-', ' ', $perm));
            }
        }

        foreach ($allPermissions as $name => $label) {
            $existing = DB::table('permissions')->where('name', $name)->first();
            if (!$existing) {
                DB::table('permissions')->insert([
                    'name' => $name,
                    'label' => $label,
                    'module' => 'Role Based',
                    'group' => 'roles',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $permMap = DB::table('permissions')->pluck('id', 'name')->toArray();

        $roles = [
            // Executive
            ['name' => 'managing_director', 'label' => 'Managing Director', 'perms' => ['view-dashboard', 'view-reports', 'view-approvals', 'approve-final', 'view-companies', 'switch-companies', 'view-tenders', 'view-crm-contracts', 'view-employees', 'view-sales-invoices', 'view-purchase-invoices', 'view-expenses', 'view-revenues', 'view-projects', 'view-helpdesk-tickets', 'view-self-service', 'view-my-payslips']],
            ['name' => 'general_manager', 'label' => 'General Manager', 'perms' => ['view-dashboard', 'view-reports', 'view-approvals', 'approve-finance', 'approve-hr', 'approve-technical', 'view-projects', 'view-crm-leads', 'view-crm-deals', 'view-sales-invoices', 'view-employees', 'view-attendance', 'view-leaves', 'view-helpdesk-tickets', 'view-self-service', 'view-my-payslips']],
            ['name' => 'technical_manager', 'label' => 'Technical Manager', 'perms' => ['view-dashboard', 'view-projects', 'view-timesheets', 'view-bugs', 'create-bugs', 'view-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-lpos', 'approve-technical', 'view-assets', 'view-employees', 'view-self-service', 'view-my-payslips']],
            ['name' => 'operations_manager', 'label' => 'Operations Manager', 'perms' => ['view-dashboard', 'view-fleet', 'view-vehicles', 'view-fuel-logs', 'view-maintenance', 'view-helpdesk-tickets', 'view-products', 'view-warehouses', 'view-stock-movements', 'view-self-service', 'view-my-payslips']],

            // Finance
            ['name' => 'finance_manager', 'label' => 'Finance Manager', 'perms' => ['view-dashboard', 'view-reports', 'view-sales-invoices', 'view-purchase-invoices', 'view-expenses', 'view-revenues', 'view-bills', 'view-bank-accounts', 'view-acc-transfers', 'view-budgets', 'view-journal-entries', 'view-bank-reconciliation', 'view-tax-management', 'view-approvals', 'approve-finance', 'view-payroll', 'view-self-service', 'view-my-payslips']],
            ['name' => 'chief_accountant', 'label' => 'Chief Accountant', 'perms' => ['view-dashboard', 'view-reports', 'view-journal-entries', 'create-journal-entries', 'view-bank-reconciliation', 'create-bank-reconciliation', 'view-tax-management', 'create-tax-management', 'view-sales-invoices', 'view-purchase-invoices', 'view-expenses', 'view-revenues', 'view-bills', 'view-self-service', 'view-my-payslips']],
            ['name' => 'accountant', 'label' => 'Accountant', 'perms' => ['view-dashboard', 'view-journal-entries', 'create-journal-entries', 'view-sales-invoices', 'create-sales-invoices', 'view-purchase-invoices', 'create-purchase-invoices', 'view-expenses', 'create-expenses', 'view-cost-centres', 'view-self-service', 'view-my-payslips']],
            ['name' => 'accounts_receivable_officer', 'label' => 'Accounts Receivable Officer', 'perms' => ['view-dashboard', 'view-sales-invoices', 'create-sales-invoices', 'view-revenues', 'create-revenues', 'view-credit-limits', 'view-self-service', 'view-my-payslips']],
            ['name' => 'accounts_payable_officer', 'label' => 'Accounts Payable Officer', 'perms' => ['view-dashboard', 'view-purchase-invoices', 'create-purchase-invoices', 'view-expenses', 'create-expenses', 'view-bills', 'create-bills', 'view-self-service', 'view-my-payslips']],
            ['name' => 'cashier', 'label' => 'Cashier', 'perms' => ['view-dashboard', 'view-pos', 'create-pos', 'view-sales-invoices', 'view-revenues', 'create-revenues', 'view-self-service', 'view-my-payslips']],
            ['name' => 'payroll_officer', 'label' => 'Payroll Officer', 'perms' => ['view-dashboard', 'view-payroll', 'create-payroll', 'view-employees', 'view-payslips', 'create-payslips', 'view-attendance', 'view-self-service', 'view-my-payslips']],
            ['name' => 'budget_officer', 'label' => 'Budget Officer', 'perms' => ['view-dashboard', 'view-budgets', 'create-budgets', 'edit-budgets', 'view-reports', 'view-expenses', 'view-self-service', 'view-my-payslips']],
            ['name' => 'credit_controller', 'label' => 'Credit Controller', 'perms' => ['view-dashboard', 'view-credit-limits', 'edit-credit-limits', 'view-sales-invoices', 'view-self-service', 'view-my-payslips']],

            // Procurement
            ['name' => 'procurement_manager', 'label' => 'Procurement Manager', 'perms' => ['view-dashboard', 'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers', 'view-rfqs', 'create-rfqs', 'view-lpos', 'create-lpos', 'view-purchase-requisitions', 'create-purchase-requisitions', 'view-approvals', 'approve-procurement', 'view-reports', 'view-self-service', 'view-my-payslips']],
            ['name' => 'procurement_officer', 'label' => 'Procurement Officer', 'perms' => ['view-dashboard', 'view-rfqs', 'create-rfqs', 'view-purchase-requisitions', 'create-purchase-requisitions', 'view-lpos', 'create-lpos', 'view-grns', 'create-grns', 'view-suppliers', 'view-self-service', 'view-my-payslips']],
            ['name' => 'tender_officer', 'label' => 'Tender Officer', 'perms' => ['view-dashboard', 'view-tenders', 'create-tenders', 'edit-tenders', 'view-documents', 'view-self-service', 'view-my-payslips']],

            // Inventory
            ['name' => 'store_manager', 'label' => 'Store Manager', 'perms' => ['view-dashboard', 'view-warehouses', 'create-warehouses', 'view-stock-movements', 'create-stock-movements', 'view-products', 'view-suppliers', 'view-transfers', 'view-reports', 'view-self-service', 'view-my-payslips']],
            ['name' => 'storekeeper', 'label' => 'Storekeeper', 'perms' => ['view-dashboard', 'view-stock-movements', 'create-stock-movements', 'view-grns', 'create-grns', 'view-products', 'view-self-service', 'view-my-payslips']],
            ['name' => 'inventory_controller', 'label' => 'Inventory Controller', 'perms' => ['view-dashboard', 'view-products', 'create-products', 'edit-products', 'view-stock-movements', 'view-warehouses', 'view-self-service', 'view-my-payslips']],
            ['name' => 'asset_officer', 'label' => 'Asset Officer', 'perms' => ['view-dashboard', 'view-assets', 'create-assets', 'edit-assets', 'view-employees', 'view-self-service', 'view-my-payslips']],

            // Sales
            ['name' => 'sales_manager', 'label' => 'Sales Manager', 'perms' => ['view-dashboard', 'view-crm-leads', 'view-crm-deals', 'view-sales-invoices', 'view-quotations', 'view-sales-proposals', 'view-reports', 'view-self-service', 'view-my-payslips']],
            ['name' => 'business_development_manager', 'label' => 'Business Development Manager', 'perms' => ['view-dashboard', 'view-crm-leads', 'create-crm-leads', 'edit-crm-leads', 'view-crm-deals', 'create-crm-deals', 'view-self-service', 'view-my-payslips']],
            ['name' => 'sales_executive', 'label' => 'Sales Executive', 'perms' => ['view-dashboard', 'view-crm-leads', 'create-crm-leads', 'view-crm-deals', 'create-crm-deals', 'view-quotations', 'create-quotations', 'view-self-service', 'view-my-payslips']],
            ['name' => 'crm_officer', 'label' => 'CRM Officer', 'perms' => ['view-dashboard', 'view-crm-contacts', 'create-crm-contacts', 'edit-crm-contacts', 'view-crm-leads', 'view-crm-deals', 'view-self-service', 'view-my-payslips']],
            ['name' => 'marketing_officer', 'label' => 'Marketing Officer', 'perms' => ['view-dashboard', 'view-campaigns', 'create-campaigns', 'view-crm-leads', 'view-self-service', 'view-my-payslips']],

            // Projects
            ['name' => 'project_director', 'label' => 'Project Director', 'perms' => ['view-dashboard', 'view-reports', 'view-projects', 'view-budgets', 'view-timesheets', 'view-self-service', 'view-my-payslips']],
            ['name' => 'project_manager', 'label' => 'Project Manager', 'perms' => ['view-dashboard', 'view-projects', 'create-projects', 'edit-projects', 'view-timesheets', 'create-timesheets', 'view-bugs', 'create-bugs', 'view-employees', 'view-crm-deals', 'view-self-service', 'view-my-payslips']],
            ['name' => 'technical_projects_manager', 'label' => 'Technical Projects Manager', 'perms' => ['view-dashboard', 'view-projects', 'create-projects', 'edit-projects', 'view-timesheets', 'create-timesheets', 'view-bugs', 'create-bugs', 'view-employees', 'view-self-service', 'view-my-payslips']],
            ['name' => 'project_coordinator', 'label' => 'Project Coordinator', 'perms' => ['view-dashboard', 'view-projects', 'view-timesheets', 'create-timesheets', 'view-bugs', 'view-meetings', 'create-meetings', 'view-documents', 'view-self-service', 'view-my-payslips']],
            ['name' => 'project_engineer', 'label' => 'Project Engineer', 'perms' => ['view-dashboard', 'view-projects', 'view-timesheets', 'create-timesheets', 'view-site-reports', 'create-site-reports', 'view-self-service', 'view-my-payslips']],
            ['name' => 'site_supervisor', 'label' => 'Site Supervisor', 'perms' => ['view-dashboard', 'view-attendance', 'view-site-reports', 'create-site-reports', 'view-helpdesk-tickets', 'create-helpdesk-tickets', 'view-self-service', 'view-my-payslips']],
            ['name' => 'team_leader', 'label' => 'Team Leader', 'perms' => ['view-dashboard', 'view-team-timesheets', 'approve-team-timesheets', 'view-team-leaves', 'approve-team-leaves', 'view-team-attendance', 'view-self-service', 'view-my-payslips']],
            ['name' => 'project_accountant', 'label' => 'Project Accountant', 'perms' => ['view-dashboard', 'view-project-budgets', 'view-sales-invoices', 'create-sales-invoices', 'view-expenses', 'view-revenues', 'view-self-service', 'view-my-payslips']],

            // Technical
            ['name' => 'senior_systems_engineer', 'label' => 'Senior Systems Engineer', 'perms' => ['view-dashboard', 'view-projects', 'view-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-assets', 'view-documents', 'view-self-service', 'view-my-payslips']],
            ['name' => 'systems_engineer', 'label' => 'Systems Engineer', 'perms' => ['view-dashboard', 'view-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-assets', 'view-maintenance', 'view-self-service', 'view-my-payslips']],
            ['name' => 'support_engineer', 'label' => 'Support Engineer / Field Technician', 'perms' => ['view-dashboard', 'view-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-site-reports', 'create-site-reports', 'view-assets', 'view-self-service', 'view-my-payslips']],
            ['name' => 'noc_engineer', 'label' => 'NOC Engineer', 'perms' => ['view-dashboard', 'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-assets', 'view-self-service', 'view-my-payslips']],

            // Service Desk
            ['name' => 'service_desk_manager', 'label' => 'Service Desk Manager', 'perms' => ['view-dashboard', 'view-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-reports', 'view-call-logs', 'view-self-service', 'view-my-payslips']],
            ['name' => 'helpdesk_supervisor', 'label' => 'Helpdesk Supervisor', 'perms' => ['view-dashboard', 'view-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-call-logs', 'view-self-service', 'view-my-payslips']],
            ['name' => 'helpdesk_officer', 'label' => 'Helpdesk Officer', 'perms' => ['view-dashboard', 'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-self-service', 'view-my-payslips']],
            ['name' => 'call_center_supervisor', 'label' => 'Call Center Supervisor', 'perms' => ['view-dashboard', 'view-call-logs', 'view-reports', 'view-self-service', 'view-my-payslips']],
            ['name' => 'call_center_agent', 'label' => 'Call Center Agent', 'perms' => ['view-dashboard', 'view-call-logs', 'create-call-logs', 'view-crm-leads', 'create-crm-leads', 'view-helpdesk-tickets', 'create-helpdesk-tickets', 'view-self-service', 'view-my-payslips']],

            // HR
            ['name' => 'hr_manager', 'label' => 'HR Manager', 'perms' => ['view-dashboard', 'view-employees', 'create-employees', 'edit-employees', 'view-attendance', 'view-payroll', 'view-leaves', 'approve-leaves', 'view-performance', 'view-training', 'view-recruitment', 'view-approvals', 'approve-hr', 'view-disciplinary', 'view-assets', 'view-policies', 'view-self-service', 'view-my-payslips']],
            ['name' => 'hr_officer', 'label' => 'HR Officer', 'perms' => ['view-dashboard', 'view-employees', 'create-employees', 'edit-employees', 'view-attendance', 'view-leaves', 'view-performance', 'view-training', 'view-recruitment', 'view-assets', 'view-policies', 'view-self-service', 'view-my-payslips']],
            ['name' => 'recruitment_officer', 'label' => 'Recruitment Officer', 'perms' => ['view-dashboard', 'view-recruitment', 'create-recruitment', 'view-job-postings', 'create-job-postings', 'view-applications', 'view-self-service', 'view-my-payslips']],
            ['name' => 'training_officer', 'label' => 'Training Officer', 'perms' => ['view-dashboard', 'view-training', 'create-training', 'view-certifications', 'create-certifications', 'view-employees', 'view-self-service', 'view-my-payslips']],
            ['name' => 'time_and_attendance_officer', 'label' => 'Time and Attendance Officer', 'perms' => ['view-dashboard', 'view-attendance', 'create-attendance', 'view-overtime', 'approve-overtime', 'view-employees', 'view-self-service', 'view-my-payslips']],

            // Operations
            ['name' => 'operations_officer', 'label' => 'Operations Officer', 'perms' => ['view-dashboard', 'view-helpdesk-tickets', 'view-fleet', 'view-vehicles', 'view-deliveries', 'view-self-service', 'view-my-payslips']],
            ['name' => 'fleet_manager', 'label' => 'Fleet Manager', 'perms' => ['view-dashboard', 'view-vehicles', 'create-vehicles', 'edit-vehicles', 'view-fuel-logs', 'create-fuel-logs', 'view-maintenance', 'create-maintenance', 'view-self-service', 'view-my-payslips']],
            ['name' => 'logistics_officer', 'label' => 'Logistics Officer', 'perms' => ['view-dashboard', 'view-deliveries', 'create-deliveries', 'view-shipments', 'create-shipments', 'view-vehicles', 'view-self-service', 'view-my-payslips']],

            // Self-Service
            ['name' => 'employee_self_service', 'label' => 'Employee Self-Service', 'perms' => ['view-self-service', 'view-my-payslips', 'apply-leave', 'view-my-attendance', 'view-my-timesheets', 'create-my-timesheets', 'view-announcements']],
            ['name' => 'manager_self_service', 'label' => 'Manager Self-Service', 'perms' => ['view-self-service', 'view-my-payslips', 'apply-leave', 'view-my-attendance', 'view-my-timesheets', 'create-my-timesheets', 'view-team-overview', 'approve-team-leaves', 'view-team-attendance', 'approve-team-timesheets', 'view-announcements']],

        ];

        foreach ($roles as $roleData) {
            $role = DB::table('roles')->where('name', $roleData['name'])->first();
            if (!$role) {
                $roleId = DB::table('roles')->insertGetId([
                    'name' => $roleData['name'],
                    'label' => $roleData['label'],
                    'editable' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $roleId = $role->id;
                DB::table('roles')->where('id', $roleId)->update([
                    'label' => $roleData['label'],
                    'updated_at' => $now,
                ]);
                DB::table('role_permission')->where('role_id', $roleId)->delete();
            }

            foreach ($roleData['perms'] as $permName) {
                if (isset($permMap[$permName])) {
                    DB::table('role_permission')->insertOrIgnore([
                        'role_id' => $roleId,
                        'permission_id' => $permMap[$permName],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
