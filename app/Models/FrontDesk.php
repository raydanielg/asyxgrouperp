<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrontDesk extends Model
{
    protected $table = 'front_desks';

    protected $fillable = [
        'name', 'person_type', 'purpose', 'host', 'department',
        'status', 'check_in_at', 'check_out_at', 'notes', 'created_by',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
