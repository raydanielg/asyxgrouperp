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
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GrnStockTest extends TestCase
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

    private function makeProduct(): Product
    {
        return Product::withoutGlobalScopes()->create([
            'name' => 'Product X', 'company_id' => $this->company()->id, 'is_active' => true,
        ]);
    }

    private function makeWarehouse(): Warehouse
    {
        return Warehouse::withoutGlobalScopes()->create([
            'name' => 'WH', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $this->company()->id, 'is_active' => true,
        ]);
    }

    private function makeLpo(User $creator, ?Product $product): array
    {
        $lpo = Lpo::withoutGlobalScopes()->create([
            'lpo_number' => 'LPO-' . uniqid(), 'supplier_name' => 'Vendor',
            'lpo_date' => '2026-10-20', 'subtotal' => 100000, 'tax_amount' => 0,
            'total' => 100000, 'status' => 'sent',
            'created_by' => $creator->id, 'company_id' => $this->company()->id,
        ]);
        $item = LpoItem::create([
            'lpo_id' => $lpo->id, 'product_id' => $product?->id, 'description' => 'Item X',
            'quantity_ordered' => 10, 'quantity_received' => 0, 'unit_price' => 10000, 'line_total' => 100000,
        ]);
        return [$lpo, $item];
    }

    private function makeGrn(User $creator, Lpo $lpo, LpoItem $item, ?int $warehouseId = null, ?int $productId = null): Grn
    {
        $grn = Grn::withoutGlobalScopes()->create([
            'grn_number' => 'GRN-' . uniqid(), 'lpo_id' => $lpo->id,
            'warehouse_id' => $warehouseId, 'received_date' => '2026-10-20',
            'status' => 'pending', 'created_by' => $creator->id,
            'company_id' => $this->company()->id,
        ]);
        GrnItem::create([
            'grn_id' => $grn->id, 'lpo_item_id' => $item->id, 'product_id' => $productId,
            'description' => 'Item X', 'quantity_expected' => 10, 'quantity_received' => 10, 'quantity_discrepant' => 0,
        ]);
        return $grn;
    }

    public function test_approved_grn_posts_stock_to_warehouse(): void
    {
        $this->workflowFor('grn');
        $creator = $this->makeUser();
        $finalizer = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $product = $this->makeProduct();
        $wh = $this->makeWarehouse();
        [$lpo, $item] = $this->makeLpo($creator, $product);
        $grn = $this->makeGrn($creator, $lpo, $item, $wh->id);

        $req = \App\Services\ApprovalService::submit('grn', $grn, "GRN {$grn->grn_number}", null, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($finalizer)->post("/admin/grns/{$grn->id}/receive")->assertRedirect();

        $this->assertEquals(10, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $wh->id)->value('quantity'));
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'type' => 'in', 'quantity' => 10,
        ]);
        $this->assertEquals(1, StockMovement::count());
    }

    public function test_unmapped_product_rejects_stock_posting_and_rolls_back(): void
    {
        $this->workflowFor('grn');
        $creator = $this->makeUser();
        $finalizer = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $wh = $this->makeWarehouse();
        [$lpo, $item] = $this->makeLpo($creator, null); // no product on LPO item
        $grn = $this->makeGrn($creator, $lpo, $item, $wh->id, null); // no product on GRN item

        $req = \App\Services\ApprovalService::submit('grn', $grn, "GRN {$grn->grn_number}", null, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($finalizer)->post("/admin/grns/{$grn->id}/receive")->assertStatus(500);
        $this->assertEquals('pending', $grn->fresh()->status);
        $this->assertEquals(0, $item->fresh()->quantity_received);
        $this->assertEquals(0, StockMovement::count());
    }

    public function test_grn_without_warehouse_applies_lpo_only(): void
    {
        $this->workflowFor('grn');
        $creator = $this->makeUser();
        $finalizer = $this->makeUser();
        $fm = $this->makeUser(['role' => 'finance_manager']);
        [$lpo, $item] = $this->makeLpo($creator, null);
        $grn = $this->makeGrn($creator, $lpo, $item, null);

        $req = \App\Services\ApprovalService::submit('grn', $grn, "GRN {$grn->grn_number}", null, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($finalizer)->post("/admin/grns/{$grn->id}/receive")->assertRedirect();
        $this->assertEquals(10, $item->fresh()->quantity_received);
        $this->assertEquals(0, StockMovement::count());
    }

    public function test_pending_grn_posts_no_stock(): void
    {
        $this->workflowFor('grn');
        $creator = $this->makeUser();
        $other = $this->makeUser();
        $product = $this->makeProduct();
        $wh = $this->makeWarehouse();
        [$lpo, $item] = $this->makeLpo($creator, $product);
        $grn = $this->makeGrn($creator, $lpo, $item, $wh->id);
        \App\Services\ApprovalService::submit('grn', $grn, "GRN {$grn->grn_number}", null, $creator);

        $this->actingAs($other)->post("/admin/grns/{$grn->id}/receive")->assertStatus(403);
        $this->assertEquals(0, StockMovement::count());
    }

    // ═══ CONTRACT ACTIVATION AUTHORIZATION ═══

    private function makeContract(User $creator, string $status = 'draft'): Contract
    {
        return Contract::withoutGlobalScopes()->create([
            'contract_number' => 'CNT-' . uniqid(), 'title' => 'Deal',
            'contractor_name' => 'Vendor', 'type' => 'supply',
            'start_date' => '2026-10-20', 'contract_value' => 500000,
            'status' => $status, 'created_by' => $creator->id,
            'company_id' => $this->company()->id,
        ]);
    }

    public function test_non_admin_with_permission_can_activate_approved_contract(): void
    {
        $this->workflowFor('contract');
        $creator = $this->makeUser();
        $activator = $this->makeUser(['role' => 'accountant']); // non-admin
        $this->grantPerm($activator, 'activate-contracts');
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $contract = $this->makeContract($creator, 'draft');

        $req = \App\Services\ApprovalService::submit('contract', $contract, "Contract {$contract->contract_number}", $contract->contract_value, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($activator)->post("/admin/contracts/{$contract->id}/activate")->assertRedirect();
        $this->assertEquals('active', $contract->fresh()->status);
    }

    public function test_non_admin_without_permission_cannot_activate(): void
    {
        $this->workflowFor('contract');
        $creator = $this->makeUser();
        $noPerm = $this->makeUser(['role' => 'accountant']);
        $fm = $this->makeUser(['role' => 'finance_manager']);
        $contract = $this->makeContract($creator, 'draft');

        $req = \App\Services\ApprovalService::submit('contract', $contract, "Contract {$contract->contract_number}", $contract->contract_value, $creator);
        \App\Services\ApprovalService::approve($req, $fm);

        $this->actingAs($noPerm)->post("/admin/contracts/{$contract->id}/activate")->assertStatus(403);
        $this->assertEquals('draft', $contract->fresh()->status);
    }

    public function test_cross_company_contract_cannot_be_activated(): void
    {
        $this->workflowFor('contract');
        $otherCo = Company::create(['name' => 'Other', 'legal_name' => 'Other Ltd', 'short_code' => 'OTH', 'is_active' => true]);
        $foreignContract = Contract::withoutGlobalScopes()->create([
            'contract_number' => 'CNT-F1', 'title' => 'X', 'contractor_name' => 'Y',
            'type' => 'supply', 'start_date' => '2026-10-20', 'contract_value' => 100,
            'status' => 'draft', 'created_by' => 1, 'company_id' => $otherCo->id,
        ]);
        $activator = $this->makeUser();

        // BelongsToCompany scope → 404 (record invisible), never reachable
        $this->actingAs($activator)->post("/admin/contracts/{$foreignContract->id}/activate")->assertStatus(404);
    }
}
