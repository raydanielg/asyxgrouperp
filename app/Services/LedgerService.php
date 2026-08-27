<?php

namespace App\Services;

use App\Models\CashAccount;
use App\Models\CashAccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\VendorInvoice;
use App\Models\VendorPayment;
use App\Models\Payroll;
use App\Models\AccountingPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class LedgerService
{
    protected function findAccount(string $code): ?ChartOfAccount
    {
        return ChartOfAccount::where('code', $code)->first();
    }

    protected function findAccountBySubtype(string $subtype): ?ChartOfAccount
    {
        return ChartOfAccount::where('subtype', $subtype)->first();
    }

    /**
     * Post a sales invoice to the ledger when it's posted.
     * Debit Accounts Receivable, credit Sales Revenue + Tax Payable.
     */
    public function postSalesInvoice(SalesInvoice $invoice): ?JournalEntry
    {
        $ar = $this->findAccount('1100');
        $revenue = $this->findAccount('4010');
        $tax = $this->findAccountBySubtype('tax_payable') ?? $this->findAccount('2020');

        if (!$ar || !$revenue) {
            return null;
        }

        $lines = [];
        $lines[] = ['chart_of_account_id' => $ar->id, 'debit' => (float) $invoice->total_amount, 'credit' => 0, 'description' => "Sales Invoice {$invoice->invoice_number}"];

        $lines[] = ['chart_of_account_id' => $revenue->id, 'debit' => 0, 'credit' => (float) $invoice->subtotal, 'description' => "Revenue - {$invoice->invoice_number}"];

        if ($invoice->tax_amount > 0 && $tax) {
            $lines[] = ['chart_of_account_id' => $tax->id, 'debit' => 0, 'credit' => (float) $invoice->tax_amount, 'description' => "Tax - {$invoice->invoice_number}"];
        }

        if ($invoice->discount_amount > 0) {
            $discount = $this->findAccountBySubtype('sales_discount') ?? $revenue;
            $lines[] = ['chart_of_account_id' => $discount->id, 'debit' => (float) $invoice->discount_amount, 'credit' => 0, 'description' => "Discount - {$invoice->invoice_number}"];
        }

        return $this->postEntry([
            'entry_date' => $invoice->invoice_date?->toDateString() ?? now()->toDateString(),
            'reference' => $invoice->invoice_number,
            'description' => "Sales Invoice {$invoice->invoice_number}",
            'source_type' => 'sales_invoice',
            'source_id' => $invoice->id,
            'created_by' => $invoice->creator_id ?? auth()->id(),
        ], $lines);
    }

    /**
     * Post a purchase invoice to the ledger when it's posted.
     * Debit Purchases/Expense + Tax, credit Accounts Payable.
     */
    public function postPurchaseInvoice(PurchaseInvoice $invoice): ?JournalEntry
    {
        $ap = $this->findAccount('2010');
        $expense = $this->findAccount('5010');
        $tax = $this->findAccountBySubtype('tax_payable') ?? $this->findAccount('2020');

        if (!$ap || !$expense) {
            return null;
        }

        $lines = [];
        $lines[] = ['chart_of_account_id' => $expense->id, 'debit' => (float) $invoice->subtotal, 'credit' => 0, 'description' => "Purchase Invoice {$invoice->invoice_number}"];

        if ($invoice->tax_amount > 0 && $tax) {
            $lines[] = ['chart_of_account_id' => $tax->id, 'debit' => (float) $invoice->tax_amount, 'credit' => 0, 'description' => "Input Tax - {$invoice->invoice_number}"];
        }

        $lines[] = ['chart_of_account_id' => $ap->id, 'debit' => 0, 'credit' => (float) $invoice->total_amount, 'description' => "Payable - {$invoice->invoice_number}"];

        return $this->postEntry([
            'entry_date' => $invoice->invoice_date?->toDateString() ?? now()->toDateString(),
            'reference' => $invoice->invoice_number,
            'description' => "Purchase Invoice {$invoice->invoice_number}",
            'source_type' => 'purchase_invoice',
            'source_id' => $invoice->id,
            'created_by' => $invoice->creator_id ?? auth()->id(),
        ], $lines);
    }

    /**
     * Post a vendor payment to the ledger.
     * Debit Accounts Payable, credit Cash/Bank.
     */
    public function postVendorPayment(VendorPayment $payment): ?JournalEntry
    {
        $ap = $this->findAccount('2010');
        $cash = $this->findAccount('1010');

        if (!$ap || !$cash) {
            return null;
        }

        return $this->postEntry([
            'entry_date' => $payment->payment_date ?? now()->toDateString(),
            'reference' => $payment->payment_number,
            'description' => "Vendor Payment {$payment->payment_number}",
            'source_type' => 'vendor_payment',
            'source_id' => $payment->id,
            'created_by' => $payment->created_by ?? auth()->id(),
        ], [
            ['chart_of_account_id' => $ap->id, 'debit' => (float) $payment->amount, 'credit' => 0, 'description' => "Payment to vendor"],
            ['chart_of_account_id' => $cash->id, 'debit' => 0, 'credit' => (float) $payment->amount, 'description' => "Cash/Bank out"],
        ]);
    }

    /**
     * Post payroll to the ledger.
     * Debit Salary Expense, credit Cash/Bank + Tax Payable + Other deductions.
     */
    public function postPayroll(Payroll $payroll): ?JournalEntry
    {
        $salaryExp = $this->findAccountBySubtype('salary_expense') ?? $this->findAccount('5040');
        $cash = $this->findAccount('1010');
        $tax = $this->findAccountBySubtype('tax_payable') ?? $this->findAccount('2020');

        if (!$salaryExp || !$cash) {
            return null;
        }

        $netPay = (float) ($payroll->net_pay ?? $payroll->net_salary ?? 0);
        $grossPay = (float) ($payroll->gross_pay ?? $payroll->gross_salary ?? 0);
        $deductions = $grossPay - $netPay;
        $payrollRef = $payroll->payroll_number ?? "PR-{$payroll->id}";

        $lines = [];
        $lines[] = ['chart_of_account_id' => $salaryExp->id, 'debit' => $grossPay, 'credit' => 0, 'description' => "Payroll {$payrollRef}"];

        $lines[] = ['chart_of_account_id' => $cash->id, 'debit' => 0, 'credit' => $netPay, 'description' => "Net pay - {$payrollRef}"];

        if ($deductions > 0 && $tax) {
            $lines[] = ['chart_of_account_id' => $tax->id, 'debit' => 0, 'credit' => $deductions, 'description' => "Deductions - {$payrollRef}"];
        }

        return $this->postEntry([
            'entry_date' => $payroll->payroll_date ?? $payroll->created_at?->toDateString() ?? now()->toDateString(),
            'reference' => $payrollRef,
            'description' => "Payroll {$payrollRef}",
            'source_type' => 'payroll',
            'source_id' => $payroll->id,
            'created_by' => $payroll->created_by ?? auth()->id(),
        ], $lines);
    }
    /**
     * Post a balanced double-entry journal entry.
     *
     * @param array $header ['entry_date' => 'Y-m-d', 'description' => string, 'reference' => ?string,
     *                        'source_type' => ?string, 'source_id' => ?int, 'project_id' => ?int, 'created_by' => ?int]
     * @param array $lines  [['chart_of_account_id' => int, 'debit' => float, 'credit' => float, 'description' => ?string, 'project_id' => ?int], ...]
     */
    public function postEntry(array $header, array $lines): JournalEntry
    {
        $totalDebit = round(array_sum(array_column($lines, 'debit')), 2);
        $totalCredit = round(array_sum(array_column($lines, 'credit')), 2);

        if (count($lines) < 2) {
            throw new RuntimeException('A journal entry needs at least two lines.');
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new RuntimeException("Journal entry is not balanced: debit {$totalDebit} != credit {$totalCredit}.");
        }

        $entryDate = $header['entry_date'] ?? now()->toDateString();

        $lockedPeriod = AccountingPeriod::where('status', 'closed')
            ->whereDate('start_date', '<=', $entryDate)
            ->whereDate('end_date', '>=', $entryDate)
            ->first();

        if ($lockedPeriod) {
            throw new RuntimeException("Accounting period '{$lockedPeriod->name}' is closed. No entries can be posted to this period.");
        }

        return DB::transaction(function () use ($header, $lines) {
            $entry = JournalEntry::create([
                'entry_number' => 'JE-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'entry_date' => $header['entry_date'] ?? now()->toDateString(),
                'reference' => $header['reference'] ?? null,
                'description' => $header['description'] ?? null,
                'source_type' => $header['source_type'] ?? 'manual',
                'source_id' => $header['source_id'] ?? null,
                'project_id' => $header['project_id'] ?? null,
                'status' => 'posted',
                'created_by' => $header['created_by'] ?? auth()->id(),
                'posted_at' => now(),
            ]);

            foreach ($lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $line['chart_of_account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                    'project_id' => $line['project_id'] ?? $header['project_id'] ?? null,
                ]);
            }

            return $entry;
        });
    }

    /**
     * Fund a cash account (petty cash or project card): money comes in.
     * Debits the cash account's own GL account, credits the funding/contra account.
     */
    public function fundCashAccount(
        CashAccount $account,
        float $amount,
        string $description,
        ?ChartOfAccount $contraAccount = null,
        ?string $sourceType = 'cash_topup',
        ?int $sourceId = null,
        ?int $createdBy = null,
        ?string $category = 'topup'
    ): CashAccountTransaction {
        if ($amount <= 0) {
            throw new RuntimeException('Top-up amount must be greater than zero.');
        }

        return DB::transaction(function () use ($account, $amount, $description, $contraAccount, $sourceType, $sourceId, $createdBy, $category) {
            $entry = null;

            if ($account->chart_of_account_id) {
                $contra = $contraAccount ?? ChartOfAccount::where('code', '3900')->first();
                if ($contra) {
                    $entry = $this->postEntry([
                        'entry_date' => now()->toDateString(),
                        'description' => $description,
                        'source_type' => $sourceType,
                        'source_id' => $sourceId,
                        'project_id' => $account->project_id,
                        'created_by' => $createdBy,
                    ], [
                        ['chart_of_account_id' => $account->chart_of_account_id, 'debit' => $amount, 'credit' => 0, 'description' => $description],
                        ['chart_of_account_id' => $contra->id, 'credit' => $amount, 'debit' => 0, 'description' => $description],
                    ]);
                }
            }

            $newBalance = round((float) $account->current_balance + $amount, 2);
            $account->update(['current_balance' => $newBalance]);

            return CashAccountTransaction::create([
                'company_id' => $account->company_id,
                'cash_account_id' => $account->id,
                'type' => 'credit',
                'category' => $category,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'journal_entry_id' => $entry?->id,
                'created_by' => $createdBy ?? auth()->id(),
                'transaction_date' => now()->toDateString(),
            ]);
        });
    }

    /**
     * Spend from a cash account (e.g. a project expense paid from the project's card).
     * Debits the expense account, credits the cash account's own GL account.
     */
    public function spendFromCashAccount(
        CashAccount $account,
        float $amount,
        string $description,
        ?ChartOfAccount $expenseAccount = null,
        ?string $sourceType = 'cash_expense',
        ?int $sourceId = null,
        ?int $createdBy = null,
        ?string $category = 'expense'
    ): CashAccountTransaction {
        if ($amount <= 0) {
            throw new RuntimeException('Amount must be greater than zero.');
        }

        if ($amount > (float) $account->current_balance) {
            throw new RuntimeException('Insufficient balance on this cash account.');
        }

        return DB::transaction(function () use ($account, $amount, $description, $expenseAccount, $sourceType, $sourceId, $createdBy, $category) {
            $entry = null;

            if ($account->chart_of_account_id) {
                $expenseAcc = $expenseAccount ?? ChartOfAccount::where('code', $account->type === 'project' ? '5020' : '5030')->first();
                if ($expenseAcc) {
                    $entry = $this->postEntry([
                        'entry_date' => now()->toDateString(),
                        'description' => $description,
                        'source_type' => $sourceType,
                        'source_id' => $sourceId,
                        'project_id' => $account->project_id,
                        'created_by' => $createdBy,
                    ], [
                        ['chart_of_account_id' => $expenseAcc->id, 'debit' => $amount, 'credit' => 0, 'description' => $description],
                        ['chart_of_account_id' => $account->chart_of_account_id, 'credit' => $amount, 'debit' => 0, 'description' => $description],
                    ]);
                }
            }

            $newBalance = round((float) $account->current_balance - $amount, 2);
            $account->update(['current_balance' => $newBalance]);

            return CashAccountTransaction::create([
                'company_id' => $account->company_id,
                'cash_account_id' => $account->id,
                'type' => 'debit',
                'category' => $category,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'journal_entry_id' => $entry?->id,
                'created_by' => $createdBy ?? auth()->id(),
                'transaction_date' => now()->toDateString(),
            ]);
        });
    }

    /**
     * Trial balance: net balance per account as of an optional date, split into debit/credit columns.
     */
    public function trialBalance(?string $asOf = null): array
    {
        $accounts = ChartOfAccount::active()->orderBy('code')->get();
        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $net = $account->balance(null, $asOf);
            if (abs($net) < 0.01) continue;

            $debit = $net > 0 && $account->normal_balance === 'debit' ? $net : 0;
            $credit = $net > 0 && $account->normal_balance === 'credit' ? $net : 0;
            // If balance flipped sign (unusual), place it on the opposite column.
            if ($net < 0) {
                if ($account->normal_balance === 'debit') {
                    $credit = abs($net);
                } else {
                    $debit = abs($net);
                }
            }

            $rows[] = [
                'account' => $account,
                'debit' => round($debit, 2),
                'credit' => round($credit, 2),
            ];
            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        return [
            'rows' => $rows,
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
        ];
    }

    /**
     * Profit & Loss between two dates. Optionally scoped to a project.
     */
    public function profitAndLoss(string $from, string $to, ?int $projectId = null): array
    {
        $lineQuery = function (string $type) use ($from, $to, $projectId) {
            $q = JournalEntryLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_entry_lines.chart_of_account_id')
                ->where('journal_entries.status', 'posted')
                ->where('chart_of_accounts.type', $type)
                ->whereDate('journal_entries.entry_date', '>=', $from)
                ->whereDate('journal_entries.entry_date', '<=', $to);
            if ($projectId) {
                $q->where('journal_entries.project_id', $projectId);
            }
            return $q;
        };

        $revenueRows = $lineQuery('revenue')
            ->selectRaw('chart_of_accounts.id, chart_of_accounts.code, chart_of_accounts.name, SUM(journal_entry_lines.credit) - SUM(journal_entry_lines.debit) as amount')
            ->groupBy('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name')
            ->get();

        $expenseRows = $lineQuery('expense')
            ->selectRaw('chart_of_accounts.id, chart_of_accounts.code, chart_of_accounts.name, SUM(journal_entry_lines.debit) - SUM(journal_entry_lines.credit) as amount')
            ->groupBy('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name')
            ->get();

        $totalRevenue = round((float) $revenueRows->sum('amount'), 2);
        $totalExpense = round((float) $expenseRows->sum('amount'), 2);

        return [
            'from' => $from,
            'to' => $to,
            'revenue' => $revenueRows,
            'expenses' => $expenseRows,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_profit' => round($totalRevenue - $totalExpense, 2),
        ];
    }
}
