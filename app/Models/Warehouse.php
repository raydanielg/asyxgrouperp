<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use BelongsToCompany;

    protected $fillable = ['name', 'address', 'city', 'zip_code', 'phone', 'email', 'is_active', 'creator_id', 'created_by', 'company_id', 'branch_id'];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * Branch-restricted users only see warehouses of their assigned branches.
     * NULL branch_id = unassigned (company-wide, visible to all).
     */
    protected static function booted(): void
    {
        static::addGlobalScope('branch', function (\Illuminate\Database\Eloquent\Builder $b) {
            if (!auth()->check()) {
                return;
            }
            $ids = auth()->user()->accessibleBranchIds();
            if ($ids !== null) {
                $b->where(function ($q) use ($ids) {
                    $q->whereIn('branch_id', $ids)->orWhereNull('branch_id');
                });
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator() { return $this->belongsTo(User::class, 'creator_id'); }
    public function createdByUser() { return $this->belongsTo(User::class, 'created_by'); }
}
