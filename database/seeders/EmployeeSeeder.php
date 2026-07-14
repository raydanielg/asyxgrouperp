<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Map roles to realistic departments and designations.
     */
    private array $roleMeta = [
        'superadmin' => ['Executive', 'Super Admin'],
        'admin' => ['Administration', 'System Admin'],
        'director' => ['Executive', 'Director'],
        'accountant' => ['Finance', 'Accountant'],
        'finance_manager' => ['Finance', 'Finance Manager'],
        'procurement_manager' => ['Procurement', 'Procurement Manager'],
        'sales_manager' => ['Sales', 'Sales Manager'],
        'project_manager' => ['Projects', 'Project Manager'],
        'technical_manager' => ['IT', 'Technical Manager'],
        'operations_manager' => ['Operations', 'Operations Manager'],
        'hr_manager' => ['Human Resources', 'HR Manager'],
    ];

    public function run(): void
    {
        $now = now();
        $counter = 1;

        // Find the highest existing ASYX-XXXX number to avoid duplicate employee IDs
        $existingIds = Employee::where('employee_id', 'like', 'ASYX-%')->pluck('employee_id');
        foreach ($existingIds as $id) {
            $number = (int) str_replace('ASYX-', '', $id);
            if ($number >= $counter) {
                $counter = $number + 1;
            }
        }

        // Ensure admin and superadmin have employee records
        $systemUsers = [
            'admin@djanproject.com' => ['Executive', 'System Administrator'],
            'superadmin@asyxgroup.co.tz' => ['Executive', 'ERP Super Administrator'],
        ];

        foreach ($systemUsers as $email => $meta) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                continue;
            }

            if (Employee::where('user_id', $user->id)->exists() || Employee::where('email', $email)->exists()) {
                continue;
            }

            $employeeId = $this->generateEmployeeId($counter++);

            Employee::create([
                'user_id' => $user->id,
                'employee_id' => $employeeId,
                'first_name' => $user->first_name ?? $user->name,
                'last_name' => $user->last_name ?? '',
                'email' => $user->email,
                'phone' => $user->phone,
                'department' => $meta[0],
                'designation' => $meta[1],
                'employment_type' => 'Full-time',
                'status' => 'active',
                'joining_date' => $now->copy()->subYears(2),
                'salary' => 0,
                'company_id' => $user->company_id,
                'created_by' => $user->id,
            ]);
        }

        // Create an employee record for every role-based user
        $users = User::whereNotIn('email', array_keys($systemUsers))
            ->get();

        foreach ($users as $user) {
            if (Employee::where('user_id', $user->id)->exists() || Employee::where('email', $user->email)->exists()) {
                continue;
            }

            $meta = $this->roleMeta[$user->role] ?? ['General', 'Staff'];
            $employeeId = $this->generateEmployeeId($counter++);

            Employee::create([
                'user_id' => $user->id,
                'employee_id' => $employeeId,
                'first_name' => $user->first_name ?? $user->name,
                'last_name' => $user->last_name ?? 'User',
                'email' => $user->email,
                'phone' => $user->phone,
                'department' => $meta[0],
                'designation' => $meta[1],
                'employment_type' => 'Full-time',
                'status' => 'active',
                'joining_date' => $now->copy()->subMonths(rand(1, 36)),
                'salary' => rand(500000, 3500000),
                'company_id' => $user->company_id,
                'created_by' => 1,
            ]);
        }

        $company = \App\Models\Company::where('short_code', 'ASYX')->first();
        $companyId = $company?->id;

        // Add a few extra employees without user accounts so HR can see the difference
        $extraEmployees = [
            ['John', 'Doe', 'john.doe@asyxgroup.co.tz', 'Human Resources', 'HR Assistant'],
            ['Jane', 'Smith', 'jane.smith@asyxgroup.co.tz', 'Finance', 'Junior Accountant'],
            ['Michael', 'Johnson', 'michael.johnson@asyxgroup.co.tz', 'Operations', 'Driver'],
            ['Emily', 'Williams', 'emily.williams@asyxgroup.co.tz', 'Sales', 'Sales Coordinator'],
            ['David', 'Brown', 'david.brown@asyxgroup.co.tz', 'IT', 'IT Intern'],
        ];

        foreach ($extraEmployees as $idx => $data) {
            $email = $data[2];
            if (Employee::where('email', $email)->exists()) {
                continue;
            }

            $employeeId = $this->generateEmployeeId($counter++);
            Employee::create([
                'user_id' => null,
                'employee_id' => $employeeId,
                'first_name' => $data[0],
                'last_name' => $data[1],
                'email' => $email,
                'phone' => '+25570000' . str_pad($counter + 200, 4, '0', STR_PAD_LEFT),
                'department' => $data[3],
                'designation' => $data[4],
                'employment_type' => 'Full-time',
                'status' => 'active',
                'joining_date' => $now->copy()->subMonths(rand(1, 24)),
                'salary' => rand(400000, 2500000),
                'company_id' => $companyId,
                'created_by' => 1,
            ]);
        }
    }

    private function generateEmployeeId(int $counter): string
    {
        return 'ASYX-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }
}
