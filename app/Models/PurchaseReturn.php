<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $fillable = ['return_number', 'return_date', 'vendor_id', 'warehouse_id', 'original_invoice_id', 'reason', 'subtotal', 'tax_amount', 'discount_amount', 'total_amount', 'status', 'notes', 'creator_id', 'created_by'];

    public function vendor() { return $this->belongsTo(User::class, 'vendor_id'); }
    public function originalInvoice() { return $this->belongsTo(PurchaseInvoice::class, 'original_invoice_id'); }
    public function items() { return $this->hasMany(PurchaseReturnItem::class, 'return_id'); }
}
