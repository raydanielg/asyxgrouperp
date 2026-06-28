<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntercompanyLine extends Model
{
    use HasFactory;

    protected $table = 'intercompany_lines';

    protected $fillable = [
        'intercompany_transaction_id',
        'description',
        'quantity',
        'unit_price',
        'line_total',
    ];

    public function transaction()
    {
        return $this->belongsTo(IntercompanyTransaction::class);
    }
}
