<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductWarehouseStock extends Model
{
    protected $table = 'product_warehouse_stock';

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function availableQuantity(): float
    {
        return (float) $this->quantity - (float) $this->reserved_quantity;
    }
}
