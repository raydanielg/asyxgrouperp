<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosReturnItem extends Model
{
    protected $guarded = ['id'];

    public function posReturn()
    {
        return $this->belongsTo(PosReturn::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(PosSaleItem::class, 'pos_sale_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
