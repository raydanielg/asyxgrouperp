<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Default chart of accounts. Codes are grouped by first digit:
     * 1xxx Assets, 2xxx Liabilities, 3xxx Equity, 4xxx Revenue, 5xxx Expenses.
     * Safe to re-run: only creates accounts that don't already exist (by code).
     */
    public function run(): void
    {
        $accounts = [
            // code, name, type, subtype, normal_balance, parent_code
            ['1000', 'Assets', 'asset', null, 'debit', null],
            ['1010', 'Bank Accounts', 'asset', 'bank', 'debit', '1000'],
            ['1020', 'Petty Cash', 'asset', 'petty_cash', 'debit', '1000'],
            ['1030', 'Project Cash Accounts', 'asset', 'project_cash', 'debit', '1000'],
            ['1100', 'Accounts Receivable', 'asset', 'accounts_receivable', 'debit', '1000'],
            ['1500', 'Fixed Assets', 'asset', 'fixed_asset', 'debit', '1000'],

            ['2000', 'Liabilities', 'liability', null, 'credit', null],
            ['2010', 'Accounts Payable', 'liability', 'accounts_payable', 'credit', '2000'],
            ['2020', 'Accrued Expenses', 'liability', 'accrued', 'credit', '2000'],

            ['3000', 'Equity', 'equity', null, 'credit', null],
            ['3010', "Owner's Capital", 'equity', 'capital', 'credit', '3000'],
            ['3900', 'Cash Funding / Owner Contributions', 'equity', 'funding', 'credit', '3000'],
            ['3990', 'Retained Earnings', 'equity', 'retained_earnings', 'credit', '3000'],

            ['4000', 'Revenue', 'revenue', null, 'credit', null],
            ['4010', 'Project Revenue / Client Receipts', 'revenue', 'project_revenue', 'credit', '4000'],
            ['4020', 'Sales Revenue', 'revenue', 'sales', 'credit', '4000'],
            ['4090', 'Other Income', 'revenue', 'other_income', 'credit', '4000'],

            ['5000', 'Expenses', 'expense', null, 'debit', null],
            ['5010', 'Cost of Sales', 'expense', 'cost_of_sales', 'debit', '5000'],
            ['5020', 'Project Expenses', 'expense', 'project_expense', 'debit', '5000'],
            ['5030', 'Petty Cash Expenses', 'expense', 'petty_cash_expense', 'debit', '5000'],
            ['5040', 'Office Expenses', 'expense', 'office_expense', 'debit', '5000'],
            ['5050', 'Payroll Expenses', 'expense', 'payroll_expense', 'debit', '5000'],
            ['5090', 'Other Expenses', 'expense', 'other_expense', 'debit', '5000'],
        ];

        $idsByCode = [];

        foreach ($accounts as [$code, $name, $type, $subtype, $normal, $parentCode]) {
            $account = ChartOfAccount::where('code', $code)->first();
            if (!$account) {
                $account = ChartOfAccount::create([
                    'code' => $code,
                    'name' => $name,
                    'type' => $type,
                    'subtype' => $subtype,
                    'normal_balance' => $normal,
                    'parent_id' => $parentCode ? ($idsByCode[$parentCode] ?? null) : null,
                    'is_system' => true,
                    'is_active' => true,
                ]);
            } elseif ($parentCode && !$account->parent_id && isset($idsByCode[$parentCode])) {
                $account->update(['parent_id' => $idsByCode[$parentCode]]);
            }
            $idsByCode[$code] = $account->id;
        }
    }
}
