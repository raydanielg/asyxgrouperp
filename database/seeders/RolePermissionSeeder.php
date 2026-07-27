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
            'System Administration' => ['view-users', 'create-users', 'edit-users', 'delete-users', 'view-roles', 'manage-roles', 'view-settings', 'view-companies', 'switch-companies', 'view-login-history', 'view-audit-logs'],
            'Approvals' => ['view-approvals', 'approve-final', 'approve-finance', 'approve-hr', 'approve-technical', 'approve-procurement'],
            'Companies' => ['view-companies', 'switch-companies'],
            'Tenders' => ['view-tenders', 'create-tenders', 'edit-tenders', 'delete-tenders'],
            'Contracts' => ['view-crm-contracts', 'create-crm-contracts', 'edit-crm-contracts', 'delete-crm-contracts'],
            'HR Overview' => ['view-employees', 'view-attendance', 'view-leaves', 'view-payroll', 'view-performance', 'view-training', 'view-recruitment'],
            'HR Actions' => ['create-employees', 'edit-employees', 'create-attendance', 'approve-leaves', 'create-performance', 'create-recruitment', 'view-disciplinary', 'view-events', 'create-events', 'view-policies', 'create-policies', 'view-job-postings', 'create-job-postings', 'view-applications'],
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
            'Inventory Actions' => ['create-products', 'edit-products', 'view-product-categories', 'create-warehouses', 'create-stock-movements', 'edit-suppliers', 'delete-suppliers'],
            'Finance' => ['view-sales-invoices', 'view-purchase-invoices', 'view-expenses', 'view-revenues', 'view-bills', 'view-bank-accounts', 'view-acc-transfers', 'view-reports', 'view-financial-reports', 'view-petty-cash'],
            'Finance Actions' => ['create-sales-invoices', 'edit-sales-invoices', 'create-purchase-invoices', 'edit-purchase-invoices', 'create-expenses', 'create-revenues', 'create-bills', 'create-bank-accounts', 'create-acc-transfers', 'view-cost-centres'],
            'GL' => ['view-journal-entries', 'create-journal-entries'],
            'Financial Reports' => ['view-financial-reports'],
            'Petty Cash' => ['view-petty-cash', 'create-petty-cash'],
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
            'Call Center' => ['view-crm-leads', 'create-crm-leads', 'view-helpdesk-tickets', 'create-helpdesk-tickets'],
            'Training' => ['view-training', 'create-training', 'view-certifications', 'create-certifications'],
            'Time Attendance' => ['view-attendance', 'create-attendance', 'view-overtime', 'approve-overtime'],
            'Fleet' => ['view-vehicles', 'create-vehicles', 'edit-vehicles', 'view-fuel-logs', 'create-fuel-logs', 'view-maintenance', 'create-maintenance'],
            'Logistics' => ['view-deliveries', 'create-deliveries', 'view-shipments', 'create-shipments'],
            'Self Service' => ['view-self-service', 'view-my-payslips', 'apply-leave', 'view-my-attendance', 'view-my-timesheets', 'create-my-timesheets', 'view-announcements'],
            'Manager Self Service' => ['view-team-overview', 'approve-team-leaves', 'view-team-attendance', 'approve-team-timesheets'],
            'Reception' => ['view-visitors', 'create-visitors', 'edit-visitors', 'delete-visitors', 'view-appointments', 'create-appointments', 'edit-appointments', 'delete-appointments', 'view-calls', 'create-calls', 'edit-calls', 'delete-calls', 'view-correspondence', 'create-correspondence', 'edit-correspondence', 'delete-correspondence', 'view-parcels', 'create-parcels', 'edit-parcels', 'delete-parcels', 'view-front-desk', 'create-front-desk', 'edit-front-desk', 'delete-front-desk', 'view-departments', 'create-departments', 'edit-departments', 'delete-departments', 'view-announcements', 'create-announcements', 'edit-announcements', 'delete-announcements', 'view-messages', 'create-messages', 'edit-messages', 'delete-messages'],
            'Documents' => ['view-documents', 'create-documents', 'edit-documents', 'delete-documents'],
            'Meetings' => ['view-meetings', 'create-meetings', 'edit-meetings'],
            'Audit' => ['view-audit-logs', 'view-login-history'],
            'POS' => ['view-pos', 'create-pos'],
            'Job Cards' => ['view-job-cards', 'create-job-cards', 'edit-job-cards', 'delete-job-cards'],
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

        // ═══════════════════════════════════════════════════════
        // CONSOLIDATED ROLE STRUCTURE — 11 roles total.
        // superadmin/admin = full system access.
        // Every other business area has exactly ONE role.
        // ═══════════════════════════════════════════════════════
        $roles = [
            // System Administration (kept both as top-level access roles)
            ['name' => 'admin', 'label' => 'System Admin', 'perms' => array_keys($allPermissions)],
            ['name' => 'superadmin', 'label' => 'Super Admin', 'perms' => array_keys($allPermissions)],

            // Executive
            ['name' => 'director', 'label' => 'Director', 'perms' => ['view-dashboard', 'view-reports', 'view-financial-reports', 'view-approvals', 'approve-final', 'view-companies', 'switch-companies', 'view-tenders', 'view-crm-contracts', 'view-employees', 'view-sales-invoices', 'view-purchase-invoices', 'view-expenses', 'view-revenues', 'view-projects', 'view-helpdesk-tickets', 'view-crm-leads', 'view-crm-deals', 'view-products', 'view-warehouses', 'view-pos', 'view-self-service', 'view-my-payslips']],

            // Finance
            ['name' => 'accountant', 'label' => 'Accountant', 'perms' => ['view-dashboard', 'view-journal-entries', 'create-journal-entries', 'view-financial-reports', 'view-petty-cash', 'create-petty-cash', 'view-sales-invoices', 'create-sales-invoices', 'view-purchase-invoices', 'create-purchase-invoices', 'view-expenses', 'create-expenses', 'view-revenues', 'create-revenues', 'view-bills', 'create-bills', 'view-cost-centres', 'view-bank-accounts', 'view-acc-transfers', 'view-reports', 'view-self-service', 'view-my-payslips']],
            ['name' => 'finance_manager', 'label' => 'Finance Manager', 'perms' => ['view-dashboard', 'view-reports', 'view-sales-invoices', 'view-purchase-invoices', 'view-expenses', 'view-revenues', 'view-bills', 'view-bank-accounts', 'view-acc-transfers', 'view-budgets', 'create-budgets', 'edit-budgets', 'view-journal-entries', 'create-journal-entries', 'view-financial-reports', 'view-petty-cash', 'create-petty-cash', 'view-bank-reconciliation', 'create-bank-reconciliation', 'view-tax-management', 'create-tax-management', 'view-credit-limits', 'edit-credit-limits', 'view-approvals', 'approve-finance', 'view-payroll', 'create-payroll', 'view-self-service', 'view-my-payslips']],

            // Procurement & Inventory (single manager role)
            ['name' => 'procurement_manager', 'label' => 'Procurement Manager', 'perms' => ['view-dashboard', 'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers', 'view-rfqs', 'create-rfqs', 'edit-rfqs', 'view-lpos', 'create-lpos', 'view-grns', 'create-grns', 'view-purchase-requisitions', 'create-purchase-requisitions', 'view-purchase-invoices', 'view-products', 'create-products', 'edit-products', 'view-product-categories', 'view-warehouses', 'create-warehouses', 'view-stock-movements', 'create-stock-movements', 'view-transfers', 'view-assets', 'create-assets', 'edit-assets', 'view-approvals', 'approve-procurement', 'view-reports', 'view-self-service', 'view-my-payslips']],

            // Sales (single manager role)
            ['name' => 'sales_manager', 'label' => 'Sales Manager', 'perms' => ['view-dashboard', 'view-crm-leads', 'create-crm-leads', 'edit-crm-leads', 'view-crm-deals', 'create-crm-deals', 'edit-crm-deals', 'view-crm-contacts', 'create-crm-contacts', 'edit-crm-contacts', 'view-sales-invoices', 'create-sales-invoices', 'view-quotations', 'create-quotations', 'view-sales-proposals', 'view-campaigns', 'create-campaigns', 'view-reports', 'view-self-service', 'view-my-payslips']],

            // Projects (single manager role)
            ['name' => 'project_manager', 'label' => 'Project Manager', 'perms' => ['view-dashboard', 'view-projects', 'create-projects', 'edit-projects', 'view-timesheets', 'create-timesheets', 'view-bugs', 'create-bugs', 'view-employees', 'view-crm-deals', 'view-budgets', 'view-site-reports', 'create-site-reports', 'view-meetings', 'create-meetings', 'view-documents', 'view-reports', 'view-self-service', 'view-my-payslips', 'view-job-cards', 'create-job-cards', 'edit-job-cards']],

            // Technical / IT (single manager role)
            ['name' => 'technical_manager', 'label' => 'Technical Manager', 'perms' => ['view-dashboard', 'view-projects', 'create-projects', 'edit-projects', 'view-timesheets', 'view-bugs', 'create-bugs', 'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-lpos', 'approve-technical', 'view-assets', 'create-assets', 'edit-assets', 'view-settings', 'view-employees', 'view-login-history', 'view-audit-logs', 'view-self-service', 'view-my-payslips', 'view-job-cards', 'create-job-cards', 'edit-job-cards', 'delete-job-cards']],

            // Technical field staff
            ['name' => 'technician', 'label' => 'Technician', 'perms' => ['view-dashboard', 'view-projects', 'view-job-cards', 'create-job-cards', 'edit-job-cards', 'view-helpdesk-tickets', 'create-helpdesk-tickets', 'view-bugs', 'create-bugs', 'view-self-service', 'view-my-payslips']],

            // Services & Operations (single manager role)
            ['name' => 'operations_manager', 'label' => 'Operations Manager', 'perms' => ['view-dashboard', 'view-fleet', 'view-vehicles', 'create-vehicles', 'edit-vehicles', 'view-fuel-logs', 'create-fuel-logs', 'view-maintenance', 'create-maintenance', 'view-helpdesk-tickets', 'view-deliveries', 'create-deliveries', 'view-shipments', 'create-shipments', 'view-products', 'view-warehouses', 'view-stock-movements', 'view-reports', 'view-self-service', 'view-my-payslips']],

            // HR (single manager role)
            ['name' => 'hr_manager', 'label' => 'HR Manager', 'perms' => ['view-dashboard', 'view-employees', 'create-employees', 'edit-employees', 'view-attendance', 'create-attendance', 'view-overtime', 'approve-overtime', 'view-payroll', 'create-payroll', 'view-leaves', 'approve-leaves', 'view-performance', 'create-performance', 'view-training', 'create-training', 'view-certifications', 'create-certifications', 'view-recruitment', 'create-recruitment', 'view-job-postings', 'create-job-postings', 'view-applications', 'view-approvals', 'approve-hr', 'view-disciplinary', 'view-assets', 'view-policies', 'create-policies', 'view-events', 'create-events', 'view-self-service', 'view-my-payslips']],
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

        // ═══════════════════════════════════════════════════════
        // Remove all roles that are NOT part of the consolidated
        // structure above (and any users/pivots tied to them).
        // ═══════════════════════════════════════════════════════
        $keepRoles = array_column($roles, 'name');
        $obsoleteRoles = DB::table('roles')->whereNotIn('name', $keepRoles)->get();

        foreach ($obsoleteRoles as $obsolete) {
            $userIds = DB::table('role_user')->where('role_id', $obsolete->id)->pluck('user_id')
                ->merge(DB::table('users')->where('role', $obsolete->name)->pluck('id'))
                ->unique();

            foreach ($userIds as $userId) {
                DB::table('role_user')->where('user_id', $userId)->delete();
                try {
                    DB::table('users')->where('id', $userId)->delete();
                } catch (\Throwable $e) {
                    // Referenced elsewhere (employees, created_by, etc.) — disable login instead
                    DB::table('users')->where('id', $userId)->update(['is_enable_login' => false]);
                }
            }

            DB::table('role_permission')->where('role_id', $obsolete->id)->delete();
            DB::table('role_user')->where('role_id', $obsolete->id)->delete();
            DB::table('roles')->where('id', $obsolete->id)->delete();
        }
    }
}
