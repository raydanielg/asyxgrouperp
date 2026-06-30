<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'project_id', 'title', 'type', 'mode',
        'meeting_date', 'start_time', 'end_time', 'location',
        'meeting_link', 'agenda', 'minutes', 'report', 'status',
        'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function attendees()
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    public function actionPoints()
    {
        return $this->hasMany(MeetingActionPoint::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
