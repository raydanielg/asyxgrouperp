<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'description', 'number_of_users', 'status', 'free_plan', 'modules', 'package_price_yearly', 'package_price_monthly', 'storage_limit', 'trial', 'trial_days', 'created_by'];

    protected $casts = ['status' => 'boolean', 'free_plan' => 'boolean', 'trial' => 'boolean', 'modules' => 'array'];
}
