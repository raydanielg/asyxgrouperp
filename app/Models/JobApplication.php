<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'extra_docs' => 'array',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function approvals()
    {
        return $this->hasMany(JobApplicationApproval::class);
    }
}
