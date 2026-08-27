<?php

namespace App\Policies;

use App\Models\PurchaseInvoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoicePolicy extends CompanyPolicy
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
