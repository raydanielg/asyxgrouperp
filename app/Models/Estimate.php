<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Estimate extends Model
{
    use BelongsToCompany;
    protected $guarded = ['id'];

    protected $casts = [
        'estimate_date' => 'date',
        'expiry_date' => 'date',
    ];
}
