<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'visitor_name', 'phone', 'email', 'company', 'purpose', 'host', 'department',
        'appointment_date', 'duration', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
