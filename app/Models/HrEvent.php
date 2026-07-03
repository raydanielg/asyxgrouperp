<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class HrEvent extends Model
{
    use BelongsToCompany;
    protected $guarded = ['id'];

    protected $casts = ['event_date' => 'date'];
}
