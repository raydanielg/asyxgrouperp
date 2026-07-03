<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class BankTransferAcc extends Model
{
    use BelongsToCompany;
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
