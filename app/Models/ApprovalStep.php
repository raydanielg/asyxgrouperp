<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id', 'level', 'name', 'approver_role', 'approver_user_id',
        'approver_type', 'is_final', 'order',
    ];

    protected $casts = ['is_final' => 'boolean'];

    public function workflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
