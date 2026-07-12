<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class JobCard extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'due_date' => 'date',
        'report_date' => 'date',
        'resolved_at' => 'datetime',
        'end_user_signed_at' => 'datetime',
        'technician_signed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function parts()
    {
        return $this->hasMany(JobCardPart::class)->orderBy('sort_order');
    }
}
