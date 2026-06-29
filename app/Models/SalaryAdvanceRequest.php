<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAdvanceRequest extends Model
{
    protected $fillable = [
        'user_id', 'approved_by', 'amount', 'reason', 'status', 'requested_date', 'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'requested_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
