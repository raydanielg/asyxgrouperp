<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    protected $fillable = [
        'tracking_number', 'sender_name', 'recipient_name', 'courier',
        'status', 'received_date', 'delivered_date', 'notes', 'created_by',
    ];

    protected $casts = [
        'received_date' => 'datetime',
        'delivered_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
