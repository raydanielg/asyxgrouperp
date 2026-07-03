<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static $applyingScope = false;

        static::addGlobalScope('company', function (Builder $builder) use (&$applyingScope) {
            if ($applyingScope) return;

            $table = $builder->getQuery()->from;

            // Skip scope for users table to prevent infinite recursion:
            // auth()->check() → retrieveById → User query → applyScopes → auth()->check() ...
            if ($table === 'users') return;

            // Skip if table doesn't have company_id column
            try {
                if (!\Schema::hasColumn($table, 'company_id')) return;
            } catch (\Throwable $e) {
                return;
            }

            if (!auth()->check()) return;

            $user = auth()->user();

            // Superadmin / ERP admin sees all unless a company is explicitly switched
            if ($user->isAdmin() || $user->isSuperAdmin()) {
                $switchedId = session('switched_company_id');
                if ($switchedId !== null) {
                    $builder->where($table . '.company_id', $switchedId);
                }
                return;
            }

            // Group-level user sees all subsidiaries, or only the switched company
            $switchedId = session('switched_company_id');
            if ($user->company_id && $user->company && $user->company->is_group) {
                if ($switchedId !== null) {
                    $builder->where($table . '.company_id', $switchedId);
                }
                return;
            }

            // Regular user only sees their own company
            if ($user->company_id) {
                $builder->where($table . '.company_id', $user->company_id);
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
