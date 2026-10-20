<?php

namespace Tests\Feature;

use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\OfficeExpense;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JournalBranchTest extends TestCase
{
    use RefreshDatabase;

    private function company(): Company
    {
        return Company::firstOrCreate(
            ['name' => 'Test Co'],
            ['legal_name' => 'Test Co Ltd', 'short_code' => 'TST', 'is_active' => true]
        );
    }

    private function makeUser(array $attrs = []): User
    {
        return User::create(array_merge([
            'name' => 'U' . uniqid(),
            'email' => 'u' . uniqid() . '@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'company_id' => $this->company()->id,
            'is_enable_login' => true,
        ], $attrs));
    }

    private function grantPerm(User $user, string $name): void
    {
        $perm = Permission::firstOrCreate(['name' => $name], ['label' => $name]);
        $user->directPermissions()->create(['permission_id' => $perm->id, 'granted' => true]);
        Cache::forget("user_perms_{$user->id}");
    }

    private function workflowFor(string $module, string $role = 'finance_manager'): ApprovalWorkflow
    {
        $wf = ApprovalWorkflow::create([
            'name' => ucfirst($module) . ' Flow', 'module' => $module,
            'is_active' => true, 'company_id' => $this->company()->id,
        ]);
        ApprovalStep::create(['workflow_id' => $wf->id, 'level' => 1, 'name' => 'Step', 'approver_type' => 'role', 'approver_role' => $role, 'order' => 0]);
        return $wf;
    }

    private function makeAccounts(): array
    {
        $mk = fn(string $name) => ChartOfAccount::withoutGlobalScopes()->create([
            'code' => 'ACC-' . uniqid(), 'name' => $name, 'type' => 'asset',
            'is_active' => true, 'company_id' => $this->company()->id,
        ]);
        return [$mk('Cash'), $mk('Expense')];
    }

    private function makeJournalEntry(User $creator, string $status = 'pending'): JournalEntry
    {
        [$a, $b] = $this->makeAccounts();
        $entry = JournalEntry::withoutGlobalScopes()->create([
            'entry_number' => 'JE-' . uniqid(), 'entry_date' => '2026-10-20',
            'status' => $status, 'created_by' => $creator->id,
            'company_id' => $this->company()->id,
        ]);
        JournalEntryLine::create(['journal_entry_id' => $entry->id, 'chart_of_account_id' => $a->id, 'debit' => 100, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $entry->id, 'chart_of_account_id' => $b->id, 'debit' => 0, 'credit' => 100]);
        return $entry;
    }

    // ═══ JOURNAL LIFECYCLE ═══

    public function test_pending_entry_cannot_be_posted(): void
    {
        $this->workflowFor('journal_entry');
        $creator = $this->makeUser();
        $poster = $this->makeUser();
        $this->grantPerm($poster, 'post-journal-entries');
        $entry = $this->makeJournalEntry($creator, 'pending');
        \App\Services\ApprovalService::submit('journal_entry', $entry, "Journal {$entry->entry_number}", 100, $creator);

        $this->actingAs($poster)->post("/admin/journal-entries/{$entry->id}/post")->assertStatus(403);
        $this->assertEquals('pending', $entry->fresh()->status);
    }

    public function test_approved_entry_posts_once_and_is_idempotent(): void
    {
        $this->workflowFor('journal_entry');
        $creator = $this->makeUser();
        $poster = $this->makeUser();
        $this->grantPerm($poster, 'post-journal-entries');
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $entry = $this->makeJournalEntry($creator, 'pending');

        $req = \App\Services\ApprovalService::submit('journal_entry', $entry, "Journal {$entry->entry_number}", 100, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($poster)->post("/admin/journal-entries/{$entry->id}/post")->assertRedirect();
        $this->assertEquals('posted', $entry->fresh()->status);
        $this->assertNotNull($entry->fresh()->posted_at);

        // Double-post blocked
        $this->actingAs($poster)->post("/admin/journal-entries/{$entry->id}/post")->assertStatus(403);
        $this->assertEquals(2, $entry->fresh()->lines()->count()); // no duplicate lines
    }

    public function test_rejected_entry_cannot_be_posted(): void
    {
        $this->workflowFor('journal_entry');
        $creator = $this->makeUser();
        $poster = $this->makeUser();
        $this->grantPerm($poster, 'post-journal-entries');
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $entry = $this->makeJournalEntry($creator, 'pending');

        $req = \App\Services\ApprovalService::submit('journal_entry', $entry, "Journal {$entry->entry_number}", 100, $creator);
        \App\Services\ApprovalService::reject($req, $fm, 'Incorrect');

        $this->actingAs($poster)->post("/admin/journal-entries/{$entry->id}/post")->assertStatus(403);
        $this->assertEquals('rejected', $entry->fresh()->status);
    }

    public function test_post_requires_permission(): void
    {
        $creator = $this->makeUser();
        $noPerm = $this->makeUser();
        $entry = $this->makeJournalEntry($creator, 'approved');
        $this->actingAs($noPerm)->post("/admin/journal-entries/{$entry->id}/post")->assertStatus(403);
    }

    public function test_reversal_creates_linked_swapped_entry(): void
    {
        $creator = $this->makeUser();
        $reverser = $this->makeUser();
        $this->grantPerm($reverser, 'reverse-journal-entries');
        $entry = $this->makeJournalEntry($creator, 'posted');

        $this->actingAs($reverser)->post("/admin/journal-entries/{$entry->id}/reverse")->assertRedirect();

        $reversal = JournalEntry::where('reversal_of_id', $entry->id)->first();
        $this->assertNotNull($reversal);
        $this->assertEquals('posted', $reversal->status);
        // Debits/credits swapped
        $lines = $reversal->lines;
        $this->assertEquals(0, $lines->first()->debit); // was debit=100
        $this->assertEquals(100, $lines->first()->credit);
        $this->assertEquals(100, $reversal->totalDebit());
        $this->assertEquals(100, $reversal->totalCredit());
        // Original preserved
        $this->assertEquals('posted', $entry->fresh()->status);

        // Cannot reverse twice
        $this->actingAs($reverser)->post("/admin/journal-entries/{$entry->id}/reverse")->assertStatus(403);
    }

    public function test_unbalanced_entry_rejected_at_service(): void
    {
        $this->expectException(\RuntimeException::class);
        app(\App\Services\LedgerService::class)->postEntry(
            ['entry_date' => '2026-10-20', 'created_by' => 1],
            [['chart_of_account_id' => 1, 'debit' => 100.50], ['chart_of_account_id' => 2, 'credit' => 100.49]]
        );
    }

    // ═══ BRANCH ISOLATION ═══

    public function test_branch_restricted_user_cannot_view_other_branch_expense(): void
    {
        $bA = Branch::create(['company_id' => $this->company()->id, 'name' => 'A']);
        $bB = Branch::create(['company_id' => $this->company()->id, 'name' => 'B']);

        $restricted = $this->makeUser(['role' => 'user']);
        $restricted->branches()->sync([$bA->id]);

        $expA = OfficeExpense::withoutGlobalScopes()->create([
            'expense_number' => 'E-A', 'description' => 'a', 'amount' => 100, 'expense_date' => '2026-10-20',
            'status' => 'pending', 'company_id' => $this->company()->id, 'branch_id' => $bA->id, 'requested_by' => $restricted->id,
        ]);
        $expB = OfficeExpense::withoutGlobalScopes()->create([
            'expense_number' => 'E-B', 'description' => 'b', 'amount' => 100, 'expense_date' => '2026-10-20',
            'status' => 'pending', 'company_id' => $this->company()->id, 'branch_id' => $bB->id, 'requested_by' => $restricted->id,
        ]);

        $this->actingAs($restricted);
        $names = OfficeExpense::pluck('expense_number')->all();
        $this->assertContains('E-A', $names);
        $this->assertNotContains('E-B', $names);
        // IDOR — direct model fetch respects the scope
        $this->assertNull(OfficeExpense::find($expB->id));
    }

    public function test_foreign_branch_id_rejected_on_store(): void
    {
        $bB = Branch::create(['company_id' => 999, 'name' => 'Foreign', 'is_active' => true]);
        $user = $this->makeUser();

        $this->actingAs($user)->post('/admin/office-expenses', [
            'description' => 'x', 'amount' => 500000, 'expense_date' => '2026-10-20',
            'branch_id' => $bB->id,
        ])->assertSessionHasErrors('branch_id');
    }

    // ═══ NOTIFICATIONS ═══

    public function test_approval_submit_notifies_designated_approver(): void
    {
        $this->workflowFor('office_expense');
        $requester = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $other = $this->makeUser(['role' => 'hr_manager']);

        $expense = OfficeExpense::create([
            'expense_number' => 'E-N', 'description' => 'x', 'amount' => 500000, 'expense_date' => '2026-10-20',
            'status' => 'pending', 'company_id' => $this->company()->id, 'requested_by' => $requester->id, 'created_by' => $requester->id,
        ]);
        \App\Services\ApprovalService::submit('office_expense', $expense, 'Office Expense E-N', 500000, $requester);

        // Only the designated role gets notified
        $this->assertDatabaseHas('user_notifications', ['user_id' => $fm->id, 'type' => 'approval_submitted']);
        $this->assertDatabaseMissing('user_notifications', ['user_id' => $other->id]);
    }

    public function test_decision_notifies_requester_without_duplicates(): void
    {
        $this->workflowFor('office_expense');
        $requester = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $expense = OfficeExpense::create([
            'expense_number' => 'E-N2', 'description' => 'x', 'amount' => 500000, 'expense_date' => '2026-10-20',
            'status' => 'pending', 'company_id' => $this->company()->id, 'requested_by' => $requester->id, 'created_by' => $requester->id,
        ]);
        $req = \App\Services\ApprovalService::submit('office_expense', $expense, 'Office Expense E-N2', 500000, $requester);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->assertEquals(1, UserNotification::where('user_id', $requester->id)->where('type', 'approval_step_decided')->count());
    }
}
