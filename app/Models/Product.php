<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = ['is_active' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function warehouseStocks()
    {
        return $this->hasMany(ProductWarehouseStock::class);
    }

    public function stockInWarehouse(int $warehouseId): float
    {
        $stock = $this->warehouseStocks()->where('warehouse_id', $warehouseId)->first();
        return $stock ? (float) $stock->quantity : 0;
    }
}
