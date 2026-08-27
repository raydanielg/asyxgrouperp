<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use App\Policies\EmployeePolicy;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_view_own_company_records(): void
    {
        $companyA = Company::create(['name' => 'Company A', 'legal_name' => 'Company A Ltd', 'short_code' => 'CMA', 'is_active' => true]);
        $companyB = Company::create(['name' => 'Company B', 'legal_name' => 'Company B Ltd', 'short_code' => 'CMB', 'is_active' => true]);

        $userA = User::create([
            'name' => 'User A',
            'email' => 'usera@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'company_id' => $companyA->id,
        ]);

        $employeeA = Employee::create([
            'company_id' => $companyA->id,
            'employee_id' => 'EMP-A-001',
            'first_name' => 'Emp A',
            'last_name' => 'Test',
            'email' => 'empa@test.com',
        ]);

        $employeeB = Employee::create([
            'company_id' => $companyB->id,
            'employee_id' => 'EMP-B-001',
            'first_name' => 'Emp B',
            'last_name' => 'Test',
            'email' => 'empb@test.com',
        ]);

        $this->actingAs($userA);

        $policy = new EmployeePolicy();
        $this->assertTrue($policy->view($userA, $employeeA));
        $this->assertFalse($policy->view($userA, $employeeB));
    }

    public function test_admin_can_view_all_company_records(): void
    {
        $companyA = Company::create(['name' => 'Company A', 'legal_name' => 'Company A Ltd', 'short_code' => 'CMA', 'is_active' => true]);
        $companyB = Company::create(['name' => 'Company B', 'legal_name' => 'Company B Ltd', 'short_code' => 'CMB', 'is_active' => true]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_id' => null,
        ]);

        $employeeA = Employee::create([
            'company_id' => $companyA->id,
            'employee_id' => 'EMP-A-002',
            'first_name' => 'Emp A',
            'last_name' => 'Test',
            'email' => 'empa2@test.com',
        ]);

        $employeeB = Employee::create([
            'company_id' => $companyB->id,
            'employee_id' => 'EMP-B-002',
            'first_name' => 'Emp B',
            'last_name' => 'Test',
            'email' => 'empb2@test.com',
        ]);

        $policy = new EmployeePolicy();
        $this->assertTrue($policy->view($admin, $employeeA));
        $this->assertTrue($policy->view($admin, $employeeB));
    }

    public function test_employee_cannot_be_deleted(): void
    {
        $company = Company::create(['name' => 'Test Co', 'legal_name' => 'Test Co Ltd', 'short_code' => 'TST', 'is_active' => true]);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_id' => null,
        ]);

        $employee = Employee::create([
            'company_id' => $company->id,
            'employee_id' => 'EMP-T-001',
            'first_name' => 'Test',
            'last_name' => 'Employee',
            'email' => 'emptest@test.com',
        ]);

        $policy = new EmployeePolicy();
        $this->assertFalse($policy->delete($admin, $employee));
    }
}
