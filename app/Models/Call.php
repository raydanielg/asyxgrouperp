<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = [
        'caller_name', 'phone', 'email', 'company', 'call_type', 'subject',
        'status', 'duration', 'notes', 'call_time', 'created_by',
    ];

    protected $casts = [
        'call_time' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
