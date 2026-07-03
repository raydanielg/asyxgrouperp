<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class CrmContract extends Model
{
    use BelongsToCompany;
    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function deal()
    {
        return $this->belongsTo(CrmDeal::class);
    }
}
