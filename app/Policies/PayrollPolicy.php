<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PayrollPolicy extends CompanyPolicy
{
    public function delete(User $user, Model $model): bool
    {
        if (in_array($model->status, ['approved', 'paid'])) {
            return false;
        }
        return parent::delete($user, $model);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }
}
