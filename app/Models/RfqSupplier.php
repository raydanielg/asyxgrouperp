<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfqSupplier extends Model
{
    protected $guarded = ['id'];

    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function response()
    {
        return $this->hasOne(RfqResponse::class);
    }
}
