<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepreciationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'fixed_asset_id', 'depreciation_date', 'depreciation_amount',
        'accumulated_depreciation', 'net_book_value', 'period', 'created_by',
    ];

    protected $casts = ['depreciation_date' => 'date'];

    public function asset()
    {
        return $this->belongsTo(FixedAsset::class, 'fixed_asset_id');
    }
}
