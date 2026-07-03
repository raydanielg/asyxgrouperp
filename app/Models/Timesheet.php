<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Timesheet extends Model
{
    use BelongsToCompany;
    protected $guarded = ['id'];

    protected $casts = ['date' => 'date'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(ProjectTask::class);
    }
}
