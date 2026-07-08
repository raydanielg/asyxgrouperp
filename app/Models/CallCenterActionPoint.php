<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CallCenterActionPoint extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'due_date' => 'date',
        'raw_data' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
