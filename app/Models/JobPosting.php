<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class JobPosting extends Model
{
    use BelongsToCompany;
    protected $guarded = ['id'];

    protected $casts = ['deadline' => 'date'];
}
