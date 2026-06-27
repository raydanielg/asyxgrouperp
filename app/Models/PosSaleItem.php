<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosSaleItem extends Model
{
    protected $guarded = ['id'];

    public function sale()
    {
        return $this->belongsTo(PosSale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
