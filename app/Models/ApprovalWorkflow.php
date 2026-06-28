<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalWorkflow extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'name', 'module', 'description', 'is_active', 'company_id', 'created_by',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function steps()
    {
        return $this->hasMany(ApprovalStep::class, 'workflow_id')->orderBy('order');
    }

    public function requests()
    {
        return $this->hasMany(ApprovalRequest::class, 'workflow_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
