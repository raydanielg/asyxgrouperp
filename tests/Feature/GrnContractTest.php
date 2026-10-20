<?php

namespace Tests\Feature;

use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\Lpo;
use App\Models\LpoItem;
use App\Models\Permission;
use App\Models\Supplier;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GrnContractTest extends TestCase
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

    // ═══ GRN ═══

    private function makeLpoWithItem(User $creator): array
    {
        $lpo = Lpo::withoutGlobalScopes()->create([
            'lpo_number' => 'LPO-' . uniqid(), 'supplier_name' => 'Vendor',
            'lpo_date' => '2026-10-20', 'subtotal' => 100000, 'tax_amount' => 0,
            'total' => 100000, 'status' => 'sent',
            'created_by' => $creator->id, 'company_id' => $this->company()->id,
        ]);
        $item = LpoItem::create([
            'lpo_id' => $lpo->id, 'description' => 'Item X',
            'quantity_ordered' => 10, 'quantity_received' => 0, 'unit_price' => 10000, 'line_total' => 100000,
        ]);
        return [$lpo, $item];
    }

    private function makeGrn(User $creator, Lpo $lpo, LpoItem $item, string $status = 'pending', float $qty = 10): Grn
    {
        $grn = Grn::withoutGlobalScopes()->create([
            'grn_number' => 'GRN-' . uniqid(), 'lpo_id' => $lpo->id,
            'received_date' => '2026-10-20', 'status' => $status,
            'created_by' => $creator->id, 'company_id' => $this->company()->id,
        ]);
        GrnItem::create([
            'grn_id' => $grn->id, 'lpo_item_id' => $item->id, 'description' => 'Item X',
            'quantity_expected' => 10, 'quantity_received' => $qty, 'quantity_discrepant' => 0,
        ]);
        return $grn;
    }

    public function test_pending_grn_cannot_be_finalized(): void
    {
        $this->workflowFor('grn');
        $creator = $this->makeUser();
        $other = $this->makeUser();
        [$lpo, $item] = $this->makeLpoWithItem($creator);
        $grn = $this->makeGrn($creator, $lpo, $item);
        \App\Services\ApprovalService::submit('grn', $grn, "GRN {$grn->grn_number}", null, $creator);

        $this->actingAs($other)->post("/admin/grns/{$grn->id}/receive")->assertStatus(403);
        $this->assertEquals(0, $item->fresh()->quantity_received);
    }

    public function test_approved_grn_finalizes_once_and_rejects_over_receipt(): void
    {
        $this->workflowFor('grn');
        $creator = $this->makeUser();
        $finalizer = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        [$lpo, $item] = $this->makeLpoWithItem($creator);
        $grn = $this->makeGrn($creator, $lpo, $item, 'pending', 10);

        $req = \App\Services\ApprovalService::submit('grn', $grn, "GRN {$grn->grn_number}", null, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($finalizer)->post("/admin/grns/{$grn->id}/receive")->assertRedirect();
        $this->assertEquals('received', $grn->fresh()->status);
        $this->assertEquals(10, $item->fresh()->quantity_received);
        $this->assertEquals('received', $lpo->fresh()->status);

        // Idempotent — cannot finalize twice
        $this->actingAs($finalizer)->post("/admin/grns/{$grn->id}/receive")->assertStatus(403);
        $this->assertEquals(10, $item->fresh()->quantity_received);
    }

    public function test_over_receipt_rejected_at_finalize(): void
    {
        $this->workflowFor('grn');
        $creator = $this->makeUser();
        $finalizer = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        [$lpo, $item] = $this->makeLpoWithItem($creator);
        $item->update(['quantity_received' => 8]); // 8 already received
        $grn = $this->makeGrn($creator, $lpo, $item, 'pending', 5); // would take to 13 > 10

        $req = \App\Services\ApprovalService::submit('grn', $grn, "GRN {$grn->grn_number}", null, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($finalizer)->post("/admin/grns/{$grn->id}/receive")->assertStatus(500);
        // Quantities unchanged — transaction rolled back
        $this->assertEquals(8, $item->fresh()->quantity_received);
        $this->assertEquals('pending', $grn->fresh()->status);
    }

    public function test_received_grn_cannot_be_deleted(): void
    {
        $creator = $this->makeUser();
        [$lpo, $item] = $this->makeLpoWithItem($creator);
        $grn = $this->makeGrn($creator, $lpo, $item, 'received');

        $this->actingAs($creator)->delete("/admin/grns/{$grn->id}")->assertStatus(403);
        $this->assertDatabaseHas('grns', ['id' => $grn->id]);
    }

    // ═══ TRANSFER ═══

    public function test_transfer_creator_cannot_self_approve(): void
    {
        $whA = Warehouse::withoutGlobalScopes()->create(['name' => 'W1', 'address' => 'a', 'city' => 'a', 'zip_code' => '1', 'company_id' => $this->company()->id, 'is_active' => true]);
        $whB = Warehouse::withoutGlobalScopes()->create(['name' => 'W2', 'address' => 'a', 'city' => 'a', 'zip_code' => '1', 'company_id' => $this->company()->id, 'is_active' => true]);
        $creator = $this->makeUser();
        $this->actingAs($creator); // company_id hook requires an authenticated user
        $transfer = Transfer::create([
            'from_warehouse' => $whA->id, 'to_warehouse' => $whB->id,
            'product_name' => 'Widget', 'quantity' => 5, 'status' => 'pending',
            'created_by' => $creator->id, 'creator_id' => $creator->id,
            'company_id' => $this->company()->id,
        ]);

        $this->actingAs($creator)->patch("/admin/transfers/{$transfer->id}/approve")->assertStatus(403);
        $this->assertEquals('pending', $transfer->fresh()->status);
    }

    // ═══ CONTRACT ═══

    private function makeContract(User $creator, string $status = 'draft'): Contract
    {
        return Contract::withoutGlobalScopes()->create([
            'contract_number' => 'CNT-' . uniqid(), 'title' => 'Supply Deal',
            'contractor_name' => 'Vendor X', 'type' => 'supply',
            'start_date' => '2026-10-20', 'contract_value' => 500000,
            'status' => $status, 'created_by' => $creator->id,
            'company_id' => $this->company()->id,
        ]);
    }

    public function test_contract_update_cannot_forge_status(): void
    {
        $creator = $this->makeUser();
        $contract = $this->makeContract($creator, 'draft');

        $this->actingAs($creator)->put("/admin/contracts/{$contract->id}", [
            'title' => 'Supply Deal', 'contractor_name' => 'Vendor X', 'type' => 'supply',
            'start_date' => '2026-10-20', 'contract_value' => 500000, 'status' => 'active',
        ]);

        $this->assertEquals('draft', $contract->fresh()->status);
    }

    public function test_draft_contract_cannot_activate_without_approval(): void
    {
        $this->workflowFor('contract');
        $creator = $this->makeUser();
        $activator = $this->makeUser();
        $contract = $this->makeContract($creator, 'draft');

        $this->actingAs($activator)->post("/admin/contracts/{$contract->id}/activate")->assertStatus(403);
        $this->assertEquals('draft', $contract->fresh()->status);
    }

    public function test_approved_contract_activates_and_material_edit_invalidates(): void
    {
        $this->workflowFor('contract');
        $creator = $this->makeUser();
        $activator = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $contract = $this->makeContract($creator, 'draft');

        $req = \App\Services\ApprovalService::submit('contract', $contract, "Contract {$contract->contract_number}", $contract->contract_value, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        // Material edit (contract_value change) → supersede + resubmit
        $this->actingAs($creator)->put("/admin/contracts/{$contract->id}", [
            'title' => 'Supply Deal', 'contractor_name' => 'Vendor X', 'type' => 'supply',
            'start_date' => '2026-10-20', 'contract_value' => 750000,
        ])->assertRedirect();

        $this->assertEquals('superseded', $req->fresh()->status);
        // Stale approval cannot activate
        $this->actingAs($activator)->post("/admin/contracts/{$contract->id}/activate")->assertStatus(403);
    }

    public function test_approved_contract_activates(): void
    {
        $this->workflowFor('contract');
        $creator = $this->makeUser();
        $activator = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $contract = $this->makeContract($creator, 'draft');

        $req = \App\Services\ApprovalService::submit('contract', $contract, "Contract {$contract->contract_number}", $contract->contract_value, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($activator)->post("/admin/contracts/{$contract->id}/activate")->assertRedirect();
        $this->assertEquals('active', $contract->fresh()->status);
    }
}
