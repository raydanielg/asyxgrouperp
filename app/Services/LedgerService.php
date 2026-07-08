<?php

namespace App\Services;

use App\Models\CashAccount;
use App\Models\CashAccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class LedgerService
{
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
