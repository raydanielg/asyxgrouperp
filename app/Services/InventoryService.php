<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductWarehouseStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function getStock(int $productId, int $warehouseId): float
    {
        $stock = ProductWarehouseStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        return $stock ? (float) $stock->quantity : 0;
    }

    public function getTotalStock(int $productId): float
    {
        return (float) ProductWarehouseStock::where('product_id', $productId)->sum('quantity');
    }

    public function getAvailableStock(int $productId, int $warehouseId): float
    {
        $stock = ProductWarehouseStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        return $stock ? $stock->availableQuantity() : 0;
    }

    public function addStock(int $productId, int $warehouseId, float $quantity, string $reason = 'restock', ?int $createdBy = null): ProductWarehouseStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $reason, $createdBy) {
            $stock = ProductWarehouseStock::firstOrCreate(
                ['product_id' => $productId, 'warehouse_id' => $warehouseId],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );

            $stock->increment('quantity', $quantity);

            StockMovement::create([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'type' => 'in',
                'quantity' => (int) $quantity,
                'balance_after' => (int) $stock->fresh()->quantity,
                'reference' => $reason,
                'created_by' => $createdBy ?? auth()->id(),
            ]);

            $this->syncProductTotalStock($productId);

            return $stock->fresh();
        });
    }

    public function removeStock(int $productId, int $warehouseId, float $quantity, string $reason = 'sale', ?int $createdBy = null): ProductWarehouseStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Quantity must be greater than zero.');
        }

        $available = $this->getAvailableStock($productId, $warehouseId);
        if ($quantity > $available) {
            throw new RuntimeException("Insufficient stock. Available: {$available}, requested: {$quantity}.");
        }

        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $reason, $createdBy) {
            $stock = ProductWarehouseStock::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->firstOrFail();

            $stock->decrement('quantity', $quantity);

            StockMovement::create([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'type' => 'out',
                'quantity' => (int) $quantity,
                'balance_after' => (int) $stock->fresh()->quantity,
                'reference' => $reason,
                'created_by' => $createdBy ?? auth()->id(),
            ]);

            $this->syncProductTotalStock($productId);

            return $stock->fresh();
        });
    }

    public function transferStock(int $productId, int $fromWarehouseId, int $toWarehouseId, float $quantity, ?int $createdBy = null): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Transfer quantity must be greater than zero.');
        }

        if ($fromWarehouseId === $toWarehouseId) {
            throw new RuntimeException('Source and destination warehouses must be different.');
        }

        $available = $this->getAvailableStock($productId, $fromWarehouseId);
        if ($quantity > $available) {
            throw new RuntimeException("Insufficient stock at source. Available: {$available}, requested: {$quantity}.");
        }

        DB::transaction(function () use ($productId, $fromWarehouseId, $toWarehouseId, $quantity, $createdBy) {
            $fromStock = ProductWarehouseStock::where('product_id', $productId)
                ->where('warehouse_id', $fromWarehouseId)
                ->firstOrFail();

            $toStock = ProductWarehouseStock::firstOrCreate(
                ['product_id' => $productId, 'warehouse_id' => $toWarehouseId],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );

            $fromStock->decrement('quantity', $quantity);
            $toStock->increment('quantity', $quantity);

            StockMovement::create([
                'product_id' => $productId,
                'warehouse_id' => $fromWarehouseId,
                'type' => 'transfer_out',
                'quantity' => (int) $quantity,
                'balance_after' => (int) $fromStock->fresh()->quantity,
                'reference' => "Transfer to warehouse #{$toWarehouseId}",
                'created_by' => $createdBy ?? auth()->id(),
            ]);

            StockMovement::create([
                'product_id' => $productId,
                'warehouse_id' => $toWarehouseId,
                'type' => 'transfer_in',
                'quantity' => (int) $quantity,
                'balance_after' => (int) $toStock->fresh()->quantity,
                'reference' => "Transfer from warehouse #{$fromWarehouseId}",
                'created_by' => $createdBy ?? auth()->id(),
            ]);
        });
    }

    public function reserveStock(int $productId, int $warehouseId, float $quantity): ProductWarehouseStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Reserve quantity must be greater than zero.');
        }

        $available = $this->getAvailableStock($productId, $warehouseId);
        if ($quantity > $available) {
            throw new RuntimeException("Insufficient available stock. Available: {$available}, requested: {$quantity}.");
        }

        $stock = ProductWarehouseStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->firstOrFail();

        $stock->increment('reserved_quantity', $quantity);

        return $stock->fresh();
    }

    public function releaseReservation(int $productId, int $warehouseId, float $quantity): ProductWarehouseStock
    {
        $stock = ProductWarehouseStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->firstOrFail();

        $stock->decrement('reserved_quantity', $quantity);

        return $stock->fresh();
    }

    protected function syncProductTotalStock(int $productId): void
    {
        $total = $this->getTotalStock($productId);
        Product::where('id', $productId)->update(['stock_quantity' => $total]);
    }
}
