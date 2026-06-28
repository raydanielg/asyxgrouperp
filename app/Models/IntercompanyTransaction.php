<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntercompanyTransaction extends Model
{
    use HasFactory;

    protected $table = 'intercompany_transactions';

    protected $fillable = [
        'transaction_number',
        'from_company_id',
        'to_company_id',
        'type',
        'amount',
        'currency',
        'transaction_date',
        'reference_type',
        'reference_id',
        'description',
        'status',
        'eliminated_at',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'eliminated_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function fromCompany()
    {
        return $this->belongsTo(Company::class, 'from_company_id');
    }

    public function toCompany()
    {
        return $this->belongsTo(Company::class, 'to_company_id');
    }

    public function lines()
    {
        return $this->hasMany(IntercompanyLine::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeEliminated($query)
    {
        return $query->where('status', 'eliminated');
    }
}
