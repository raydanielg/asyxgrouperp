<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'estimate_date' => 'date',
        'expiry_date' => 'date',
    ];
}
