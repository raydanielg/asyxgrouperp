<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    protected $fillable = ['module', 'name', 'monthly_price', 'yearly_price', 'image', 'is_enable', 'package_name', 'priority'];

    protected $casts = ['is_enable' => 'boolean'];
}
