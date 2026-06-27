<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $fillable = ['invoice_number', 'invoice_date', 'due_date', 'vendor_id', 'warehouse_id', 'subtotal', 'tax_amount', 'discount_amount', 'total_amount', 'paid_amount', 'balance_amount', 'status', 'payment_terms', 'notes', 'creator_id', 'created_by'];

    public function vendor() { return $this->belongsTo(User::class, 'vendor_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(PurchaseInvoiceItem::class, 'invoice_id'); }
    public function creator() { return $this->belongsTo(User::class, 'creator_id'); }
}
