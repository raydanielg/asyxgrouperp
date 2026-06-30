<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingActionPoint extends Model
{
    protected $fillable = [
        'meeting_id', 'description', 'assigned_to', 'due_date',
        'status', 'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
