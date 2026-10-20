<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\StockMovement;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReservationTest extends TestCase
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

    private function makeWarehouse(): Warehouse
    {
        return Warehouse::withoutGlobalScopes()->create([
            'name' => 'WH', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
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

    // ═══ POS RESPECTS RESERVATIONS ═══

    public function test_pos_sale_cannot_sell_reserved_stock(): void
    {
        $admin = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct($wh->id);
        // 50 on-hand, 45 reserved for a transfer → only 5 available
        $this->seedStock($product, $wh, 50, 45);

        $response = $this->actingAs($admin)->post('/admin/pos', [
            'subtotal' => 100, 'total_amount' => 100, 'paid_amount' => 100,
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product->id, 'product_name' => $product->name,
                 'quantity' => 10, 'unit_price' => 10, 'total' => 100],
            ],
        ]);

        // Sale must fail — 10 requested but only 5 available
        $this->assertNotEquals(302, $response->status() === 302 && !session()->has('error') ? 302 : $response->status());
        $this->assertEquals(50, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $wh->id)->value('quantity'));
    }

    public function test_pos_sale_succeeds_within_available(): void
    {
        $admin = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct($wh->id);
        $this->seedStock($product, $wh, 50, 45); // 5 available

        $this->actingAs($admin)->post('/admin/pos', [
            'subtotal' => 50, 'total_amount' => 50, 'paid_amount' => 50,
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product->id, 'product_name' => $product->name,
                 'quantity' => 5, 'unit_price' => 10, 'total' => 50],
            ],
        ]);

        $this->assertEquals(45, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $wh->id)->value('quantity'));
        $this->assertEquals(45, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $wh->id)->value('reserved_quantity'));
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'type' => 'out']);
    }

    // ═══ RESERVATION SERVICE ═══

    public function test_excessive_reservation_rejected(): void
    {
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct();
        $this->seedStock($product, $wh, 10, 0);

        $this->expectException(\RuntimeException::class);
        app(\App\Services\InventoryService::class)->reserveStock($product->id, $wh->id, 15);
    }

    public function test_competing_reservations_cannot_exceed_available(): void
    {
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct();
        $this->seedStock($product, $wh, 10, 0);
        $inv = app(\App\Services\InventoryService::class);

        $inv->reserveStock($product->id, $wh->id, 7); // 7 reserved, 3 left

        $this->expectException(\RuntimeException::class);
        $inv->reserveStock($product->id, $wh->id, 5); // only 3 available
    }

    public function test_double_release_floored_at_zero(): void
    {
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct();
        $this->seedStock($product, $wh, 50, 10);
        $inv = app(\App\Services\InventoryService::class);

        $inv->releaseReservation($product->id, $wh->id, 10);
        $inv->releaseReservation($product->id, $wh->id, 10); // second release → floor at 0

        $this->assertEquals(0, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $wh->id)->value('reserved_quantity'));
    }

    public function test_consume_more_than_reserved_rejected(): void
    {
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct();
        $this->seedStock($product, $wh, 50, 10);

        $this->expectException(\RuntimeException::class);
        app(\App\Services\InventoryService::class)->consumeReserved($product->id, $wh->id, 20);
    }

    // ═══ PARTIAL EXECUTE VALIDATION ═══

    public function test_zero_and_negative_execution_rejected(): void
    {
        $creator = $this->makeUser();
        $from = $this->makeWarehouse(); $to = Warehouse::withoutGlobalScopes()->create([
            'name' => 'W2', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $this->company()->id, 'is_active' => true,
        ]);
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 50);

        $this->actingAs($creator);
        $transfer = Transfer::create([
            'from_warehouse' => $from->id, 'to_warehouse' => $to->id, 'product_id' => $product->id,
            'product_name' => $product->name, 'quantity' => 20, 'status' => 'approved',
            'created_by' => $creator->id, 'company_id' => $this->company()->id,
        ]);
        $executor = $this->makeUser();

        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute", ['quantity' => 0])->assertSessionHasErrors('quantity');
        $this->actingAs($executor)->post("/admin/transfers/{$transfer->id}/execute", ['quantity' => -5])->assertSessionHasErrors('quantity');
        $this->assertEquals(0, StockMovement::count());
    }

    // ═══ STALE RESERVATION RECONCILIATION ═══

    public function test_audit_flags_orphaned_reservation(): void
    {
        $admin = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct();
        // reserved>0 but no open transfer → orphaned
        $this->seedStock($product, $wh, 50, 15);

        $response = $this->actingAs($admin)->get('/admin/inventory/audit');
        $response->assertStatus(200);
        $response->assertSee('Stale Reservations');
        $response->assertSee('15 reserved');
    }

    public function test_manual_release_clears_reservation_with_audit(): void
    {
        $admin = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct();
        $this->seedStock($product, $wh, 50, 15);

        $this->actingAs($admin)->post('/admin/inventory/release-reservation', [
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'reason' => 'Stale release',
        ])->assertRedirect();

        $this->assertEquals(0, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $wh->id)->value('reserved_quantity'));
        $this->assertDatabaseHas('audit_logs', ['module' => 'inventory', 'module_action' => 'reservation_released']);

        // Idempotent — second release is a no-op, not an error
        $this->actingAs($admin)->post('/admin/inventory/release-reservation', [
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'reason' => 'Retry',
        ])->assertRedirect();
        $this->assertEquals(0, ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $wh->id)->value('reserved_quantity'));
    }

    public function test_open_transfer_reservation_not_flagged_stale(): void
    {
        $admin = $this->makeUser();
        $from = $this->makeWarehouse();
        $to = Warehouse::withoutGlobalScopes()->create([
            'name' => 'W2', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $this->company()->id, 'is_active' => true,
        ]);
        $product = $this->makeProduct();
        $this->seedStock($product, $from, 50, 20);

        // Open approved transfer claims this reservation → NOT stale
        $this->actingAs($admin);
        Transfer::create([
            'from_warehouse' => $from->id, 'to_warehouse' => $to->id, 'product_id' => $product->id,
            'product_name' => $product->name, 'quantity' => 20, 'transferred_quantity' => 0,
            'status' => 'approved', 'created_by' => $admin->id, 'company_id' => $this->company()->id,
        ]);

        $response = $this->get('/admin/inventory/audit');
        $response->assertStatus(200);
        $response->assertDontSee('No open transfer claims this reservation');
    }
}
