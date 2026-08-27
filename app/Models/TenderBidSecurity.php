<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class TenderBidSecurity extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'valid_until' => 'date',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function isValid(): bool
    {
        return !$this->valid_until || $this->valid_until->isFuture();
    }
}
