<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Policy extends Model
{
    use BelongsToCompany;
    protected $guarded = ['id'];

    protected $casts = ['is_active' => 'boolean'];
}
