<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCardPart extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCard::class);
    }
}
