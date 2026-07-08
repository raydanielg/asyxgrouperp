<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CostAllocation extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function costAllocatable()
    {
        return $this->morphTo();
    }
}
