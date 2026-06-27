<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function lpo()
    {
        return $this->belongsTo(Lpo::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
