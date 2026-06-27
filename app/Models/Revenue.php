<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['revenue_date' => 'date'];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
