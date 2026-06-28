<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = ['revenue_date' => 'date'];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
