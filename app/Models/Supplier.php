<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = ['is_active' => 'boolean'];

    public function lpos()
    {
        return $this->hasMany(Lpo::class);
    }

    public function vendorInvoices()
    {
        return $this->hasMany(VendorInvoice::class);
    }

    public function vendorPayments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function grns()
    {
        return $this->hasMany(Grn::class);
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
    }
}
