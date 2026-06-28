<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_request_id', 'level', 'approver_id', 'action', 'comment', 'acted_at',
    ];

    protected $casts = ['acted_at' => 'datetime'];

    public function request()
    {
        return $this->belongsTo(ApprovalRequest::class, 'approval_request_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
