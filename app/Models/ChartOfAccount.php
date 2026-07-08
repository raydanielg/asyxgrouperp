<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use BelongsToCompany;

    protected $table = 'chart_of_accounts';

    protected $guarded = ['id'];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class, 'chart_of_account_id');
    }

    public function cashAccounts()
    {
        return $this->hasMany(CashAccount::class);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Net balance for this account: debit-normal accounts are (debits - credits),
     * credit-normal accounts are (credits - debits).
     */
    public function balance(?string $from = null, ?string $to = null): float
    {
        $query = $this->lines()->whereHas('journalEntry', function ($q) use ($from, $to) {
            $q->where('status', 'posted');
            if ($from) $q->where('entry_date', '>=', $from);
            if ($to) $q->where('entry_date', '<=', $to);
        });

        $debit = (clone $query)->sum('debit');
        $credit = (clone $query)->sum('credit');

        return $this->normal_balance === 'credit' ? ($credit - $debit) : ($debit - $credit);
    }
}
