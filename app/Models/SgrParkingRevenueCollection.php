<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgrParkingRevenueCollection extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'sgr_parking_revenue_collections';

    protected $guarded = ['id'];

    protected $casts = [
        'date_in' => 'date',
        'date_out' => 'date',
        'amount_collected' => 'decimal:2',
        'amount_deposited' => 'decimal:2',
        'difference' => 'decimal:2',
        'raw_data' => 'array',
    ];

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }
}
