<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'joining_date' => 'date',
        'leaving_date' => 'date',
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function salaryAdvances()
    {
        return $this->hasMany(SalaryAdvance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function assets()
    {
        return $this->hasMany(EmployeeAsset::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function fullDepartmentName(): string
    {
        return $this->department ?? 'Unassigned';
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'employee_project')
            ->withPivot(['role', 'assigned_from', 'assigned_until', 'is_active'])
            ->withTimestamps();
    }

    public function activeProjects()
    {
        return $this->belongsToMany(Project::class, 'employee_project')
            ->wherePivot('is_active', true)
            ->withPivot(['role', 'assigned_from', 'assigned_until', 'is_active'])
            ->withTimestamps();
    }

    public function bonuses()
    {
        return $this->hasMany(EmployeeBonus::class);
    }
}
