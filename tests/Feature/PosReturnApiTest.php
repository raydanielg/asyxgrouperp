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
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PosReturnApiTest extends TestCase
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

    private function makeSaleWithItem(User $cashier, float $qty = 10, float $price = 10): array
    {
        Sanctum::actingAs($cashier);
        $sale = PosSale::create([
            'sale_number' => 'POS-' . uniqid(), 'subtotal' => $qty * $price,
            'total_amount' => $qty * $price, 'paid_amount' => $qty * $price,
            'payment_method' => 'cash', 'status' => 'completed', 'cashier_id' => $cashier->id,
            'company_id' => $this->company()->id,
        ]);
        $item = PosSaleItem::create([
            'pos_sale_id' => $sale->id, 'product_name' => 'Widget',
            'quantity' => $qty, 'unit_price' => $price, 'total' => $qty * $price,
        ]);
        return [$sale, $item];
    }

    private function stockableSetup(): array
    {
        $wh = Warehouse::withoutGlobalScopes()->create([
            'name' => 'WH', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $this->company()->id, 'is_active' => true,
        ]);
        $product = Product::withoutGlobalScopes()->create([
            'name' => 'P', 'product_code' => 'P-' . uniqid(), 'warehouse_id' => $wh->id,
            'company_id' => $this->company()->id, 'is_active' => true, 'stock_quantity' => 40,
        ]);
        ProductWarehouseStock::withoutGlobalScopes()->create([
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'quantity' => 40, 'reserved_quantity' => 0,
        ]);
        return [$wh, $product];
    }

    public function test_api_return_created_and_processed(): void
    {
        $cashier = $this->makeUser();
        $processor = $this->makeUser();
        [$wh, $product] = $this->stockableSetup();
        [$sale, $item] = $this->makeSaleWithItem($cashier, 10);
        $item->update(['product_id' => $product->id]);

        // Create return via API
        $response = $this->postJson("/api/pos/sales/{$sale->id}/returns", [
            'reason' => 'defective', 'refund_amount' => 30,
            'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'restock']],
        ]);
        $response->assertStatus(201);
        $returnId = $response->json('id');
        $this->assertEquals('pending', $response->json('status'));

        // Processor processes — stock restored
        Sanctum::actingAs($processor);
        $this->postJson("/api/pos/returns/{$returnId}/process")->assertOk();

        $stock = ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $wh->id)->first();
        $this->assertEquals(43, $stock->quantity);
        $this->assertEquals('awaiting_reconciliation', PosReturn::find($returnId)->refund_status);
    }

    public function test_api_creator_cannot_process_own_return(): void
    {
        $cashier = $this->makeUser();
        [$sale, $item] = $this->makeSaleWithItem($cashier, 10);

        $resp = $this->postJson("/api/pos/sales/{$sale->id}/returns", [
            'reason' => 'x', 'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'restock']],
        ]);
        $returnId = $resp->json('id');

        $this->postJson("/api/pos/returns/{$returnId}/process")->assertStatus(403);
    }

    public function test_api_over_return_rejected(): void
    {
        $cashier = $this->makeUser();
        [$sale, $item] = $this->makeSaleWithItem($cashier, 10);

        $this->postJson("/api/pos/sales/{$sale->id}/returns", [
            'reason' => 'x', 'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 15, 'disposition' => 'restock']],
        ])->assertStatus(422);
        $this->assertEquals(0, PosReturn::count());
    }

    public function test_api_foreign_sale_item_rejected(): void
    {
        $cashier = $this->makeUser();
        [$saleA] = $this->makeSaleWithItem($cashier, 10);
        [$saleB, $itemB] = $this->makeSaleWithItem($cashier, 10);

        $this->postJson("/api/pos/sales/{$saleA->id}/returns", [
            'reason' => 'x', 'items' => [['pos_sale_item_id' => $itemB->id, 'quantity' => 1, 'disposition' => 'restock']],
        ])->assertStatus(422); // item belongs to another sale
    }

    public function test_api_damaged_return_no_stock_increase(): void
    {
        $cashier = $this->makeUser();
        $processor = $this->makeUser();
        [$wh, $product] = $this->stockableSetup();
        [$sale, $item] = $this->makeSaleWithItem($cashier, 10);
        $item->update(['product_id' => $product->id]);

        $resp = $this->postJson("/api/pos/sales/{$sale->id}/returns", [
            'reason' => 'damaged', 'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'damaged']],
        ]);
        $returnId = $resp->json('id');

        Sanctum::actingAs($processor);
        $this->postJson("/api/pos/returns/{$returnId}/process")->assertOk();

        $stock = ProductWarehouseStock::where('product_id', $product->id)->where('warehouse_id', $wh->id)->first();
        $this->assertEquals(40, $stock->quantity); // unchanged
        $this->assertEquals(0, StockMovement::count());
    }

    public function test_api_refund_confirm_separate_and_audited(): void
    {
        $cashier = $this->makeUser();
        $processor = $this->makeUser();
        $confirmer = $this->makeUser();
        [$wh, $product] = $this->stockableSetup();
        [$sale, $item] = $this->makeSaleWithItem($cashier, 10);
        $item->update(['product_id' => $product->id]);

        $resp = $this->postJson("/api/pos/sales/{$sale->id}/returns", [
            'reason' => 'x', 'refund_amount' => 30,
            'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'restock']],
        ]);
        $returnId = $resp->json('id');

        Sanctum::actingAs($processor);
        $this->postJson("/api/pos/returns/{$returnId}/process")->assertOk();
        $this->assertEquals('awaiting_reconciliation', PosReturn::find($returnId)->refund_status);

        Sanctum::actingAs($confirmer);
        $this->postJson("/api/pos/returns/{$returnId}/confirm-refund", [
            'refund_reference' => 'CASH-001',
        ])->assertOk();
        $this->assertEquals('confirmed', PosReturn::find($returnId)->refund_status);
        // Double confirm rejected
        $this->postJson("/api/pos/returns/{$returnId}/confirm-refund")->assertStatus(403);
    }

    public function test_api_unauthenticated_rejected(): void
    {
        $this->postJson("/api/pos/sales/999/returns", [
            'reason' => 'x', 'items' => [['pos_sale_item_id' => 1, 'quantity' => 1, 'disposition' => 'restock']],
        ])->assertStatus(401);
    }
}
