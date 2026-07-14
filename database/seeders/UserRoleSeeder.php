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

        // Consolidated 11-role structure. superadmin/admin are seeded
        // separately below with fixed credentials.
        $allRoles = [
            'director',
            'accountant', 'finance_manager',
            'procurement_manager',
            'sales_manager',
            'project_manager',
            'technical_manager',
            'operations_manager',
            'hr_manager',
        ];

        $company = \App\Models\Company::where('short_code', 'ASYX')->first();
        $companyId = $company?->id;
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
            ];
        }

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

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
        $admin = User::where('email', 'admin@djanproject.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@djanproject.com',
                'phone' => '+255700000000',
                'company_id' => $companyId,
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => $now,
            ]);
        } elseif ($companyId && !$admin->company_id) {
            $admin->update(['company_id' => $companyId]);
        }

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            DB::table('role_user')->updateOrInsert(
                ['user_id' => $admin->id, 'role_id' => $adminRole->id],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        // Ensure superadmin user exists
        $superadmin = User::where('email', 'superadmin@djanproject.com')->first();
        if (!$superadmin) {
            $superadmin = User::create([
                'name' => 'Super Administrator',
                'first_name' => 'Super',
                'last_name' => 'Administrator',
                'email' => 'superadmin@djanproject.com',
                'phone' => '+255700000001',
                'company_id' => $companyId,
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'email_verified_at' => $now,
            ]);
        } elseif ($companyId && !$superadmin->company_id) {
            $superadmin->update(['company_id' => $companyId]);
        }

        $superadminRole = Role::where('name', 'superadmin')->first();
        if ($superadminRole) {
            DB::table('role_user')->updateOrInsert(
                ['user_id' => $superadmin->id, 'role_id' => $superadminRole->id],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }
    }
}
