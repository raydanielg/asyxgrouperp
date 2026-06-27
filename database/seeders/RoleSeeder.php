<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // All roles with their permission mappings
        $roles = [
            [
                'name' => 'director',
                'label' => 'Director',
                'permissions' => ['view-dashboard', 'view-reports', 'view-sales-invoices', 'view-purchase-invoices', 'view-expenses', 'view-revenues', 'view-projects', 'view-employees', 'view-pos', 'view-products', 'view-warehouses', 'view-helpdesk-tickets', 'view-crm-leads', 'view-crm-deals', 'view-bills', 'view-bank-accounts', 'view-acc-transfers', 'view-settings'],
            ],
            [
                'name' => 'admin_manager',
                'label' => 'Admin Manager',
                'permissions' => ['view-dashboard', 'view-users', 'create-users', 'edit-users', 'delete-users', 'view-roles', 'manage-roles', 'view-login-history', 'view-employees', 'view-attendance', 'view-leaves', 'approve-leaves', 'view-settings', 'view-reports'],
            ],
            [
                'name' => 'administrator',
                'label' => 'Administrator',
                'permissions' => ['view-dashboard', 'view-users', 'create-users', 'edit-users', 'delete-users', 'view-roles', 'manage-roles', 'view-settings', 'view-reports', 'view-employees', 'view-attendance', 'view-payroll', 'view-leaves', 'approve-leaves', 'view-performance', 'view-training', 'view-recruitment', 'view-assets', 'view-events', 'view-policies', 'view-crm-leads', 'view-crm-deals', 'view-crm-contracts', 'view-crm-contacts', 'view-bank-accounts', 'view-acc-transfers', 'view-expenses', 'create-expenses', 'view-revenues', 'create-revenues', 'view-bills', 'view-projects', 'view-timesheets', 'view-bugs', 'view-products', 'view-product-categories', 'view-suppliers', 'view-stock-movements', 'view-pos', 'view-sales-invoices', 'view-purchase-invoices', 'view-warehouses', 'view-helpdesk-tickets'],
            ],
            [
                'name' => 'finance_officer',
                'label' => 'Finance Officer',
                'permissions' => ['view-dashboard', 'view-sales-invoices', 'create-sales-invoices', 'edit-sales-invoices', 'view-purchase-invoices', 'create-purchase-invoices', 'edit-purchase-invoices', 'view-expenses', 'create-expenses', 'view-revenues', 'create-revenues', 'view-bills', 'create-bills', 'view-bank-accounts', 'create-bank-accounts', 'view-acc-transfers', 'create-acc-transfers', 'view-reports'],
            ],
            [
                'name' => 'auditor',
                'label' => 'Auditor',
                'permissions' => ['view-dashboard', 'view-sales-invoices', 'view-purchase-invoices', 'view-expenses', 'view-revenues', 'view-bills', 'view-bank-accounts', 'view-acc-transfers', 'view-reports', 'view-warehouses', 'view-products', 'view-stock-movements', 'view-pos'],
            ],
            [
                'name' => 'hr_officer',
                'label' => 'HR Officer',
                'permissions' => ['view-dashboard', 'view-employees', 'create-employees', 'edit-employees', 'view-attendance', 'create-attendance', 'view-payroll', 'create-payroll', 'view-leaves', 'create-leaves', 'approve-leaves', 'view-performance', 'create-performance', 'view-training', 'create-training', 'view-recruitment', 'create-recruitment', 'view-assets', 'view-events', 'create-events', 'view-policies', 'create-policies'],
            ],
            [
                'name' => 'legal_officer',
                'label' => 'Legal Officer',
                'permissions' => ['view-dashboard', 'view-crm-contracts', 'create-crm-contracts', 'view-crm-contacts', 'view-reports', 'view-projects'],
            ],
            [
                'name' => 'receptionist',
                'label' => 'Receptionist',
                'permissions' => ['view-dashboard', 'view-crm-leads', 'create-crm-leads', 'view-crm-contacts', 'create-crm-contacts', 'view-helpdesk-tickets', 'create-helpdesk-tickets'],
            ],
            [
                'name' => 'logistics_officer',
                'label' => 'Logistics Officer',
                'permissions' => ['view-dashboard', 'view-warehouses', 'view-products', 'view-stock-movements', 'create-stock-movements', 'view-purchase-invoices', 'view-suppliers', 'view-transfers'],
            ],
            [
                'name' => 'technical_manager',
                'label' => 'Technical Manager',
                'permissions' => ['view-dashboard', 'view-projects', 'create-projects', 'edit-projects', 'view-timesheets', 'view-bugs', 'create-bugs', 'view-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-employees'],
            ],
            [
                'name' => 'technician',
                'label' => 'Technician',
                'permissions' => ['view-dashboard', 'view-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-projects', 'view-timesheets', 'create-timesheets', 'view-bugs', 'create-bugs'],
            ],
            [
                'name' => 'ict_officer',
                'label' => 'ICT Officer',
                'permissions' => ['view-dashboard', 'view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-projects', 'view-bugs', 'create-bugs', 'view-assets', 'view-employees'],
            ],
            [
                'name' => 'project_manager',
                'label' => 'Project Manager',
                'permissions' => ['view-dashboard', 'view-projects', 'create-projects', 'edit-projects', 'view-timesheets', 'create-timesheets', 'view-bugs', 'create-bugs', 'view-employees', 'view-crm-deals', 'view-reports'],
            ],
            [
                'name' => 'operations_manager',
                'label' => 'Operations Manager',
                'permissions' => ['view-dashboard', 'view-warehouses', 'view-products', 'view-stock-movements', 'view-sales-invoices', 'view-purchase-invoices', 'view-suppliers', 'view-transfers', 'view-projects', 'view-employees', 'view-attendance', 'view-reports'],
            ],
            [
                'name' => 'call_center_agent',
                'label' => 'Call Center Agent',
                'permissions' => ['view-dashboard', 'view-crm-leads', 'create-crm-leads', 'edit-crm-leads', 'view-crm-contacts', 'create-crm-contacts', 'view-helpdesk-tickets', 'create-helpdesk-tickets'],
            ],
            [
                'name' => 'cashier',
                'label' => 'Cashier',
                'permissions' => ['view-dashboard', 'view-pos', 'create-pos', 'view-sales-invoices', 'view-products', 'view-revenues', 'create-revenues'],
            ],
            [
                'name' => 'supervisor',
                'label' => 'Supervisor',
                'permissions' => ['view-dashboard', 'view-employees', 'view-attendance', 'create-attendance', 'view-leaves', 'view-projects', 'view-timesheets', 'view-sales-invoices', 'view-pos', 'view-products', 'view-warehouses', 'view-reports'],
            ],
            [
                'name' => 'ict_engineer',
                'label' => 'ICT Engineer',
                'permissions' => ['view-dashboard', 'view-helpdesk-tickets', 'edit-helpdesk-tickets', 'view-projects', 'view-bugs', 'create-bugs', 'view-assets', 'view-employees', 'view-settings'],
            ],
        ];

        // Get all permission IDs
        $permMap = DB::table('permissions')->pluck('id', 'name');

        foreach ($roles as $roleData) {
            // Create or update role
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
                // Clear existing permissions
                DB::table('role_permission')->where('role_id', $roleId)->delete();
            }

            // Assign permissions
            foreach ($roleData['permissions'] as $permName) {
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
