<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class EmployeeAsset extends Model
{
    use BelongsToCompany;
    protected $guarded = ['id'];

    protected $casts = [
        'assigned_date' => 'date',
        'return_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
