<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class BankTransferPayment extends Model
{
    use BelongsToCompany;
    protected $fillable = ['user_id', 'price', 'order_id', 'status', 'price_currency', 'attachment', 'request', 'type', 'created_by'];

    public function user() { return $this->belongsTo(User::class); }
}
