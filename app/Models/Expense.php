<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = ['expense_date' => 'date'];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function costAllocations()
    {
        return $this->morphMany(CostAllocation::class, 'cost_allocatable');
    }
}
