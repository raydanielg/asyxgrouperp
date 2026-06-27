<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = ['user_id', 'ip', 'date', 'details', 'type', 'created_by'];

    protected $casts = ['details' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
}
