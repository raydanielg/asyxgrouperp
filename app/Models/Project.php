<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function bugs()
    {
        return $this->hasMany(ProjectBug::class);
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
