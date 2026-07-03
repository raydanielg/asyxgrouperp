<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class ProjectTask extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = ['due_date' => 'date'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
