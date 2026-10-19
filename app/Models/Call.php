<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use \App\Traits\BelongsToCompany;

    protected $table = 'calls';

    protected $fillable = [
        'company_id',
        'created_by',
        'caller_name',
        'phone',
        'email',
        'company',
        'call_type',
        'subject',
        'status',
        'duration',
        'notes',
        'call_time',
    ];

    protected $casts = [
        'call_time' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
