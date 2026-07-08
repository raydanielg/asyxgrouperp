<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFinancingRepayment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function financing()
    {
        return $this->belongsTo(ProjectFinancing::class, 'project_financing_id');
    }
}
