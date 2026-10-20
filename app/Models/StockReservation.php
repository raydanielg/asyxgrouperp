<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class StockReservation extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'consumed_quantity' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Outstanding (still-held) quantity — quantity minus consumed */
    public function outstanding(): float
    {
        return max(0.0, (float) $this->quantity - (float) $this->consumed_quantity);
    }
}
