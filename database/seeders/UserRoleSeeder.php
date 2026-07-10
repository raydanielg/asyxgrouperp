<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $allRoles = [
            'erp_super_administrator', 'erp_administrator', 'ict_administrator',
            'managing_director', 'general_manager', 'technical_manager', 'operations_manager',
            'finance_manager', 'chief_accountant', 'accountant', 'accounts_receivable_officer', 'accounts_payable_officer', 'cashier', 'payroll_officer', 'budget_officer', 'credit_controller',
            'finance_director', 'tax_officer', 'treasury_officer', 'cost_accountant', 'collections_officer',
            'procurement_manager', 'procurement_officer', 'tender_officer',
            'store_manager', 'storekeeper', 'inventory_controller', 'asset_officer',
            'sales_manager', 'business_development_manager', 'sales_executive', 'crm_officer', 'marketing_officer',
            'project_director', 'project_manager', 'technical_projects_manager', 'project_coordinator', 'project_engineer', 'site_supervisor', 'team_leader', 'project_accountant',
            'senior_systems_engineer', 'systems_engineer', 'network_engineer', 'software_engineer', 'cybersecurity_engineer', 'support_engineer', 'field_technician', 'noc_engineer',
            'service_desk_manager', 'helpdesk_supervisor', 'helpdesk_officer', 'call_center_supervisor', 'call_center_agent',
            'hr_manager', 'hr_officer', 'recruitment_officer', 'training_officer', 'time_and_attendance_officer',
            'operations_officer', 'fleet_manager', 'logistics_officer',
            'employee_self_service', 'manager_self_service',
            'finance_officer',
            'receptionist', 'legal_officer', 'admin_manager', 'auditor', 'ict_officer', 'ict_engineer', 'supervisor', 'director', 'administrator', 'technician',
        ];

        $companies = \App\Models\Company::where('is_active', true)->get();
        $users = [];
        foreach ($allRoles as $index => $roleName) {
            $label = ucwords(str_replace('_', ' ', $roleName));
            $email = str_replace('_', '.', $roleName) . '@djanproject.com';
            $users[] = [
                'name' => $label,
                'first_name' => $label,
                'last_name' => 'User',
                'email' => $email,
                'phone' => '+25570000' . str_pad($index + 100, 4, '0', STR_PAD_LEFT),
                'password' => 'password123',
                'role' => $roleName,
                'company_index' => $index % max(1, $companies->count()),
            ];
        }

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            $companyIndex = $userData['company_index'] ?? 0;
            unset($userData['role'], $userData['company_index']);

            $company = $companies->get($companyIndex);
            $companyId = $company?->id;

            $user = User::where('email', $userData['email'])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $userData['name'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'email' => $userData['email'],
                    'phone' => $userData['phone'],
                    'company_id' => $companyId,
                    'password' => Hash::make($userData['password']),
                    'role' => $roleName,
                    'email_verified_at' => $now,
                ]);
            } elseif ($companyId && !$user->company_id) {
                $user->update(['company_id' => $companyId]);
            }

            // Attach role via role_user pivot
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                DB::table('role_user')->updateOrInsert(
                    ['user_id' => $user->id, 'role_id' => $role->id],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }
        }

        // Ensure admin user exists
        $group = \App\Models\Company::where('is_group', true)->first();
        $admin = User::where('email', 'admin@djanproject.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@djanproject.com',
                'phone' => '+255700000000',
                'company_id' => $group?->id,
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => $now,
            ]);
        } elseif ($group && !$admin->company_id) {
            $admin->update(['company_id' => $group->id]);
        }

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            DB::table('role_user')->updateOrInsert(
                ['user_id' => $admin->id, 'role_id' => $adminRole->id],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }
    }
}
