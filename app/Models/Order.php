<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_id', 'name', 'email', 'plan_name', 'plan_id', 'price', 'discount_amount', 'currency', 'txn_id', 'payment_status', 'payment_type', 'receipt', 'created_by'];

    public function plan() { return $this->belongsTo(Plan::class); }
}
