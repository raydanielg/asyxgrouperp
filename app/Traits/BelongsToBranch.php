<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Branch-level data isolation.
 *
 * Policy:
 * - superadmin → unrestricted
 * - user with branch assignments → only records of their branches
 *   PLUS unassigned (NULL branch_id = company-wide legacy) records
 * - user without branch assignments → whole company (no constraint)
 */
trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            if (!auth()->check()) {
                return;
            }
            $ids = auth()->user()->accessibleBranchIds();
            if ($ids !== null) {
                $builder->where(function ($q) use ($ids) {
                    $q->whereIn('branch_id', $ids)->orWhereNull('branch_id');
                });
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }
}
