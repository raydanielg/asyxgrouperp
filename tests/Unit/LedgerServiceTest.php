<?php

namespace Tests\Unit;

use App\Services\LedgerService;
use App\Services\InventoryService;
use App\Services\PayrollService;
use App\Services\DocumentNumberService;
use App\Models\PayrollStatutoryRule;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LedgerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_entry_creates_balanced_journal_entry(): void
    {
        $ledger = app(LedgerService::class);

        $ar = \App\Models\ChartOfAccount::create([
            'code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset',
            'subtype' => 'accounts_receivable', 'normal_balance' => 'debit',
        ]);
        $rev = \App\Models\ChartOfAccount::create([
            'code' => '4010', 'name' => 'Sales Revenue', 'type' => 'revenue',
            'normal_balance' => 'credit',
        ]);

        $entry = $ledger->postEntry([
            'entry_date' => now()->toDateString(),
            'description' => 'Test entry',
            'source_type' => 'manual',
        ], [
            ['chart_of_account_id' => $ar->id, 'debit' => 100, 'credit' => 0],
            ['chart_of_account_id' => $rev->id, 'debit' => 0, 'credit' => 100],
        ]);

        $this->assertDatabaseHas('journal_entries', ['id' => $entry->id, 'status' => 'posted']);
        $this->assertEquals(100, $entry->totalDebit());
        $this->assertEquals(100, $entry->totalCredit());
    }

    public function test_unbalanced_entry_throws_exception(): void
    {
        $ledger = app(LedgerService::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('not balanced');

        $ledger->postEntry([
            'entry_date' => now()->toDateString(),
        ], [
            ['chart_of_account_id' => 1, 'debit' => 100, 'credit' => 0],
            ['chart_of_account_id' => 2, 'debit' => 0, 'credit' => 50],
        ]);
    }

    public function test_period_locking_prevents_posting(): void
    {
        \App\Models\AccountingPeriod::create([
            'name' => 'Closed Period 2025',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'status' => 'closed',
        ]);

        $ledger = app(LedgerService::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('closed');

        $ledger->postEntry([
            'entry_date' => '2025-06-15',
        ], [
            ['chart_of_account_id' => 1, 'debit' => 50, 'credit' => 0],
            ['chart_of_account_id' => 2, 'debit' => 0, 'credit' => 50],
        ]);
    }
}
