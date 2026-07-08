<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Bill extends Model
{
    use BelongsToCompany;
    protected $guarded = ['id'];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
    ];

    public function costAllocations()
    {
        return $this->morphMany(CostAllocation::class, 'cost_allocatable');
    }
}
