<?php

namespace Tests\Unit;

use App\Services\InventoryService;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductWarehouseStock;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createWarehouse(string $name): Warehouse
    {
        return Warehouse::create([
            'name' => $name,
            'address' => '123 Test St',
            'city' => 'Test City',
            'zip_code' => '12345',
        ]);
    }

    private function createProduct(string $sku, string $code): Product
    {
        return Product::create([
            'name' => 'Test Product',
            'sku' => $sku,
            'product_code' => $code,
            'stock_quantity' => 0,
        ]);
    }

    public function test_add_stock_creates_warehouse_balance(): void
    {
        $service = app(InventoryService::class);
        $product = $this->createProduct('TEST001', 'PRD001');
        $warehouse = $this->createWarehouse('Main Warehouse');

        $service->addStock($product->id, $warehouse->id, 100);

        $this->assertEquals(100, $service->getStock($product->id, $warehouse->id));
        $this->assertEquals(100, $service->getTotalStock($product->id));
    }

    public function test_remove_stock_decreases_balance(): void
    {
        $service = app(InventoryService::class);
        $product = $this->createProduct('TEST002', 'PRD002');
        $warehouse = $this->createWarehouse('Main Warehouse');

        $service->addStock($product->id, $warehouse->id, 100);
        $service->removeStock($product->id, $warehouse->id, 30);

        $this->assertEquals(70, $service->getStock($product->id, $warehouse->id));
    }

    public function test_remove_stock_insufficient_throws(): void
    {
        $service = app(InventoryService::class);
        $product = $this->createProduct('TEST003', 'PRD003');
        $warehouse = $this->createWarehouse('Main Warehouse');

        $service->addStock($product->id, $warehouse->id, 50);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Insufficient stock');

        $service->removeStock($product->id, $warehouse->id, 100);
    }

    public function test_transfer_stock_between_warehouses(): void
    {
        $service = app(InventoryService::class);
        $product = $this->createProduct('TEST004', 'PRD004');
        $wh1 = $this->createWarehouse('Warehouse 1');
        $wh2 = $this->createWarehouse('Warehouse 2');

        $service->addStock($product->id, $wh1->id, 100);
        $service->transferStock($product->id, $wh1->id, $wh2->id, 40);

        $this->assertEquals(60, $service->getStock($product->id, $wh1->id));
        $this->assertEquals(40, $service->getStock($product->id, $wh2->id));
        $this->assertEquals(100, $service->getTotalStock($product->id));
    }
}
