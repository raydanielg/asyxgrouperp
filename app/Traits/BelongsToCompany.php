<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && auth()->user()->company_id) {
                $user = auth()->user();
                $switchedId = session('switched_company_id');

                if ($user->company && $user->company->is_group) {
                    if ($switchedId !== null) {
                        $builder->where($builder->getQuery()->from . '.company_id', $switchedId);
                    }
                    return;
                }
                $builder->where($builder->getQuery()->from . '.company_id', $user->company_id);
            }
        });

        static::creating(function ($model) {
            if (!$model->company_id && auth()->check()) {
                $switchedId = session('switched_company_id');
                if ($switchedId !== null) {
                    $model->company_id = $switchedId;
                } elseif (auth()->user()->company_id) {
                    $model->company_id = auth()->user()->company_id;
                }
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForCompany(Builder $query, ?int $companyId = null): Builder
    {
        return $query->withoutGlobalScope('company')->where('company_id', $companyId);
    }

    public function scopeAllCompanies(Builder $query): Builder
    {
        return $query->withoutGlobalScope('company');
    }
}
