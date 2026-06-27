<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable = ['return_id', 'product_name', 'quantity', 'unit_price', 'total_amount'];

    public function purchaseReturn() { return $this->belongsTo(PurchaseReturn::class, 'return_id'); }
}
