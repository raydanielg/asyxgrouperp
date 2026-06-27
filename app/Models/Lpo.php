<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lpo extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'lpo_date' => 'date',
        'expected_delivery_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(LpoItem::class);
    }

    public function grns()
    {
        return $this->hasMany(Grn::class);
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
    }

    public function vendorInvoices()
    {
        return $this->hasMany(VendorInvoice::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
