<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceReturnItem extends Model
{
    protected $fillable = ['return_id', 'product_name', 'quantity', 'unit_price', 'total_amount'];

    public function salesReturn() { return $this->belongsTo(SalesInvoiceReturn::class, 'return_id'); }
}
