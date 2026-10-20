<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['name', 'from', 'module_name', 'subject', 'content', 'is_active', 'created_by', 'company_id'];

    protected $casts = ['is_active' => 'boolean'];
}
