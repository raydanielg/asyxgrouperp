<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name', 'code', 'head_name', 'phone', 'email', 'description', 'status',
    ];
}
