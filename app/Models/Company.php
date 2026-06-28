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
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'is_active' => 'boolean',
    ];

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
}
