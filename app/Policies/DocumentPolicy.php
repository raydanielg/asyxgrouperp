<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DocumentPolicy extends CompanyPolicy
{
    public function delete(User $user, Model $model): bool
    {
        if (in_array($model->status, ['signed', 'archived'])) {
            return $user->isAdmin() || $user->isSuperAdmin();
        }
        return parent::delete($user, $model);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function download(User $user, Model $model): bool
    {
        if ($model->is_confidential) {
            return $model->uploaded_by === $user->id || $user->isAdmin() || $user->isSuperAdmin();
        }
        return parent::view($user, $model);
    }
}
