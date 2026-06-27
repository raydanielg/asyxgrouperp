<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'submission_date' => 'date',
        'closing_date' => 'date',
    ];

    public function lead()
    {
        return $this->hasOne(CrmLead::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
