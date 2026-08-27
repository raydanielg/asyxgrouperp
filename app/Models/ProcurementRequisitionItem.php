<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementRequisitionItem extends Model
{
    protected $guarded = ['id'];

    public function requisition()
    {
        return $this->belongsTo(ProcurementRequisition::class, 'requisition_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
