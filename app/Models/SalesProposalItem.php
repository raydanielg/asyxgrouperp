<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesProposalItem extends Model
{
    protected $fillable = ['proposal_id', 'product_name', 'quantity', 'unit_price', 'discount_percentage', 'discount_amount', 'tax_percentage', 'tax_amount', 'total_amount'];

    public function proposal() { return $this->belongsTo(SalesProposal::class, 'proposal_id'); }
}
