<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChMessage extends Model
{
    protected $fillable = ['from_id', 'to_id', 'body', 'attachment', 'seen'];

    protected $casts = ['seen' => 'boolean'];
}
