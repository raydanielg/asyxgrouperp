<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\StockMovement;
use App\Models\StockReservation;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReservationAttributionTest extends TestCase
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

    private function makeWarehouse(string $name = 'W1'): Warehouse
    {
        return Warehouse::withoutGlobalScopes()->create([
            'name' => $name, 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $this->company()->id, 'is_active' => true,
        ]);
    }

    private function makeProduct(?int $warehouseId = null): Product
    {
        return Product::withoutGlobalScopes()->create([
            'name' => 'P-X', 'product_code' => 'P-' . uniqid(), 'warehouse_id' => $warehouseId,
            'company_id' => $this->company()->id, 'is_active' => true, 'stock_quantity' => 50,
        ]);
    }

    private function seedStock(Product $p, Warehouse $w, float $qty, float $reserved = 0): void
    {
        ProductWarehouseStock::withoutGlobalScopes()->updateOrCreate(
            ['product_id' => $p->id, 'warehouse_id' => $w->id],
            ['quantity' => $qty, 'reserved_quantity' => $reserved]
        );
    }

    // ═══ ATTRIBUTION ═══

    public function test_transfer_approval_creates_attributed_reservation_record(): void
    {
        $creator = $this->makeUser();
        $approver = $this->makeUser();
        $from = $this->makeWarehouse(); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 50);

        $this->actingAs($creator);
        $transfer = Transfer::create([
            'from_warehouse' => $from->id, 'to_warehouse' => $to->id, 'product_id' => $product->id,
            'product_name' => $product->name, 'quantity' => 20, 'status' => 'pending',
            'created_by' => $creator->id, 'company_id' => $this->company()->id,
        ]);

        $this->actingAs($approver)->patch("/admin/transfers/{$transfer->id}/approve")->assertRedirect();

        $res = StockReservation::where('transfer_id', $transfer->id)->first();
        $this->assertNotNull($res);
        $this->assertEquals('active', $res->status);
        $this->assertEquals(20, $res->quantity);
        $this->assertEquals($product->id, $res->product_id);
        $this->assertEquals($from->id, $res->warehouse_id);
    }

    public function test_consume_updates_reservation_attribution(): void
    {
        $creator = $this->makeUser();
        $approver = $this->makeUser();
        $executor = $this->makeUser();
        $from = $this->makeWarehouse(); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 50);

        $this->actingAs($creator);
        $transfer = Transfer::create([
            'from_warehouse' => $from->id, 'to_warehouse' => $to->id, 'product_id' => $product->id,
            'product_name' => $product->name, 'quantity' => 20, 'status' => 'pending',
            'created_by' => $creator->id, 'company_id' => $this->company()->id,
        ]);
        $this->actingAs($approver)->patch("/admin/transfers/{$transfer->id}/approve");
        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute", ['quantity' => 8]);

        $res = StockReservation::where('transfer_id', $transfer->id)->first();
        $this->assertEquals(8, $res->consumed_quantity);
        $this->assertEquals('active', $res->status); // 12 remaining

        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute", ['quantity' => 12]);
        $res = $res->fresh();
        $this->assertEquals(20, $res->consumed_quantity);
        $this->assertEquals('consumed', $res->status); // fully consumed — history preserved
        $this->assertDatabaseHas('stock_reservations', ['id' => $res->id]); // never deleted
    }

    public function test_cancel_marks_reservation_released(): void
    {
        $creator = $this->makeUser();
        $approver = $this->makeUser();
        $from = $this->makeWarehouse(); $to = $this->makeWarehouse('W2');
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 50);

        $this->actingAs($creator);
        $transfer = Transfer::create([
            'from_warehouse' => $from->id, 'to_warehouse' => $to->id, 'product_id' => $product->id,
            'product_name' => $product->name, 'quantity' => 20, 'status' => 'pending',
            'created_by' => $creator->id, 'company_id' => $this->company()->id,
        ]);
        $this->actingAs($approver)->patch("/admin/transfers/{$transfer->id}/approve");
        $this->actingAs($approver)->post("/admin/transfers/{$transfer->id}/cancel");

        $res = StockReservation::where('transfer_id', $transfer->id)->first();
        $this->assertEquals('released', $res->status);
        $this->assertEquals(0, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $from->id)->value('reserved_quantity'));
    }

    public function test_double_reserve_same_transfer_rejected(): void
    {
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct();
        $this->seedStock($product, $wh, 50);
        $this->actingAs($this->makeUser());
        $transfer = Transfer::create([
            'from_warehouse' => $wh->id, 'to_warehouse' => $wh->id + 999, 'product_id' => $product->id,
            'product_name' => 'x', 'quantity' => 10, 'status' => 'pending', 'created_by' => 1,
            'company_id' => $this->company()->id,
        ]);
        $inv = app(\App\Services\InventoryService::class);
        $inv->reserveStock($product->id, $wh->id, 10, $transfer->id);

        $this->expectException(\RuntimeException::class);
        $inv->reserveStock($product->id, $wh->id, 10, $transfer->id); // duplicate hold rejected
    }

    // ═══ POS WAREHOUSE POLICY ═══

    public function test_pos_rejects_product_without_warehouse(): void
    {
        $admin = $this->makeUser();
        $product = $this->makeProduct(null); // no warehouse
        $this->seedStock($product, $this->makeWarehouse(), 50);

        $response = $this->actingAs($admin)->post('/admin/pos', [
            'subtotal' => 50, 'total_amount' => 50, 'paid_amount' => 50, 'payment_method' => 'cash',
            'items' => [['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 5, 'unit_price' => 10, 'total' => 50]],
        ]);

        $this->assertNotEquals(200, $response->status() === 200 ? 200 : $response->status());
        $this->assertEquals(50, $product->fresh()->stock_quantity); // unchanged — no silent decrement
        $this->assertEquals(0, StockMovement::count());
    }

    public function test_pos_movement_links_to_sale(): void
    {
        $admin = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct($wh->id);
        $this->seedStock($product, $wh, 50);

        $this->actingAs($admin)->post('/admin/pos', [
            'subtotal' => 50, 'total_amount' => 50, 'paid_amount' => 50, 'payment_method' => 'cash',
            'items' => [['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 5, 'unit_price' => 10, 'total' => 50]],
        ]);

        $movement = StockMovement::where('product_id', $product->id)->first();
        $this->assertNotNull($movement);
        $this->assertStringContainsString('POS', $movement->reference);
    }

    // ═══ WAREHOUSE ASSIGNMENT RECONCILIATION ═══

    public function test_assign_warehouse_to_unmapped_product(): void
    {
        $admin = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct(null);

        $this->actingAs($admin)->post('/admin/inventory/assign-warehouse', [
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'reason' => 'POS fix',
        ])->assertRedirect();

        $this->assertEquals($wh->id, $product->fresh()->warehouse_id);
        $this->assertDatabaseHas('audit_logs', ['module' => 'inventory', 'module_action' => 'product_warehouse_assigned']);
    }

    public function test_cross_company_warehouse_assignment_rejected(): void
    {
        $otherCo = Company::create(['name' => 'Other', 'legal_name' => 'O', 'short_code' => 'OTH', 'is_active' => true]);
        $foreignWh = Warehouse::withoutGlobalScopes()->create([
            'name' => 'FW', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $otherCo->id, 'is_active' => true,
        ]);
        $admin = $this->makeUser();
        $product = $this->makeProduct(null);

        $this->actingAs($admin)->post('/admin/inventory/assign-warehouse', [
            'product_id' => $product->id, 'warehouse_id' => $foreignWh->id, 'reason' => 'x',
        ])->assertSessionHasErrors('warehouse_id');
        $this->assertNull($product->fresh()->warehouse_id);
    }

    public function test_audit_flags_unattributed_reservation(): void
    {
        $admin = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct();

        // Legacy active reservation with no transfer link
        $this->actingAs($admin);
        StockReservation::create([
            'company_id' => $this->company()->id, 'product_id' => $product->id,
            'warehouse_id' => $wh->id, 'transfer_id' => null,
            'quantity' => 10, 'status' => 'active',
        ]);

        $this->get('/admin/inventory/audit')->assertSee('no source record', false);
    }
}
