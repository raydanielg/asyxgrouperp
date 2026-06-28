<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $fillable = ['company_id', 'invoice_number', 'invoice_date', 'due_date', 'customer_id', 'warehouse_id', 'subtotal', 'tax_amount', 'discount_amount', 'total_amount', 'paid_amount', 'balance_amount', 'status', 'type', 'payment_terms', 'notes', 'creator_id', 'created_by'];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(SalesInvoiceItem::class, 'invoice_id'); }
    public function creator() { return $this->belongsTo(User::class, 'creator_id'); }
}
