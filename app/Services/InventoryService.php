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
            $stock = ProductWarehouseStock::lockForUpdate()->find($stock->id);

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
                ->lockForUpdate()
                ->firstOrFail();

            // Revalidate inside the lock — concurrent requests cannot oversell
            $available = $stock->quantity - ($stock->reserved_quantity ?? 0);
            if ($quantity > $available) {
                throw new RuntimeException("Insufficient stock. Available: {$available}, requested: {$quantity}.");
            }

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

    public function transferStock(int $productId, int $fromWarehouseId, int $toWarehouseId, float $quantity, ?int $createdBy = null, ?string $reference = null): void
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

        DB::transaction(function () use ($productId, $fromWarehouseId, $toWarehouseId, $quantity, $createdBy, $reference) {
            $fromStock = ProductWarehouseStock::where('product_id', $productId)
                ->where('warehouse_id', $fromWarehouseId)
                ->lockForUpdate()
                ->firstOrFail();

            // Revalidate inside the lock — concurrent transfers cannot oversell
            $available = $fromStock->quantity - ($fromStock->reserved_quantity ?? 0);
            if ($quantity > $available) {
                throw new RuntimeException("Insufficient stock at source. Available: {$available}, requested: {$quantity}.");
            }

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
                'reference' => ($reference ? $reference . ' — ' : '') . "Transfer to warehouse #{$toWarehouseId}",
                'created_by' => $createdBy ?? auth()->id(),
            ]);

            StockMovement::create([
                'product_id' => $productId,
                'warehouse_id' => $toWarehouseId,
                'type' => 'transfer_in',
                'quantity' => (int) $quantity,
                'balance_after' => (int) $toStock->fresh()->quantity,
                'reference' => ($reference ? $reference . ' — ' : '') . "Transfer from warehouse #{$fromWarehouseId}",
                'created_by' => $createdBy ?? auth()->id(),
            ]);
        });
    }

    /**
     * Reserve stock for an eligible operation — locks the row and
     * revalidates availability inside the transaction so concurrent
     * reservations cannot exceed available stock.
     */
    public function reserveStock(int $productId, int $warehouseId, float $quantity): ProductWarehouseStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Reserve quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($productId, $warehouseId, $quantity) {
            $stock = ProductWarehouseStock::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->lockForUpdate()
                ->firstOrFail();

            $available = $stock->quantity - $stock->reserved_quantity;
            if ($quantity > $available) {
                throw new RuntimeException("Insufficient available stock. Available: {$available}, requested: {$quantity}.");
            }

            $stock->increment('reserved_quantity', $quantity);
            return $stock->fresh();
        });
    }

    /**
     * Release a reservation — floored at zero so double-release can never
     * create negative reservations.
     */
    public function releaseReservation(int $productId, int $warehouseId, float $quantity): ProductWarehouseStock
    {
        return DB::transaction(function () use ($productId, $warehouseId, $quantity) {
            $stock = ProductWarehouseStock::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->lockForUpdate()
                ->firstOrFail();

            $newReserved = max(0, $stock->reserved_quantity - $quantity);
            $stock->update(['reserved_quantity' => $newReserved]);

            return $stock->fresh();
        });
    }

    /**
     * Consume reserved stock — decrements reserved AND on-hand atomically.
     * Used when an operation physically takes its reserved stock (e.g.
     * transfer execution moves qty to the destination afterwards).
     * Returns the decremented stock row. Must be called inside the
     * caller's transaction.
     */
    public function consumeReserved(int $productId, int $warehouseId, float $quantity): ProductWarehouseStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Consume quantity must be greater than zero.');
        }

        $stock = ProductWarehouseStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->firstOrFail();

        if ($quantity > $stock->reserved_quantity + 0.0001) {
            throw new RuntimeException("Cannot consume {$quantity} — only {$stock->reserved_quantity} is reserved.");
        }
        if ($quantity > $stock->quantity + 0.0001) {
            throw new RuntimeException("Insufficient on-hand stock to consume {$quantity}.");
        }

        $stock->decrement('reserved_quantity', $quantity);
        $stock->decrement('quantity', $quantity);

        $this->syncProductTotalStock($productId);
        return $stock->fresh();
    }

    protected function syncProductTotalStock(int $productId): void
    {
        $total = $this->getTotalStock($productId);
        Product::where('id', $productId)->update(['stock_quantity' => $total]);
    }
}
