<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PosReturn extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = ['processed_at' => 'datetime', 'refund_amount' => 'decimal:2'];

    public function sale()
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }

    public function items()
    {
        return $this->hasMany(PosReturnItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
