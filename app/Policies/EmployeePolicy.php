<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmployeePolicy extends CompanyPolicy
{
    public function view(User $user, Model $model): bool
    {
        return parent::view($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }
}
