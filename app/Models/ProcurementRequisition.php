<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequisition extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'required_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(ProcurementRequisitionItem::class, 'requisition_id');
    }

    public function rfqs()
    {
        return $this->hasMany(Rfq::class);
    }

    public function scopeDraft($query) { return $query->where('status', 'draft'); }
    public function scopePendingApproval($query) { return $query->where('status', 'pending_approval'); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
}
