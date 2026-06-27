<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LpoItem extends Model
{
    protected $guarded = ['id'];

    public function lpo()
    {
        return $this->belongsTo(Lpo::class);
    }

    public function grnItems()
    {
        return $this->hasMany(GrnItem::class);
    }
}
