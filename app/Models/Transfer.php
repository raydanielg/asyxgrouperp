<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Transfer extends Model
{
    use BelongsToCompany;
    protected $fillable = ['from_warehouse', 'to_warehouse', 'product_name', 'quantity', 'date', 'creator_id', 'created_by', 'status', 'approved_by', 'approved_at'];

    protected $casts = ['approved_at' => 'datetime'];

    public function fromWarehouse() { return $this->belongsTo(Warehouse::class, 'from_warehouse'); }
    public function toWarehouse() { return $this->belongsTo(Warehouse::class, 'to_warehouse'); }
    public function creator() { return $this->belongsTo(User::class, 'creator_id'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }

    public function approve(User $user): void
    {
        $this->update(['status' => 'completed', 'approved_by' => $user->id, 'approved_at' => now()]);
    }
}
