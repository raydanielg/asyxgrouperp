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
        'erp_super_administrator' => ['IT', 'ERP Super Administrator'],
        'erp_administrator' => ['IT', 'ERP Administrator'],
        'ict_administrator' => ['IT', 'ICT Administrator'],
        'ict_engineer' => ['IT', 'ICT Engineer'],
        'ict_officer' => ['IT', 'ICT Officer'],
        'senior_systems_engineer' => ['IT', 'Senior Systems Engineer'],
        'systems_engineer' => ['IT', 'Systems Engineer'],
        'network_engineer' => ['IT', 'Network Engineer'],
        'software_engineer' => ['IT', 'Software Engineer'],
        'cybersecurity_engineer' => ['IT', 'Cybersecurity Engineer'],
        'support_engineer' => ['IT', 'Support Engineer'],
        'field_technician' => ['IT', 'Field Technician'],
        'technician' => ['IT', 'Technician'],
        'noc_engineer' => ['IT', 'NOC Engineer'],
        'service_desk_manager' => ['IT', 'Service Desk Manager'],
        'helpdesk_supervisor' => ['IT', 'Helpdesk Supervisor'],
        'helpdesk_officer' => ['IT', 'Helpdesk Officer'],
        'technical_manager' => ['IT', 'Technical Manager'],

        'managing_director' => ['Executive', 'Managing Director'],
        'general_manager' => ['Executive', 'General Manager'],
        'director' => ['Executive', 'Director'],
        'admin_manager' => ['Administration', 'Admin Manager'],
        'administrator' => ['Administration', 'Administrator'],

        'finance_manager' => ['Finance', 'Finance Manager'],
        'finance_officer' => ['Finance', 'Finance Officer'],
        'chief_accountant' => ['Finance', 'Chief Accountant'],
        'accountant' => ['Finance', 'Accountant'],
        'accounts_receivable_officer' => ['Finance', 'Accounts Receivable Officer'],
        'accounts_payable_officer' => ['Finance', 'Accounts Payable Officer'],
        'cashier' => ['Finance', 'Cashier'],
        'payroll_officer' => ['Finance', 'Payroll Officer'],
        'budget_officer' => ['Finance', 'Budget Officer'],
        'credit_controller' => ['Finance', 'Credit Controller'],
        'auditor' => ['Finance', 'Auditor'],

        'procurement_manager' => ['Procurement', 'Procurement Manager'],
        'procurement_officer' => ['Procurement', 'Procurement Officer'],
        'tender_officer' => ['Procurement', 'Tender Officer'],
        'logistics_officer' => ['Procurement', 'Logistics Officer'],
        'store_manager' => ['Procurement', 'Store Manager'],
        'storekeeper' => ['Procurement', 'Storekeeper'],
        'inventory_controller' => ['Procurement', 'Inventory Controller'],
        'asset_officer' => ['Procurement', 'Asset Officer'],

        'sales_manager' => ['Sales', 'Sales Manager'],
        'business_development_manager' => ['Sales', 'Business Development Manager'],
        'sales_executive' => ['Sales', 'Sales Executive'],
        'crm_officer' => ['Sales', 'CRM Officer'],
        'marketing_officer' => ['Sales', 'Marketing Officer'],
        'call_center_supervisor' => ['Sales', 'Call Center Supervisor'],
        'call_center_agent' => ['Sales', 'Call Center Agent'],

        'project_director' => ['Projects', 'Project Director'],
        'project_manager' => ['Projects', 'Project Manager'],
        'technical_projects_manager' => ['Projects', 'Technical Projects Manager'],
        'project_coordinator' => ['Projects', 'Project Coordinator'],
        'project_engineer' => ['Projects', 'Project Engineer'],
        'site_supervisor' => ['Projects', 'Site Supervisor'],
        'team_leader' => ['Projects', 'Team Leader'],
        'project_accountant' => ['Projects', 'Project Accountant'],

        'hr_manager' => ['Human Resources', 'HR Manager'],
        'hr_officer' => ['Human Resources', 'HR Officer'],
        'recruitment_officer' => ['Human Resources', 'Recruitment Officer'],
        'training_officer' => ['Human Resources', 'Training Officer'],
        'time_and_attendance_officer' => ['Human Resources', 'Time & Attendance Officer'],

        'operations_manager' => ['Operations', 'Operations Manager'],
        'operations_officer' => ['Operations', 'Operations Officer'],
        'fleet_manager' => ['Operations', 'Fleet Manager'],
        'supervisor' => ['Operations', 'Supervisor'],

        'employee_self_service' => ['General', 'Employee'],
        'manager_self_service' => ['General', 'Manager'],
        'receptionist' => ['Administration', 'Receptionist'],
        'legal_officer' => ['Administration', 'Legal Officer'],
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
                'company_id' => null,
                'created_by' => 1,
            ]);
        }
    }

    private function generateEmployeeId(int $counter): string
    {
        return 'ASYX-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }
}
