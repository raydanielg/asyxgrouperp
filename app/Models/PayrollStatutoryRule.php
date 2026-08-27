<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PayrollStatutoryRule extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'employee_rate' => 'decimal:4',
        'employer_rate' => 'decimal:4',
        'minimum_amount' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereDate('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', now());
            });
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function calculateEmployeeDeduction(float $grossAmount): float
    {
        $basis = $this->calculation_basis === 'gross' ? $grossAmount : $grossAmount;

        $amount = $basis * ((float) $this->employee_rate / 100);

        if ($this->minimum_amount !== null) {
            $amount = max($amount, (float) $this->minimum_amount);
        }

        if ($this->maximum_amount !== null) {
            $amount = min($amount, (float) $this->maximum_amount);
        }

        return round($amount, 2);
    }

    public function calculateEmployerContribution(float $grossAmount): float
    {
        $basis = $this->calculation_basis === 'gross' ? $grossAmount : $grossAmount;

        $amount = $basis * ((float) $this->employer_rate / 100);

        if ($this->minimum_amount !== null) {
            $amount = max($amount, (float) $this->minimum_amount);
        }

        if ($this->maximum_amount !== null) {
            $amount = min($amount, (float) $this->maximum_amount);
        }

        return round($amount, 2);
    }
}
