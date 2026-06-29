<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Correspondence extends Model
{
    protected $table = 'correspondence';

    protected $fillable = [
        'reference_number', 'type', 'sender_name', 'recipient_name', 'subject',
        'description', 'status', 'received_date', 'dispatched_date', 'notes', 'created_by',
    ];

    protected $casts = [
        'received_date' => 'datetime',
        'dispatched_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
