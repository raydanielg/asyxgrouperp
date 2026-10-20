<?php

namespace Tests\Feature;

use App\Models\ApprovalRequest;
use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\Branch;
use App\Models\Company;
use App\Models\OfficeExpense;
use App\Models\Supplier;
use App\Models\User;
use App\Models\VendorInvoice;
use App\Models\VendorPayment;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TransactionApprovalTest extends TestCase
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
        ApprovalStep::create(['workflow_id' => $wf->id, 'level' => 1, 'name' => 'Step 1', 'approver_type' => 'role', 'approver_role' => $role, 'order' => 0]);
        return $wf;
    }

    // ═══ OFFICE EXPENSE ═══

    public function test_expense_submit_creates_approval_request(): void
    {
        $this->workflowFor('office_expense');
        $user = $this->makeUser();

        $this->actingAs($user)->post('/admin/office-expenses', [
            'description' => 'Office supplies', 'amount' => 500000, 'expense_date' => '2026-10-20',
        ]);

        $expense = OfficeExpense::latest()->first();
        $this->assertEquals('pending', $expense->status);
        $this->assertDatabaseHas('approval_requests', [
            'module' => 'office_expense', 'module_id' => $expense->id, 'status' => 'pending',
        ]);
    }

    public function test_expense_creator_cannot_self_approve(): void
    {
        $this->workflowFor('office_expense');
        $user = $this->makeUser();
        $expense = OfficeExpense::create([
            'expense_number' => 'OEXP-1', 'description' => 'x', 'amount' => 200000,
            'expense_date' => '2026-10-20', 'status' => 'pending',
            'requested_by' => $user->id, 'created_by' => $user->id, 'company_id' => $this->company()->id,
        ]);

        $this->actingAs($user)->post("/admin/office-expenses/{$expense->id}/approve")->assertStatus(403);
        $this->assertEquals('pending', $expense->fresh()->status);
    }

    public function test_designated_approver_approves_expense_via_engine(): void
    {
        $this->workflowFor('office_expense');
        $requester = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $expense = OfficeExpense::create([
            'expense_number' => 'OEXP-2', 'description' => 'x', 'amount' => 300000,
            'expense_date' => '2026-10-20', 'status' => 'pending',
            'requested_by' => $requester->id, 'created_by' => $requester->id, 'company_id' => $this->company()->id,
        ]);
        \App\Services\ApprovalService::submit('office_expense', $expense, 'Office Expense OEXP-2', 300000, $requester);

        $this->actingAs($fm)->post("/admin/office-expenses/{$expense->id}/approve")->assertRedirect();
        $this->assertEquals('approved', $expense->fresh()->status);
        $this->assertNotNull($expense->fresh()->approved_by);
    }

    // ═══ VENDOR PAYMENT ═══

    private function makeInvoice(): VendorInvoice
    {
        $supplier = Supplier::create(['name' => 'Vendor X', 'company_id' => $this->company()->id]);
        return VendorInvoice::withoutGlobalScopes()->create([
            'vendor_invoice_number' => 'VI-' . uniqid(), 'supplier_id' => $supplier->id,
            'invoice_date' => '2026-10-20', 'subtotal' => 1000000, 'tax_amount' => 0,
            'total' => 1000000, 'amount_paid' => 0, 'balance' => 1000000,
            'status' => 'posted', 'company_id' => $this->company()->id,
        ]);
    }

    public function test_payment_pending_when_workflow_configured(): void
    {
        $this->workflowFor('vendor_payment');
        $invoice = $this->makeInvoice();
        $user = $this->makeUser();

        $this->actingAs($user)->post('/admin/vendor-payments', [
            'vendor_invoice_id' => $invoice->id, 'payment_date' => '2026-10-20',
            'amount' => 400000, 'payment_method' => 'bank_transfer',
        ]);

        $payment = VendorPayment::latest()->first();
        $this->assertEquals('pending', $payment->status);
        // No financial effects yet
        $this->assertEquals(1000000, $invoice->fresh()->balance);
        $this->assertDatabaseHas('approval_requests', ['module' => 'vendor_payment', 'status' => 'pending']);
    }

    public function test_pending_payment_cannot_be_executed(): void
    {
        $this->workflowFor('vendor_payment');
        $invoice = $this->makeInvoice();
        $user = $this->makeUser();
        $other = $this->makeUser();

        $payment = VendorPayment::create([
            'payment_number' => 'VP-1', 'vendor_invoice_id' => $invoice->id,
            'supplier_id' => $invoice->supplier_id, 'payment_date' => '2026-10-20',
            'amount' => 400000, 'payment_method' => 'cash', 'status' => 'pending',
            'created_by' => $user->id, 'company_id' => $this->company()->id,
        ]);
        \App\Services\ApprovalService::submit('vendor_payment', $payment, 'VP-1', 400000, $user);

        $this->actingAs($other)->post("/admin/vendor-payments/{$payment->id}/execute")->assertStatus(403);
        $this->assertEquals('pending', $payment->fresh()->status);
    }

    public function test_approved_payment_executes_and_posts_effects(): void
    {
        $this->workflowFor('vendor_payment');
        $invoice = $this->makeInvoice();
        $user = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $executor = $this->makeUser();

        $payment = VendorPayment::create([
            'payment_number' => 'VP-2', 'vendor_invoice_id' => $invoice->id,
            'supplier_id' => $invoice->supplier_id, 'payment_date' => '2026-10-20',
            'amount' => 400000, 'payment_method' => 'cash', 'status' => 'pending',
            'created_by' => $user->id, 'company_id' => $this->company()->id,
        ]);
        $req = \App\Services\ApprovalService::submit('vendor_payment', $payment, 'VP-2', 400000, $user);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($executor)->post("/admin/vendor-payments/{$payment->id}/execute")->assertRedirect();
        $this->assertEquals('completed', $payment->fresh()->status);
        $this->assertEquals(600000, $invoice->fresh()->balance);
        $this->assertEquals('partially_paid', $invoice->fresh()->status);
    }

    public function test_rejected_payment_cannot_be_executed(): void
    {
        $this->workflowFor('vendor_payment');
        $invoice = $this->makeInvoice();
        $user = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);

        $payment = VendorPayment::create([
            'payment_number' => 'VP-3', 'vendor_invoice_id' => $invoice->id,
            'supplier_id' => $invoice->supplier_id, 'payment_date' => '2026-10-20',
            'amount' => 400000, 'payment_method' => 'cash', 'status' => 'pending',
            'created_by' => $user->id, 'company_id' => $this->company()->id,
        ]);
        $req = \App\Services\ApprovalService::submit('vendor_payment', $payment, 'VP-3', 400000, $user);
        \App\Services\ApprovalService::reject($req, $fm, 'Insufficient budget');

        $this->actingAs($fm)->post("/admin/vendor-payments/{$payment->id}/execute")->assertStatus(403);
        $this->assertEquals('rejected', $payment->fresh()->status);
    }

    public function test_payment_creator_cannot_execute_own_payment(): void
    {
        $this->workflowFor('vendor_payment');
        $invoice = $this->makeInvoice();
        $user = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);

        $payment = VendorPayment::create([
            'payment_number' => 'VP-4', 'vendor_invoice_id' => $invoice->id,
            'supplier_id' => $invoice->supplier_id, 'payment_date' => '2026-10-20',
            'amount' => 400000, 'payment_method' => 'cash', 'status' => 'pending',
            'created_by' => $user->id, 'company_id' => $this->company()->id,
        ]);
        $req = \App\Services\ApprovalService::submit('vendor_payment', $payment, 'VP-4', 400000, $user);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($user)->post("/admin/vendor-payments/{$payment->id}/execute")->assertStatus(403);
    }

    // ═══ WAREHOUSE BRANCH SCOPE ═══

    public function test_branch_restricted_user_sees_only_their_warehouses(): void
    {
        $branchA = Branch::create(['company_id' => $this->company()->id, 'name' => 'HQ']);
        $branchB = Branch::create(['company_id' => $this->company()->id, 'name' => 'MWZ']);

        $whA = Warehouse::withoutGlobalScopes()->create(['name' => 'WH-A', 'company_id' => $this->company()->id, 'branch_id' => $branchA->id, 'is_active' => true]);
        $whB = Warehouse::withoutGlobalScopes()->create(['name' => 'WH-B', 'company_id' => $this->company()->id, 'branch_id' => $branchB->id, 'is_active' => true]);
        $whNull = Warehouse::withoutGlobalScopes()->create(['name' => 'WH-Gen', 'company_id' => $this->company()->id, 'branch_id' => null, 'is_active' => true]);

        $restricted = $this->makeUser(['role' => 'user']);
        $restricted->branches()->sync([$branchA->id]);

        $this->actingAs($restricted);
        $visible = Warehouse::withoutGlobalScope('company')->pluck('name')->all();

        $this->assertContains('WH-A', $visible);
        $this->assertContains('WH-Gen', $visible); // unassigned = company-wide
        $this->assertNotContains('WH-B', $visible);
    }

    public function test_company_wide_user_sees_all_warehouses(): void
    {
        $branchA = Branch::create(['company_id' => $this->company()->id, 'name' => 'HQ']);
        Warehouse::withoutGlobalScopes()->create(['name' => 'WH-A', 'company_id' => $this->company()->id, 'branch_id' => $branchA->id, 'is_active' => true]);
        Warehouse::withoutGlobalScopes()->create(['name' => 'WH-B', 'company_id' => $this->company()->id, 'branch_id' => null, 'is_active' => true]);

        $user = $this->makeUser(['role' => 'admin']);
        $this->actingAs($user);
        $this->assertEquals(2, Warehouse::count());
    }
}
