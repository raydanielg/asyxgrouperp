<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\PosReturn;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PosReturnTest extends TestCase
{
    use RefreshDatabase;

    private function company(): Company
    {
        return Company::firstOrCreate(
            ['name' => 'Test Co'],
            ['legal_name' => 'Test Co Ltd', 'short_code' => 'TST', 'is_active' => true]
        );
    }

    private function makeUser(): User
    {
        return User::create([
            'name' => 'U' . uniqid(),
            'email' => 'u' . uniqid() . '@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'company_id' => $this->company()->id,
            'is_enable_login' => true,
        ]);
    }

    private function makeWarehouse(): Warehouse
    {
        return Warehouse::withoutGlobalScopes()->create([
            'name' => 'WH', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $this->company()->id, 'is_active' => true,
        ]);
    }

    private function makeProduct(?int $whId = null): Product
    {
        return Product::withoutGlobalScopes()->create([
            'name' => 'P-X', 'product_code' => 'P-' . uniqid(), 'warehouse_id' => $whId,
            'company_id' => $this->company()->id, 'is_active' => true, 'stock_quantity' => 50,
        ]);
    }

    private function makeSale(User $cashier, Product $product, float $qty = 10, float $price = 10): array
    {
        $this->actingAs($cashier);
        $sale = PosSale::create([
            'sale_number' => 'POS-' . uniqid(), 'subtotal' => $qty * $price,
            'total_amount' => $qty * $price, 'paid_amount' => $qty * $price,
            'payment_method' => 'cash', 'status' => 'completed', 'cashier_id' => $cashier->id,
            'company_id' => $this->company()->id,
        ]);
        $item = PosSaleItem::create([
            'pos_sale_id' => $sale->id, 'product_id' => $product->id,
            'product_name' => $product->name, 'quantity' => $qty,
            'unit_price' => $price, 'total' => $qty * $price,
        ]);
        return [$sale, $item];
    }

    private function stockAt(Product $p, Warehouse $w): float
    {
        return (float) (ProductWarehouseStock::where('product_id', $p->id)->where('warehouse_id', $w->id)->value('quantity') ?? 0);
    }

    public function test_restocked_return_restores_stock(): void
    {
        $cashier = $this->makeUser();
        $processor = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct($wh->id);
        ProductWarehouseStock::withoutGlobalScopes()->create([
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'quantity' => 40, 'reserved_quantity' => 0,
        ]);
        [$sale, $item] = $this->makeSale($cashier, $product, 10);

        // Cashier requests return of 3 units — restock
        $this->post("/admin/pos-sales/{$sale->id}/returns", [
            'reason' => 'Customer exchange',
            'refund_amount' => 30,
            'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'restock']],
        ])->assertRedirect();

        $return = PosReturn::latest('id')->first();
        $this->assertEquals('pending', $return->status);
        $this->assertEquals(40, $this->stockAt($product, $wh)); // no stock yet — pending

        // Processor finalizes
        $this->actingAs($processor)->post("/admin/pos-returns/{$return->id}/process")->assertRedirect();

        $this->assertEquals(43, $this->stockAt($product, $wh));
        $this->assertEquals('processed', $return->fresh()->status);
        // Internal record — awaiting manual reconciliation, NOT externally confirmed
        $this->assertEquals('awaiting_reconciliation', $return->fresh()->refund_status);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id, 'type' => 'in', 'quantity' => 3,
        ]);
        $this->assertStringContainsString('POS_RETURN', StockMovement::latest('id')->first()->reference);
    }

    public function test_damaged_return_does_not_restore_stock(): void
    {
        $cashier = $this->makeUser();
        $processor = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct($wh->id);
        ProductWarehouseStock::withoutGlobalScopes()->create([
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'quantity' => 40, 'reserved_quantity' => 0,
        ]);
        [$sale, $item] = $this->makeSale($cashier, $product, 10);

        $this->post("/admin/pos-sales/{$sale->id}/returns", [
            'reason' => 'Damaged goods',
            'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'damaged']],
        ]);

        $return = PosReturn::latest('id')->first();
        $this->actingAs($processor)->post("/admin/pos-returns/{$return->id}/process")->assertRedirect();

        $this->assertEquals(40, $this->stockAt($product, $wh)); // no restoration
        $this->assertEquals(0, StockMovement::count());
        $this->assertEquals('processed', $return->fresh()->status);
    }

    public function test_return_exceeding_sold_quantity_rejected(): void
    {
        $cashier = $this->makeUser();
        $product = $this->makeProduct();
        [$sale, $item] = $this->makeSale($cashier, $product, 10);

        $this->post("/admin/pos-sales/{$sale->id}/returns", [
            'reason' => 'Over-return',
            'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 15, 'disposition' => 'restock']],
        ]);

        $this->assertEquals(0, PosReturn::count());
    }

    public function test_processed_return_cannot_be_processed_twice(): void
    {
        $cashier = $this->makeUser();
        $processor = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct($wh->id);
        ProductWarehouseStock::withoutGlobalScopes()->create([
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'quantity' => 40, 'reserved_quantity' => 0,
        ]);
        [$sale, $item] = $this->makeSale($cashier, $product, 10);

        $this->post("/admin/pos-sales/{$sale->id}/returns", [
            'reason' => 'x', 'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'restock']],
        ]);
        $return = PosReturn::latest('id')->first();

        $this->actingAs($processor)->post("/admin/pos-returns/{$return->id}/process")->assertRedirect();
        $this->actingAs($processor)->post("/admin/pos-returns/{$return->id}/process")->assertStatus(403);

        $this->assertEquals(43, $this->stockAt($product, $wh)); // stock restored once
    }

    public function test_creator_cannot_process_own_return(): void
    {
        $cashier = $this->makeUser();
        $product = $this->makeProduct($this->makeWarehouse()->id);
        [$sale, $item] = $this->makeSale($cashier, $product, 10);

        $this->post("/admin/pos-sales/{$sale->id}/returns", [
            'reason' => 'x', 'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'restock']],
        ]);
        $return = PosReturn::latest('id')->first();

        $this->actingAs($cashier)->post("/admin/pos-returns/{$return->id}/process")->assertStatus(403);
    }

    public function test_refund_exceeding_paid_amount_rejected(): void
    {
        $cashier = $this->makeUser();
        $processor = $this->makeUser();
        $product = $this->makeProduct($this->makeWarehouse()->id);
        ProductWarehouseStock::withoutGlobalScopes()->create([
            'product_id' => $product->id, 'warehouse_id' => $product->warehouse_id, 'quantity' => 40, 'reserved_quantity' => 0,
        ]);
        [$sale, $item] = $this->makeSale($cashier, $product, 10, 10); // paid 100

        $this->post("/admin/pos-sales/{$sale->id}/returns", [
            'reason' => 'x', 'refund_amount' => 150, // exceeds paid
            'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'restock']],
        ]);
        $return = PosReturn::latest('id')->first();

        $this->actingAs($processor)->post("/admin/pos-returns/{$return->id}/process");
        $this->assertNotEquals('processed', $return->fresh()->status); // rejected
        $this->assertEquals('pending', $return->fresh()->status);
    }

    public function test_processed_return_is_immutable(): void
    {
        $cashier = $this->makeUser();
        $processor = $this->makeUser();
        $product = $this->makeProduct($this->makeWarehouse()->id);
        ProductWarehouseStock::withoutGlobalScopes()->create([
            'product_id' => $product->id, 'warehouse_id' => $product->warehouse_id, 'quantity' => 40, 'reserved_quantity' => 0,
        ]);
        [$sale, $item] = $this->makeSale($cashier, $product, 10);

        $this->post("/admin/pos-sales/{$sale->id}/returns", [
            'reason' => 'x', 'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'restock']],
        ]);
        $return = PosReturn::latest('id')->first();
        $this->actingAs($processor)->post("/admin/pos-returns/{$return->id}/process");

        $this->actingAs($processor)->delete("/admin/pos-returns/{$return->id}")->assertStatus(403);
        $this->assertDatabaseHas('pos_returns', ['id' => $return->id]);
    }

    public function test_original_sale_movements_unchanged(): void
    {
        $cashier = $this->makeUser();
        $processor = $this->makeUser();
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct($wh->id);
        ProductWarehouseStock::withoutGlobalScopes()->create([
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'quantity' => 40, 'reserved_quantity' => 0,
        ]);
        [$sale, $item] = $this->makeSale($cashier, $product, 10);

        // Original sale movement exists
        StockMovement::create([
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'type' => 'out',
            'quantity' => 10, 'balance_after' => 40, 'reference' => "POS {$sale->sale_number}",
            'created_by' => $cashier->id, 'company_id' => $this->company()->id,
        ]);

        $this->post("/admin/pos-sales/{$sale->id}/returns", [
            'reason' => 'x', 'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'restock']],
        ]);
        $return = PosReturn::latest('id')->first();
        $this->actingAs($processor)->post("/admin/pos-returns/{$return->id}/process");

        // Original out-movement is unchanged; return adds an 'in' movement
        $this->assertDatabaseHas('stock_movements', [
            'reference' => "POS {$sale->sale_number}", 'type' => 'out', 'quantity' => 10,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'type' => 'in', 'quantity' => 3,
        ]);
    }
}
