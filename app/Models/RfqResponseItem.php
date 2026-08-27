<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfqResponseItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function response()
    {
        return $this->belongsTo(RfqResponse::class);
    }

    public function requisitionItem()
    {
        return $this->belongsTo(ProcurementRequisitionItem::class);
    }
}
