<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeClockedIn($query)
    {
        return $query->whereNotNull('clock_in_at')->whereNull('clock_out_at');
    }

    public function getFormattedWorkHoursAttribute(): string
    {
        if ($this->work_hours > 0) {
            $h = floor($this->work_hours);
            $m = ($this->work_hours - $h) * 60;
            return sprintf('%02d:%02d', $h, $m);
        }
        if ($this->clock_in_at && $this->clock_out_at) {
            $diff = $this->clock_out_at->diffInMinutes($this->clock_in_at);
            return sprintf('%02d:%02d', floor($diff / 60), $diff % 60);
        }
        if ($this->clock_in_at && !$this->clock_out_at) {
            $diff = now()->diffInMinutes($this->clock_in_at);
            return sprintf('%02d:%02d', floor($diff / 60), $diff % 60);
        }
        return '00:00';
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'present' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            'absent' => 'bg-red-50 text-red-700 border-red-100',
            'late' => 'bg-amber-50 text-amber-700 border-amber-100',
            'half_day' => 'bg-sky-50 text-sky-700 border-sky-100',
            'remote' => 'bg-purple-50 text-purple-700 border-purple-100',
        ];

        $cls = $colors[$this->status] ?? 'bg-gray-50 text-gray-700 border-gray-100';
        return '<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium border ' . $cls . '">' . ucfirst(str_replace('_', ' ', $this->status)) . '</span>';
    }
}
