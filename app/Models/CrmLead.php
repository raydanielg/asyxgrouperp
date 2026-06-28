<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CrmLead extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    public function deals()
    {
        return $this->hasMany(CrmDeal::class);
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
