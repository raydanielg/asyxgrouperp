<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = ['from_warehouse', 'to_warehouse', 'product_name', 'quantity', 'date', 'creator_id', 'created_by'];

    public function fromWarehouse() { return $this->belongsTo(Warehouse::class, 'from_warehouse'); }
    public function toWarehouse() { return $this->belongsTo(Warehouse::class, 'to_warehouse'); }
    public function creator() { return $this->belongsTo(User::class, 'creator_id'); }
}
