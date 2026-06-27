<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceReturn extends Model
{
    protected $fillable = ['return_number', 'return_date', 'customer_id', 'warehouse_id', 'original_invoice_id', 'reason', 'subtotal', 'tax_amount', 'discount_amount', 'total_amount', 'status', 'notes', 'creator_id', 'created_by'];

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function originalInvoice() { return $this->belongsTo(SalesInvoice::class, 'original_invoice_id'); }
    public function items() { return $this->hasMany(SalesInvoiceReturnItem::class, 'return_id'); }
}
