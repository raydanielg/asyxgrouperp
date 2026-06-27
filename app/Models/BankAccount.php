<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['is_active' => 'boolean'];
}
