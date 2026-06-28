<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_number', 'asset_tag', 'name', 'category', 'description',
        'acquisition_date', 'acquisition_cost', 'salvage_value', 'useful_life_years',
        'depreciation_method', 'accumulated_depreciation', 'net_book_value',
        'location', 'assigned_to', 'status', 'disposal_date', 'disposal_value',
        'disposal_notes', 'company_id', 'created_by',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'disposal_date' => 'date',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function depreciationRecords()
    {
        return $this->hasMany(DepreciationRecord::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    public function calculateMonthlyDepreciation(): float
    {
        if ($this->useful_life_years <= 0) return 0;
        $depreciable = $this->acquisition_cost - $this->salvage_value;
        return $depreciable / ($this->useful_life_years * 12);
    }
}
