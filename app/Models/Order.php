<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Order extends Model
{
    use BelongsToCompany;
    protected $fillable = ['order_id', 'name', 'email', 'plan_name', 'plan_id', 'price', 'discount_amount', 'currency', 'txn_id', 'payment_status', 'payment_type', 'receipt', 'created_by', 'company_id'];

    public function company() { return $this->belongsTo(Company::class); }

    public function plan() { return $this->belongsTo(Plan::class); }
}
