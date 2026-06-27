<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientReceipt extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
