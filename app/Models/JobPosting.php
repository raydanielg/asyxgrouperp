<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['deadline' => 'date'];
}
