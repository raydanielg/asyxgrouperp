<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use BelongsToCompany, BelongsToBranch;

    protected $guarded = ['id'];

    protected $casts = [
        'entry_date' => 'date',
        'posted_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function totalDebit()
    {
        return $this->lines()->sum('debit');
    }

    public function totalCredit()
    {
        return $this->lines()->sum('credit');
    }

    public function reversalOf()
    {
        return $this->belongsTo(JournalEntry::class, 'reversal_of_id');
    }

    public function reversal()
    {
        return $this->hasOne(JournalEntry::class, 'reversal_of_id');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    /** Whether the entry is reversible: posted and not already reversed. */
    public function isReversible(): bool
    {
        return $this->status === 'posted' && !$this->reversal()->exists();
    }
}
