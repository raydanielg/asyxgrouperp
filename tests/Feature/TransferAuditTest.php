<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\GrnItem;
use App\Models\Grn;
use App\Models\Lpo;
use App\Models\LpoItem;
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\StockMovement;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TransferAuditTest extends TestCase
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

    private function makeWarehouse(string $name): Warehouse
    {
        return Warehouse::withoutGlobalScopes()->create([
            'name' => $name, 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $this->company()->id, 'is_active' => true,
        ]);
    }

    private function makeProduct(): Product
    {
        return Product::withoutGlobalScopes()->create([
            'name' => 'P-X', 'product_code' => 'P-' . uniqid(),
            'company_id' => $this->company()->id, 'is_active' => true,
        ]);
    }

    private function makeTransfer(User $creator, Warehouse $from, Warehouse $to, Product $product, float $qty): Transfer
    {
        return Transfer::create([
            'from_warehouse' => $from->id, 'to_warehouse' => $to->id,
            'product_id' => $product->id, 'product_name' => $product->name,
            'quantity' => $qty, 'status' => 'pending',
            'created_by' => $creator->id, 'creator_id' => $creator->id,
            'company_id' => $this->company()->id,
        ]);
    }

    private function seedStock(Product $p, Warehouse $w, float $qty): void
    {
        ProductWarehouseStock::withoutGlobalScopes()->create([
            'product_id' => $p->id, 'warehouse_id' => $w->id,
            'quantity' => $qty, 'reserved_quantity' => 0,
        ]);
    }

    // ═══ TRANSFERS ═══

    public function test_approved_transfer_moves_real_stock(): void
    {
        $creator = $this->makeUser();
        $approver = $this->makeUser();
        $executor = $this->makeUser();
        $from = $this->makeWarehouse('W1'); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 50);

        $transfer = $this->makeTransfer($creator, $from, $to, $product, 20);

        // Approve → reserves full qty, no stock moves yet
        $this->actingAs($approver)->patch("/admin/transfers/{$transfer->id}/approve")->assertRedirect();
        $this->assertEquals('approved', $transfer->fresh()->status);
        $this->assertEquals(50, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('quantity'));
        $this->assertEquals(20, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('reserved_quantity'));

        // Execute → consumes reservation + moves stock
        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute")->assertRedirect();
        $this->assertEquals('completed', $transfer->fresh()->status);
        $this->assertEquals(30, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('quantity'));
        $this->assertEquals(0, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('reserved_quantity'));
        $this->assertEquals(20, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $to->id)->value('quantity'));
        $this->assertEquals(2, StockMovement::where('reference', 'like', "Transfer #{$transfer->id}%")->count());
    }

    public function test_insufficient_source_stock_rejected(): void
    {
        $creator = $this->makeUser();
        $approver = $this->makeUser();
        $from = $this->makeWarehouse('W1'); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 5);

        $transfer = $this->makeTransfer($creator, $from, $to, $product, 20);
        $this->actingAs($approver)->patch("/admin/transfers/{$transfer->id}/approve");

        $this->assertNotEquals('approved', $transfer->fresh()->status);
        $this->assertNotEquals('completed', $transfer->fresh()->status);
        $this->assertEquals(5, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('quantity'));
        $this->assertEquals(0, StockMovement::count());
    }

    public function test_completed_transfer_cannot_move_stock_twice(): void
    {
        $creator = $this->makeUser();
        $approver = $this->makeUser();
        $executor = $this->makeUser();
        $from = $this->makeWarehouse('W1'); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 50);

        $transfer = $this->makeTransfer($creator, $from, $to, $product, 20);
        $this->actingAs($approver)->patch("/admin/transfers/{$transfer->id}/approve")->assertRedirect();
        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute")->assertRedirect();
        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute")->assertStatus(403);

        $this->assertEquals(30, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('quantity'));
        $this->assertEquals(2, StockMovement::count());
    }

    public function test_partial_transfer_executes_twice_then_completes(): void
    {
        $creator = $this->makeUser();
        $approver = $this->makeUser();
        $executor = $this->makeUser();
        $from = $this->makeWarehouse('W1'); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 50);

        $transfer = $this->makeTransfer($creator, $from, $to, $product, 20);
        $this->actingAs($approver)->patch("/admin/transfers/{$transfer->id}/approve");

        // First partial: 8
        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute", ['quantity' => 8])->assertRedirect();
        $t = $transfer->fresh();
        $this->assertEquals('partially_transferred', $t->status);
        $this->assertEquals(8, $t->transferred_quantity);
        $this->assertEquals(42, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('quantity'));
        $this->assertEquals(12, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('reserved_quantity'));

        // Second partial: remaining 12 → completed
        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute", ['quantity' => 12])->assertRedirect();
        $this->assertEquals('completed', $transfer->fresh()->status);
        $this->assertEquals(30, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('quantity'));
        $this->assertEquals(20, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $to->id)->value('quantity'));
        $this->assertEquals(0, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('reserved_quantity'));
    }

    public function test_partial_execute_capped_at_outstanding(): void
    {
        $creator = $this->makeUser();
        $approver = $this->makeUser();
        $executor = $this->makeUser();
        $from = $this->makeWarehouse('W1'); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 50);

        $transfer = $this->makeTransfer($creator, $from, $to, $product, 20);
        $this->actingAs($approver)->patch("/admin/transfers/{$transfer->id}/approve");
        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute", ['quantity' => 15]);

        // Attempt to execute 10 → capped at outstanding 5 (20 total moved)
        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute", ['quantity' => 10]);
        $this->assertEquals(30, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('quantity'));
        $this->assertEquals('completed', $transfer->fresh()->status);
    }

    public function test_cancel_releases_reservation(): void
    {
        $creator = $this->makeUser();
        $approver = $this->makeUser();
        $from = $this->makeWarehouse('W1'); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 50);

        $transfer = $this->makeTransfer($creator, $from, $to, $product, 20);
        $this->actingAs($approver)->patch("/admin/transfers/{$transfer->id}/approve");
        $this->actingAs($approver)->post("/admin/transfers/{$transfer->id}/cancel")->assertRedirect();

        $this->assertEquals('cancelled', $transfer->fresh()->status);
        $this->assertEquals(0, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('reserved_quantity'));
        $this->assertEquals(50, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('quantity'));

        // Cancelled transfer cannot be executed
        $this->actingAs($approver)->post("/admin/transfers/{$transfer->id}/execute")->assertStatus(403);
    }

    public function test_reserved_stock_cannot_be_taken_by_other_operation(): void
    {
        $from = $this->makeWarehouse('W1'); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 10);

        // Reserve all 10 for another operation
        app(\App\Services\InventoryService::class)->reserveStock($product->id, $from->id, 10);

        // Now a removeStock attempt for anything > 0 fails — stock is reserved
        $this->expectException(\RuntimeException::class);
        app(\App\Services\InventoryService::class)->removeStock($product->id, $from->id, 5, 'test');
    }

    public function test_cross_company_warehouse_rejected_on_store(): void
    {
        $otherCo = Company::create(['name' => 'Other', 'legal_name' => 'Other Ltd', 'short_code' => 'OTH', 'is_active' => true]);
        $foreignWh = Warehouse::withoutGlobalScopes()->create([
            'name' => 'FW', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $otherCo->id, 'is_active' => true,
        ]);
        $from = $this->makeWarehouse('W1');
        $product = $this->makeProduct();
        $creator = $this->makeUser();

        $this->actingAs($creator)->post('/admin/transfers', [
            'from_warehouse' => $from->id, 'to_warehouse' => $foreignWh->id,
            'product_id' => $product->id, 'quantity' => 10,
        ])->assertSessionHasErrors('to_warehouse');
    }

    public function test_completed_transfer_cannot_be_deleted(): void
    {
        $creator = $this->makeUser();
        $from = $this->makeWarehouse('W1'); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $transfer = $this->makeTransfer($creator, $from, $to, $product, 10);
        $transfer->update(['status' => 'completed']);

        $this->actingAs($this->makeUser())->delete("/admin/transfers/{$transfer->id}")->assertStatus(403);
        $this->assertDatabaseHas('transfers', ['id' => $transfer->id]);
    }

    // ═══ RECONCILIATION ═══

    public function test_reconciliation_maps_product_with_audit(): void
    {
        $admin = $this->makeUser();
        $product = $this->makeProduct();
        $lpo = Lpo::withoutGlobalScopes()->create([
            'lpo_number' => 'LPO-RC1', 'lpo_date' => '2026-10-20', 'subtotal' => 0,
            'total' => 0, 'status' => 'draft', 'created_by' => $admin->id,
            'company_id' => $this->company()->id,
        ]);
        $item = LpoItem::create(['lpo_id' => $lpo->id, 'description' => 'Unmapped item', 'quantity_ordered' => 5, 'unit_price' => 100]);

        $this->actingAs($admin)->post('/admin/inventory/map-product', [
            'item_type' => 'lpo_item', 'item_id' => $item->id,
            'product_id' => $product->id, 'reason' => 'Correct mapping',
        ])->assertRedirect();

        $this->assertEquals($product->id, $item->fresh()->product_id);
        $this->assertDatabaseHas('audit_logs', ['module' => 'inventory', 'module_action' => 'product_mapping_reconciled']);
    }

    public function test_cross_company_product_mapping_rejected(): void
    {
        $otherCo = Company::create(['name' => 'Other', 'legal_name' => 'O', 'short_code' => 'OTH', 'is_active' => true]);
        $foreignProduct = Product::withoutGlobalScopes()->create([
            'name' => 'FP', 'product_code' => 'FP-1', 'company_id' => $otherCo->id, 'is_active' => true,
        ]);
        $admin = $this->makeUser();
        $lpo = Lpo::withoutGlobalScopes()->create([
            'lpo_number' => 'LPO-RC2', 'lpo_date' => '2026-10-20', 'subtotal' => 0,
            'total' => 0, 'status' => 'draft', 'created_by' => $admin->id,
            'company_id' => $this->company()->id,
        ]);
        $item = LpoItem::create(['lpo_id' => $lpo->id, 'description' => 'x', 'quantity_ordered' => 1, 'unit_price' => 1]);

        $this->actingAs($admin)->post('/admin/inventory/map-product', [
            'item_type' => 'lpo_item', 'item_id' => $item->id,
            'product_id' => $foreignProduct->id, 'reason' => 'x',
        ])->assertSessionHasErrors('product_id');
        $this->assertNull($item->fresh()->product_id);
    }

    public function test_posted_grn_item_cannot_be_remapped(): void
    {
        $admin = $this->makeUser();
        $product = $this->makeProduct();
        $wh = $this->makeWarehouse('W1');
        $grn = Grn::withoutGlobalScopes()->create([
            'grn_number' => 'GRN-RC1', 'received_date' => '2026-10-20', 'status' => 'received',
            'warehouse_id' => $wh->id, 'created_by' => $admin->id,
            'company_id' => $this->company()->id,
        ]);
        $item = GrnItem::create(['grn_id' => $grn->id, 'description' => 'x', 'quantity_received' => 5, 'quantity_expected' => 5]);

        $this->actingAs($admin)->post('/admin/inventory/map-product', [
            'item_type' => 'grn_item', 'item_id' => $item->id,
            'product_id' => $product->id, 'reason' => 'x',
        ]);

        $this->assertNull($item->fresh()->product_id);
    }

    // ═══ AUDIT REPORT ═══

    public function test_audit_detects_negative_balance_and_duplicate_references(): void
    {
        $admin = $this->makeUser();
        $product = $this->makeProduct();
        $wh = $this->makeWarehouse('W1');

        ProductWarehouseStock::withoutGlobalScopes()->create([
            'product_id' => $product->id, 'warehouse_id' => $wh->id,
            'quantity' => -10, 'reserved_quantity' => 0,
        ]);
        StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $wh->id, 'type' => 'in', 'quantity' => 1, 'balance_after' => 1, 'reference' => 'dup-1', 'company_id' => $this->company()->id, 'created_by' => $admin->id]);
        StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $wh->id, 'type' => 'in', 'quantity' => 1, 'balance_after' => 2, 'reference' => 'dup-1', 'company_id' => $this->company()->id, 'created_by' => $admin->id]);
        StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $wh->id, 'type' => 'in', 'quantity' => 1, 'balance_after' => 3, 'reference' => 'dup-1', 'company_id' => $this->company()->id, 'created_by' => $admin->id]);

        $response = $this->actingAs($admin)->get('/admin/inventory/audit');
        $response->assertStatus(200);
        $response->assertSee('P-X');       // negative balance row
        $response->assertSee('dup-1');     // duplicate reference
    }

    public function test_unauthorized_user_cannot_reconcile(): void
    {
        $nonAdmin = $this->makeUser(['role' => 'accountant']);
        $this->actingAs($nonAdmin)->get('/admin/inventory/reconciliation')->assertStatus(403);
    }
}
