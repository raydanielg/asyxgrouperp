<?php

namespace Tests\Unit;

use App\Services\PayrollService;
use App\Models\PayrollStatutoryRule;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_deductions_with_statutory_rules(): void
    {
        PayrollStatutoryRule::create([
            'name' => 'PAYE',
            'type' => 'tax',
            'calculation_basis' => 'gross',
            'employee_rate' => 10,
            'employer_rate' => 0,
            'is_active' => true,
            'effective_from' => now()->subDay(),
        ]);

        PayrollStatutoryRule::create([
            'name' => 'NSSF',
            'type' => 'pension',
            'calculation_basis' => 'gross',
            'employee_rate' => 5,
            'employer_rate' => 5,
            'is_active' => true,
            'effective_from' => now()->subDay(),
        ]);

        $service = app(PayrollService::class);
        $result = $service->calculateDeductions(1000);

        $this->assertEquals(100, $result['deductions'][0]['employee_amount']);
        $this->assertEquals(50, $result['deductions'][1]['employee_amount']);
        $this->assertEquals(150, $result['total_employee_deduction']);
        $this->assertEquals(850, $result['net_pay']);
        $this->assertEquals(50, $result['total_employer_contribution']);
        $this->assertEquals(1050, $result['total_cost_to_employer']);
    }

    public function test_inactive_rules_are_not_applied(): void
    {
        PayrollStatutoryRule::create([
            'name' => 'Old Tax',
            'type' => 'tax',
            'calculation_basis' => 'gross',
            'employee_rate' => 20,
            'employer_rate' => 0,
            'is_active' => false,
            'effective_from' => now()->subDay(),
        ]);

        $service = app(PayrollService::class);
        $result = $service->calculateDeductions(1000);

        $this->assertEmpty($result['deductions']);
        $this->assertEquals(1000, $result['net_pay']);
    }

    public function test_max_amount_cap(): void
    {
        PayrollStatutoryRule::create([
            'name' => 'Capped Tax',
            'type' => 'tax',
            'calculation_basis' => 'gross',
            'employee_rate' => 10,
            'employer_rate' => 0,
            'maximum_amount' => 50,
            'is_active' => true,
            'effective_from' => now()->subDay(),
        ]);

        $service = app(PayrollService::class);
        $result = $service->calculateDeductions(1000);

        $this->assertEquals(50, $result['deductions'][0]['employee_amount']);
    }
}
