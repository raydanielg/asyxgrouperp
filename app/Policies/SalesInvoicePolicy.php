<?php

namespace App\Policies;

use App\Models\SalesInvoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SalesInvoicePolicy extends CompanyPolicy
{
    public function delete(User $user, Model $model): bool
    {
        if (in_array($model->status, ['posted', 'paid', 'partial'])) {
            return false;
        }
        return parent::delete($user, $model);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }
}
