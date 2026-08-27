<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class TenderEvaluation extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'technical_score' => 'decimal:2',
        'financial_score' => 'decimal:2',
        'total_score' => 'decimal:2',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
