<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function custodian()
    {
        return $this->belongsTo(User::class, 'custodian_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(CashAccountTransaction::class)->orderByDesc('transaction_date')->orderByDesc('id');
    }

    public function topupRequests()
    {
        return $this->hasMany(CashTopupRequest::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function scopePettyCash($query)
    {
        return $query->where('type', 'petty_cash');
    }

    public function scopeProjectCash($query)
    {
        return $query->where('type', 'project');
    }

    public function totalCredits(): float
    {
        return (float) $this->transactions()->where('type', 'credit')->sum('amount');
    }

    public function totalDebits(): float
    {
        return (float) $this->transactions()->where('type', 'debit')->sum('amount');
    }
}
