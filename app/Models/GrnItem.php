<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnItem extends Model
{
    protected $guarded = ['id'];

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }

    public function lpoItem()
    {
        return $this->belongsTo(LpoItem::class);
    }
}
