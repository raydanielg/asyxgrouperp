<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'legal_name',
        'short_code',
        'registration_number',
        'tax_id',
        'vrn',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'website',
        'logo',
        'currency',
        'fiscal_year_start',
        'timezone',
        'parent_id',
        'is_group',
        'is_active',
        'created_by',
        'subscription_plan',
        'subscription_status',
        'billing_cycle',
        'mrr',
        'max_users',
        'subscription_starts_at',
        'subscription_ends_at',
        'suspended_at',
        'suspension_reason',
        'emails_enabled',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'is_active' => 'boolean',
        'emails_enabled' => 'boolean',
        'mrr' => 'decimal:2',
        'subscription_starts_at' => 'date',
        'subscription_ends_at' => 'date',
        'suspended_at' => 'datetime',
    ];

    public const PLANS = [
        'starter' => 'Starter',
        'growth' => 'Growth',
        'business' => 'Business',
        'enterprise' => 'Enterprise',
    ];

    public const SUBSCRIPTION_STATUSES = [
        'trial' => 'Trial',
        'active' => 'Active',
        'past_due' => 'Past Due',
        'suspended' => 'Suspended',
        'cancelled' => 'Cancelled',
    ];

    /**
     * A company whose entire user base must be locked out of the system.
     * Suspension OR inactive flag both lock the company out.
     */
    public function isLockedOut(): bool
    {
        if (!$this->is_active) {
            return true;
        }
        if ($this->subscription_status === 'suspended') {
            return true;
        }
        if ($this->subscription_ends_at && $this->subscription_ends_at->isPast() && in_array($this->subscription_status, ['past_due', 'cancelled'], true)) {
            return true;
        }
        return false;
    }

    public function suspensionMessage(): string
    {
        if ($this->subscription_status === 'suspended') {
            $reason = $this->suspension_reason ? " Reason: {$this->suspension_reason}." : '';
            return "Access to {$this->name} has been suspended.{$reason} Contact your ERP administrator.";
        }
        if (!$this->is_active) {
            return "{$this->name} is deactivated. Contact your ERP administrator.";
        }
        if ($this->subscription_ends_at && $this->subscription_ends_at->isPast()) {
            return "The {$this->name} subscription has expired. Contact your ERP administrator to renew.";
        }
        return "Access to {$this->name} is unavailable right now. Contact your ERP administrator.";
    }

    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Company::class, 'parent_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tenders()
    {
        return $this->hasMany(Tender::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function outgoingIntercompany()
    {
        return $this->hasMany(IntercompanyTransaction::class, 'from_company_id');
    }

    public function incomingIntercompany()
    {
        return $this->hasMany(IntercompanyTransaction::class, 'to_company_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOperating($query)
    {
        return $query->where('is_group', false)->where('is_active', true);
    }

    public static function current(): ?self
    {
        $id = session('current_company_id');
        return $id ? self::find($id) : null;
    }

    public function scopeForCurrentUser($query)
    {
        if (auth()->check() && !auth()->user()->isAdmin()) {
            return $query->where('id', session('current_company_id'));
        }
        return $query;
    }
}
