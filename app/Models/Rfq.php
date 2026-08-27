<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Rfq extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'issue_date' => 'date',
        'closing_date' => 'date',
    ];

    public function requisition()
    {
        return $this->belongsTo(ProcurementRequisition::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function suppliers()
    {
        return $this->hasMany(RfqSupplier::class);
    }

    public function responses()
    {
        return $this->hasMany(RfqResponse::class);
    }

    public function scopeOpen($query) { return $query->where('status', 'open'); }
    public function scopeClosed($query) { return $query->where('status', 'closed'); }
    public function scopeAwarded($query) { return $query->where('status', 'awarded'); }
}
