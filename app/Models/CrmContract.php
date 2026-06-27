<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmContract extends Model
{
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
