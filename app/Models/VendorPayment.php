<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function vendorInvoice()
    {
        return $this->belongsTo(VendorInvoice::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
