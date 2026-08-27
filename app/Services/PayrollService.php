<?php

namespace App\Services;

use App\Models\PayrollStatutoryRule;
use App\Models\Payroll;

class PayrollService
{
    public function calculateDeductions(float $grossAmount, ?int $companyId = null): array
    {
        $rules = PayrollStatutoryRule::active()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->get();

        $deductions = [];
        $totalEmployeeDeduction = 0;
        $totalEmployerContribution = 0;

        foreach ($rules as $rule) {
            $employeeAmount = $rule->calculateEmployeeDeduction($grossAmount);
            $employerAmount = $rule->calculateEmployerContribution($grossAmount);

            $deductions[] = [
                'name' => $rule->name,
                'type' => $rule->type,
                'employee_amount' => $employeeAmount,
                'employer_amount' => $employerAmount,
            ];

            $totalEmployeeDeduction += $employeeAmount;
            $totalEmployerContribution += $employerAmount;
        }

        return [
            'deductions' => $deductions,
            'total_employee_deduction' => round($totalEmployeeDeduction, 2),
            'total_employer_contribution' => round($totalEmployerContribution, 2),
            'net_pay' => round($grossAmount - $totalEmployeeDeduction, 2),
            'total_cost_to_employer' => round($grossAmount + $totalEmployerContribution, 2),
        ];
    }

    public function generatePayroll(float $grossAmount, ?int $companyId = null): array
    {
        $calc = $this->calculateDeductions($grossAmount, $companyId);

        return [
            'gross_pay' => round($grossAmount, 2),
            'deductions' => $calc['deductions'],
            'total_deductions' => $calc['total_employee_deduction'],
            'net_pay' => $calc['net_pay'],
            'employer_contribution' => $calc['total_employer_contribution'],
            'total_cost' => $calc['total_cost_to_employer'],
        ];
    }
}
