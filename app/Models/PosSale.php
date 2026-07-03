<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class PosSale extends Model
{
    use BelongsToCompany;
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(PosSaleItem::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
