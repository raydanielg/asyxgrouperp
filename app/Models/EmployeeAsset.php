<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAsset extends Model
{
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
