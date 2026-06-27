<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrEvent extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['event_date' => 'date'];
}
