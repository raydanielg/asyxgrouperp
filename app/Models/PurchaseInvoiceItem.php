<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'product_name', 'quantity', 'unit_price', 'discount_percentage', 'discount_amount', 'tax_percentage', 'tax_amount', 'total_amount'];

    public function invoice() { return $this->belongsTo(PurchaseInvoice::class, 'invoice_id'); }
}
