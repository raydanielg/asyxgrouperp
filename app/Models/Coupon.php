<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['name', 'description', 'code', 'discount', 'limit', 'type', 'minimum_spend', 'maximum_spend', 'limit_per_user', 'expiry_date', 'included_module', 'excluded_module', 'status', 'created_by'];

    protected $casts = ['status' => 'boolean', 'expiry_date' => 'datetime', 'included_module' => 'array', 'excluded_module' => 'array'];
}
