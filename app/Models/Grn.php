<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function lpo()
    {
        return $this->belongsTo(Lpo::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(GrnItem::class);
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
