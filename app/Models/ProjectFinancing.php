<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProjectFinancing extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'disbursed_at' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sourceProject()
    {
        return $this->belongsTo(Project::class, 'source_project_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function repayments()
    {
        return $this->hasMany(ProjectFinancingRepayment::class);
    }

    public function totalPaid(): float
    {
        return (float) $this->repayments()->sum('paid_amount');
    }

    public function balanceDue(): float
    {
        return max(0, (float) $this->amount - $this->totalPaid());
    }
}
