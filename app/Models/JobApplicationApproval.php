<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicationApproval extends Model
{
    protected $guarded = ['id'];

    public function application()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
