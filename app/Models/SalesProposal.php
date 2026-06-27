<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesProposal extends Model
{
    protected $fillable = ['proposal_number', 'proposal_date', 'due_date', 'customer_id', 'warehouse_id', 'subtotal', 'tax_amount', 'discount_amount', 'total_amount', 'status', 'converted_to_invoice', 'payment_terms', 'notes', 'creator_id', 'created_by'];

    protected $casts = ['converted_to_invoice' => 'boolean'];

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(SalesProposalItem::class, 'proposal_id'); }
    public function creator() { return $this->belongsTo(User::class, 'creator_id'); }
}
