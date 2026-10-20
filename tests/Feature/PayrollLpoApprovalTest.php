<?php

namespace Tests\Feature;

use App\Models\ApprovalRequest;
use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Lpo;
use App\Models\Payroll;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PayrollLpoApprovalTest extends TestCase
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

    private function workflowFor(string $module, string $role = 'finance_manager'): ApprovalWorkflow
    {
        $wf = ApprovalWorkflow::create([
            'name' => ucfirst($module) . ' Flow', 'module' => $module,
            'is_active' => true, 'company_id' => $this->company()->id,
        ]);
        ApprovalStep::create(['workflow_id' => $wf->id, 'level' => 1, 'name' => 'Step', 'approver_type' => 'role', 'approver_role' => $role, 'order' => 0]);
        return $wf;
    }

    private function makeEmployee(): Employee
    {
        return Employee::create([
            'employee_id' => 'EMP-' . uniqid(), 'first_name' => 'Jane', 'last_name' => 'Doe',
            'email' => 'e' . uniqid() . '@x.co', 'status' => 'active',
            'company_id' => $this->company()->id,
        ]);
    }

    private function makePayroll(User $creator, string $status = 'pending'): Payroll
    {
        return Payroll::create([
            'employee_id' => $this->makeEmployee()->id,
            'payroll_number' => 'PAY-' . uniqid(), 'month' => 'October', 'year' => 2026,
            'basic_salary' => 1000000, 'allowances' => 100000, 'deductions' => 50000,
            'net_salary' => 1050000, 'status' => $status,
            'created_by' => $creator->id, 'company_id' => $this->company()->id,
        ]);
    }

    // ═══ PAYROLL ═══

    public function test_payroll_store_never_accepts_client_status(): void
    {
        $user = $this->makeUser();
        $emp = $this->makeEmployee();

        $this->actingAs($user)->post('/admin/payroll', [
            'employee_id' => $emp->id, 'payroll_number' => 'PAY-X1', 'month' => 'Oct', 'year' => 2026,
            'basic_salary' => 1000, 'net_salary' => 1000, 'status' => 'paid',
        ]);

        $this->assertEquals('pending', Payroll::where('payroll_number', 'PAY-X1')->first()->status);
    }

    public function test_paid_payroll_is_immutable(): void
    {
        $creator = $this->makeUser();
        $payroll = $this->makePayroll($creator, 'paid');

        $this->actingAs($creator)->patch("/admin/payroll/{$payroll->id}", [
            'basic_salary' => 2000, 'net_salary' => 2000,
        ])->assertStatus(403);

        $this->assertEquals(1000000, $payroll->fresh()->basic_salary);
    }

    public function test_creator_cannot_mark_own_payroll_paid(): void
    {
        $creator = $this->makeUser();
        $payroll = $this->makePayroll($creator);

        $this->actingAs($creator)->post("/admin/payroll/{$payroll->id}/mark-paid")->assertStatus(403);
        $this->assertEquals('pending', $payroll->fresh()->status);
    }

    public function test_unapproved_payroll_cannot_be_paid_when_workflow_exists(): void
    {
        $this->workflowFor('payroll');
        $creator = $this->makeUser();
        $executor = $this->makeUser();
        $payroll = $this->makePayroll($creator);

        $this->actingAs($executor)->post("/admin/payroll/{$payroll->id}/mark-paid")->assertStatus(403);
        $this->assertEquals('pending', $payroll->fresh()->status);
    }

    public function test_approved_payroll_marks_paid_once(): void
    {
        $this->workflowFor('payroll');
        $creator = $this->makeUser();
        $executor = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $payroll = $this->makePayroll($creator);

        $req = \App\Services\ApprovalService::submit('payroll', $payroll, "Payroll {$payroll->payroll_number}", $payroll->net_salary, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($executor)->post("/admin/payroll/{$payroll->id}/mark-paid")->assertRedirect();
        $this->assertEquals('paid', $payroll->fresh()->status);
        $this->assertNotNull($payroll->fresh()->paid_at);

        // Idempotent — second attempt fails
        $this->actingAs($executor)->post("/admin/payroll/{$payroll->id}/mark-paid")->assertStatus(403);
    }

    public function test_material_edit_invalidates_approved_payroll(): void
    {
        $this->workflowFor('payroll');
        $creator = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $payroll = $this->makePayroll($creator);

        $req = \App\Services\ApprovalService::submit('payroll', $payroll, "Payroll {$payroll->payroll_number}", $payroll->net_salary, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        // Material change: net_salary doubles
        $this->actingAs($creator)->patch("/admin/payroll/{$payroll->id}", [
            'basic_salary' => 2000000, 'net_salary' => 2100000,
        ])->assertRedirect();

        $this->assertEquals('superseded', $req->fresh()->status);
        // Fresh request is pending — stale approval cannot be used to pay
        $pending = ApprovalRequest::where('module', 'payroll')->where('module_id', $payroll->id)->where('status', 'pending')->exists();
        $this->assertTrue($pending);
        $this->assertDatabaseHas('audit_logs', ['module' => 'approvals', 'module_action' => 'approval_invalidated']);
    }

    // ═══ LPO ═══

    private function makeLpo(User $creator, string $status = 'draft'): Lpo
    {
        return Lpo::create([
            'lpo_number' => 'LPO-' . uniqid(), 'supplier_name' => 'Vendor',
            'lpo_date' => '2026-10-20', 'subtotal' => 100000, 'tax_amount' => 18000,
            'total' => 118000, 'status' => $status,
            'created_by' => $creator->id, 'company_id' => $this->company()->id,
        ]);
    }

    public function test_lpo_sent_requires_approved_workflow(): void
    {
        $this->workflowFor('lpo');
        $creator = $this->makeUser();
        $lpo = $this->makeLpo($creator);
        \App\Services\ApprovalService::submit('lpo', $lpo, "LPO {$lpo->lpo_number}", $lpo->total, $creator);

        $this->actingAs($creator)->patch("/admin/lpos/{$lpo->id}/status", ['status' => 'sent'])->assertStatus(403);
        $this->assertEquals('draft', $lpo->fresh()->status);
    }

    public function test_approved_lpo_can_be_sent(): void
    {
        $this->workflowFor('lpo');
        $creator = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $lpo = $this->makeLpo($creator);

        $req = \App\Services\ApprovalService::submit('lpo', $lpo, "LPO {$lpo->lpo_number}", $lpo->total, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($creator)->patch("/admin/lpos/{$lpo->id}/status", ['status' => 'sent'])->assertRedirect();
        $this->assertEquals('sent', $lpo->fresh()->status);
    }

    public function test_rejected_lpo_cannot_be_sent(): void
    {
        $this->workflowFor('lpo');
        $creator = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $lpo = $this->makeLpo($creator);

        $req = \App\Services\ApprovalService::submit('lpo', $lpo, "LPO {$lpo->lpo_number}", $lpo->total, $creator);
        \App\Services\ApprovalService::reject($req, $fm, 'Over budget');

        $this->actingAs($creator)->patch("/admin/lpos/{$lpo->id}/status", ['status' => 'sent'])->assertStatus(403);
    }

    public function test_issued_lpo_cannot_be_deleted(): void
    {
        $creator = $this->makeUser();
        $lpo = $this->makeLpo($creator, 'sent');
        $this->actingAs($creator)->delete("/admin/lpos/{$lpo->id}")->assertStatus(403);
        $this->assertDatabaseHas('lpos', ['id' => $lpo->id]);
    }
}
