<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CompanyPolicy
{
    public function view(User $user, Model $model): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        $companyId = $model->company_id ?? null;

        if ($companyId === null) {
            return true;
        }

        $accessibleIds = $user->accessibleCompanyIds();
        return in_array($companyId, $accessibleIds);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Model $model): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        $companyId = $model->company_id ?? null;
        if ($companyId === null) {
            return true;
        }

        return in_array($companyId, $user->accessibleCompanyIds());
    }

    public function delete(User $user, Model $model): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        $companyId = $model->company_id ?? null;
        if ($companyId === null) {
            return true;
        }

        return in_array($companyId, $user->accessibleCompanyIds());
    }

    public function restore(User $user, Model $model): bool
    {
        return $this->delete($user, $model);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }
}
