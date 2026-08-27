<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfqResponse extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'response_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    public function supplier()
    {
        return $this->belongsTo(RfqSupplier::class, 'rfq_supplier_id');
    }

    public function items()
    {
        return $this->hasMany(RfqResponseItem::class);
    }
}
