<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use \App\Traits\BelongsToCompany;
    protected $fillable = [
        'company_id',
        'name',
        'period',
        'cost_center',
        'amount',
        'actual_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getVarianceAttribute(): float
    {
        return (float) $this->amount - (float) $this->actual_amount;
    }
}
