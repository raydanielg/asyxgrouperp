<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpdeskCategory extends Model
{
    protected $fillable = ['name', 'description', 'color', 'is_active', 'creator_id', 'created_by'];

    protected $casts = ['is_active' => 'boolean'];

    public function tickets() { return $this->hasMany(HelpdeskTicket::class, 'category_id'); }
}
