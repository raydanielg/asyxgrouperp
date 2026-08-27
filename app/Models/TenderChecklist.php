<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class TenderChecklist extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'is_required' => 'boolean',
        'is_checked' => 'boolean',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }
}
