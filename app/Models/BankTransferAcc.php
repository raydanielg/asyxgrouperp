<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransferAcc extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['transfer_date' => 'date'];

    public function fromAccount()
    {
        return $this->belongsTo(BankAccount::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(BankAccount::class, 'to_account_id');
    }
}
