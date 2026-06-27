<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(CrmLead::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
