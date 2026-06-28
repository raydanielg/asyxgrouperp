<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    use BelongsToCompany;

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
