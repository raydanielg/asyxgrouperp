<?php

namespace Tests\Feature;

use App\Models\ApprovalRequest;
use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
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
            'role' => 'user',
            'company_id' => $this->company()->id,
            'is_enable_login' => true,
        ], $attrs));
    }

    /**
     * Workflow: level 1 requires 'finance_manager' role, level 2 'managing_director'.
     */
    private function workflow(): ApprovalWorkflow
    {
        $wf = ApprovalWorkflow::create([
            'name' => 'Expense Approval', 'module' => 'office_expense',
            'is_active' => true, 'company_id' => $this->company()->id,
        ]);
        ApprovalStep::create(['workflow_id' => $wf->id, 'level' => 1, 'name' => 'Finance', 'approver_type' => 'role', 'approver_role' => 'finance_manager', 'order' => 0]);
        ApprovalStep::create(['workflow_id' => $wf->id, 'level' => 2, 'name' => 'MD', 'approver_type' => 'role', 'approver_role' => 'managing_director', 'order' => 1]);
        return $wf;
    }

    private function request(ApprovalWorkflow $wf, User $requester): ApprovalRequest
    {
        return ApprovalRequest::create([
            'request_number' => 'REQ-' . uniqid(), 'workflow_id' => $wf->id,
            'module' => 'office_expense', 'module_id' => 1, 'module_label' => 'Expense',
            'amount' => 5000, 'status' => 'pending', 'current_level' => 1,
            'requested_by' => $requester->id, 'company_id' => $this->company()->id,
        ]);
    }

    public function test_self_approval_is_blocked_and_audited(): void
    {
        $wf = $this->workflow();
        $requester = $this->makeUser(['role' => 'finance_manager']);
        $req = $this->request($wf, $requester);

        $this->actingAs($requester)
            ->post("/admin/approval-requests/{$req->id}/approve")
            ->assertStatus(403);

        $this->assertDatabaseHas('approval_requests', ['id' => $req->id, 'status' => 'pending']);
        $this->assertDatabaseHas('audit_logs', ['module' => 'approvals', 'module_action' => 'self_approval_attempt']);
    }

    public function test_wrong_role_cannot_approve(): void
    {
        $wf = $this->workflow();
        $requester = $this->makeUser();
        $other = $this->makeUser(['role' => 'sales_manager']); // not finance_manager
        $req = $this->request($wf, $requester);

        $this->actingAs($other)
            ->post("/admin/approval-requests/{$req->id}/approve")
            ->assertStatus(403);

        $this->assertDatabaseHas('approval_requests', ['id' => $req->id, 'status' => 'pending', 'current_level' => 1]);
        $this->assertDatabaseHas('audit_logs', ['module' => 'approvals', 'module_action' => 'unauthorized_approval_attempt']);
    }

    public function test_designated_role_approves_and_level_advances(): void
    {
        $wf = $this->workflow();
        $requester = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $req = $this->request($wf, $requester);

        $this->actingAs($fm)->post("/admin/approval-requests/{$req->id}/approve")->assertRedirect();
        $this->assertDatabaseHas('approval_requests', ['id' => $req->id, 'status' => 'pending', 'current_level' => 2]);
        $this->assertDatabaseHas('audit_logs', ['module' => 'approvals', 'module_action' => 'approval_granted']);
    }

    public function test_final_approval_marks_request_approved(): void
    {
        $wf = $this->workflow();
        $requester = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $md = $this->makeUser(['role' => 'managing_director']);
        $req = $this->request($wf, $requester);

        $this->actingAs($fm)->post("/admin/approval-requests/{$req->id}/approve");
        $this->actingAs($md)->post("/admin/approval-requests/{$req->id}/approve");

        $this->assertDatabaseHas('approval_requests', ['id' => $req->id, 'status' => 'approved']);
        $this->assertNotNull($req->fresh()->approved_at);
    }

    public function test_duplicate_decision_at_same_level_blocked(): void
    {
        $wf = $this->workflow();
        $requester = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $req = $this->request($wf, $requester);

        $this->actingAs($fm)->post("/admin/approval-requests/{$req->id}/approve");
        $req->update(['current_level' => 1]); // tamper back — but track already exists
        $this->actingAs($fm)->post("/admin/approval-requests/{$req->id}/approve")->assertStatus(403);
    }

    public function test_non_pending_request_cannot_be_acted_on(): void
    {
        $wf = $this->workflow();
        $requester = $this->makeUser();
        $md = $this->makeUser(['role' => 'managing_director']);
        $req = $this->request($wf, $requester);
        $req->update(['status' => 'approved', 'approved_at' => now()]);

        $this->actingAs($md)->post("/admin/approval-requests/{$req->id}/approve")->assertStatus(403);
    }

    public function test_reject_requires_reason_and_marks_rejected(): void
    {
        $wf = $this->workflow();
        $requester = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $req = $this->request($wf, $requester);

        $this->actingAs($fm)->post("/admin/approval-requests/{$req->id}/reject", [])->assertSessionHasErrors('rejection_reason');
        $this->actingAs($fm)->post("/admin/approval-requests/{$req->id}/reject", ['rejection_reason' => 'No budget'])->assertRedirect();
        $this->assertDatabaseHas('approval_requests', ['id' => $req->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('audit_logs', ['module' => 'approvals', 'module_action' => 'approval_rejected']);
    }

    public function test_branch_scope_resolution(): void
    {
        $branchA = Branch::create(['company_id' => $this->company()->id, 'name' => 'HQ', 'code' => 'HQ1']);
        $branchB = Branch::create(['company_id' => $this->company()->id, 'name' => 'Mwanza', 'code' => 'MWZ']);

        $unassigned = $this->makeUser();
        $this->assertNull($unassigned->accessibleBranchIds()); // company-wide
        $this->assertTrue($unassigned->canAccessBranch($branchA->id));

        $restricted = $this->makeUser();
        $restricted->branches()->sync([$branchA->id]);
        $this->assertTrue($restricted->canAccessBranch($branchA->id));
        $this->assertFalse($restricted->canAccessBranch($branchB->id));
        $this->assertFalse($restricted->canAccessBranch(9999));
    }

    public function test_tenant_admin_cannot_move_user_to_foreign_company(): void
    {
        $other = Company::create(['name' => 'Other Co', 'legal_name' => 'Other Ltd', 'short_code' => 'OTH', 'is_active' => true]);
        $admin = $this->makeUser(['role' => 'admin']);
        $target = $this->makeUser();

        $this->actingAs($admin)->patch("/admin/users/{$target->id}", [
            'name' => 'X', 'email' => $target->email, 'role' => 'user', 'company_id' => $other->id,
        ])->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $target->id, 'company_id' => $this->company()->id]);
    }

    public function test_branch_assignment_validates_company(): void
    {
        $other = Company::create(['name' => 'Other Co', 'legal_name' => 'Other Ltd', 'short_code' => 'OTH', 'is_active' => true]);
        $foreign = Branch::create(['company_id' => $other->id, 'name' => 'Foreign']);
        $admin = $this->makeUser(['role' => 'admin']);
        $target = $this->makeUser();

        $this->actingAs($admin)->patch("/admin/users/{$target->id}", [
            'name' => 'X', 'email' => $target->email, 'role' => 'user',
            'company_id' => $this->company()->id,
            'assigned_branches' => [$foreign->id],
        ])->assertStatus(403);

        $this->assertEmpty($target->fresh()->branches);
    }
}
