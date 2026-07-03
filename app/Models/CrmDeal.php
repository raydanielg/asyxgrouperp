<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class CrmDeal extends Model
{
    use BelongsToCompany;
    protected $guarded = ['id'];

    protected $casts = ['expected_close_date' => 'date'];

    public function lead()
    {
        return $this->belongsTo(CrmLead::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contracts()
    {
        return $this->hasMany(CrmContract::class, 'deal_id');
    }
}
