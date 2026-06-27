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

        $users = [
            [
                'name' => 'Director User',
                'first_name' => 'John',
                'last_name' => 'Mwakyusa',
                'email' => 'director@djanproject.com',
                'phone' => '+255700000001',
                'password' => 'password123',
                'role' => 'director',
            ],
            [
                'name' => 'Admin Manager',
                'first_name' => 'Sarah',
                'last_name' => 'Kimaro',
                'email' => 'admin.manager@djanproject.com',
                'phone' => '+255700000002',
                'password' => 'password123',
                'role' => 'admin_manager',
            ],
            [
                'name' => 'Administrator',
                'first_name' => 'James',
                'last_name' => 'Mushi',
                'email' => 'administrator@djanproject.com',
                'phone' => '+255700000003',
                'password' => 'password123',
                'role' => 'administrator',
            ],
            [
                'name' => 'Finance Officer',
                'first_name' => 'Grace',
                'last_name' => 'Massawe',
                'email' => 'finance@djanproject.com',
                'phone' => '+255700000004',
                'password' => 'password123',
                'role' => 'finance_officer',
            ],
            [
                'name' => 'Auditor',
                'first_name' => 'Peter',
                'last_name' => 'Kessy',
                'email' => 'auditor@djanproject.com',
                'phone' => '+255700000005',
                'password' => 'password123',
                'role' => 'auditor',
            ],
            [
                'name' => 'HR Officer',
                'first_name' => 'Mary',
                'last_name' => 'Njau',
                'email' => 'hr@djanproject.com',
                'phone' => '+255700000006',
                'password' => 'password123',
                'role' => 'hr_officer',
            ],
            [
                'name' => 'Legal Officer',
                'first_name' => 'David',
                'last_name' => 'Lyimo',
                'email' => 'legal@djanproject.com',
                'phone' => '+255700000007',
                'password' => 'password123',
                'role' => 'legal_officer',
            ],
            [
                'name' => 'Receptionist',
                'first_name' => 'Lucy',
                'last_name' => 'Mlay',
                'email' => 'receptionist@djanproject.com',
                'phone' => '+255700000008',
                'password' => 'password123',
                'role' => 'receptionist',
            ],
            [
                'name' => 'Logistics Officer',
                'first_name' => 'Joseph',
                'last_name' => 'Mrema',
                'email' => 'logistics@djanproject.com',
                'phone' => '+255700000009',
                'password' => 'password123',
                'role' => 'logistics_officer',
            ],
            [
                'name' => 'Technical Manager',
                'first_name' => 'Eric',
                'last_name' => 'Shirima',
                'email' => 'tech.manager@djanproject.com',
                'phone' => '+255700000010',
                'password' => 'password123',
                'role' => 'technical_manager',
            ],
            [
                'name' => 'Technician',
                'first_name' => 'Frank',
                'last_name' => 'Mbwambo',
                'email' => 'technician@djanproject.com',
                'phone' => '+255700000011',
                'password' => 'password123',
                'role' => 'technician',
            ],
            [
                'name' => 'ICT Officer',
                'first_name' => 'Anna',
                'last_name' => 'Komba',
                'email' => 'ict.officer@djanproject.com',
                'phone' => '+255700000012',
                'password' => 'password123',
                'role' => 'ict_officer',
            ],
            [
                'name' => 'ICT Engineer',
                'first_name' => 'Michael',
                'last_name' => 'Mtei',
                'email' => 'ict.engineer@djanproject.com',
                'phone' => '+255700000013',
                'password' => 'password123',
                'role' => 'ict_engineer',
            ],
            [
                'name' => 'Project Manager',
                'first_name' => 'Esther',
                'last_name' => 'Mwakalukwa',
                'email' => 'project.manager@djanproject.com',
                'phone' => '+255700000014',
                'password' => 'password123',
                'role' => 'project_manager',
            ],
            [
                'name' => 'Operations Manager',
                'first_name' => 'Daniel',
                'last_name' => 'Malya',
                'email' => 'operations@djanproject.com',
                'phone' => '+255700000015',
                'password' => 'password123',
                'role' => 'operations_manager',
            ],
            [
                'name' => 'Call Center Agent',
                'first_name' => 'Ruth',
                'last_name' => 'Mwita',
                'email' => 'callcenter@djanproject.com',
                'phone' => '+255700000016',
                'password' => 'password123',
                'role' => 'call_center_agent',
            ],
            [
                'name' => 'Cashier',
                'first_name' => 'Joyce',
                'last_name' => 'Nkya',
                'email' => 'cashier@djanproject.com',
                'phone' => '+255700000017',
                'password' => 'password123',
                'role' => 'cashier',
            ],
            [
                'name' => 'Supervisor',
                'first_name' => 'Albert',
                'last_name' => 'Mwinuka',
                'email' => 'supervisor@djanproject.com',
                'phone' => '+255700000018',
                'password' => 'password123',
                'role' => 'supervisor',
            ],
        ];

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
                    'password' => Hash::make($userData['password']),
                    'role' => $roleName,
                    'email_verified_at' => $now,
                ]);
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
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => $now,
            ]);
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
