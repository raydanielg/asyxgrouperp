<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['expense_date' => 'date'];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
