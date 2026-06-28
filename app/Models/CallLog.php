<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id', 'call_direction', 'caller_name', 'caller_phone',
        'callee_name', 'callee_phone', 'call_start', 'call_end',
        'duration_seconds', 'status', 'disposition', 'notes',
        'call_recording_url', 'agent_id', 'company_id',
    ];

    protected $casts = [
        'call_start' => 'datetime',
        'call_end' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(CallCampaign::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getDurationFormattedAttribute(): string
    {
        $mins = floor($this->duration_seconds / 60);
        $secs = $this->duration_seconds % 60;
        return sprintf('%02d:%02d', $mins, $secs);
    }
}
