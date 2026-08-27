<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ProjectPolicy extends CompanyPolicy
{
    public function delete(User $user, Model $model): bool
    {
        if (in_array($model->status, ['completed', 'closed'])) {
            return false;
        }
        return parent::delete($user, $model);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }
}
