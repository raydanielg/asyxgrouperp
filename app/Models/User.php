<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'first_name', 'last_name', 'email', 'phone', 'password', 'role', 'company_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, \App\Traits\BelongsToCompany;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function permissions()
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->unique('id');
    }

    public function permissionNames(): array
    {
        if (cache()->has("user_perms_{$this->id}")) {
            return cache()->get("user_perms_{$this->id}");
        }
        $names = $this->permissions()->pluck('name')->unique()->values()->toArray();
        cache()->put("user_perms_{$this->id}", $names, now()->addMinutes(30));
        return $names;
    }

    public function hasPermission(string $name): bool
    {
        if ($this->role === 'admin') return true;
        return in_array($name, $this->permissionNames());
    }

    public function hasAnyPermission(array $names): bool
    {
        if ($this->role === 'admin') return true;
        $perms = $this->permissionNames();
        foreach ($names as $n) {
            if (in_array($n, $perms)) return true;
        }
        return false;
    }

    public function hasRole(string $name): bool
    {
        return $this->roles()->where('name', $name)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin' || $this->hasRole('superadmin');
    }

    public function canSwitchCompany(): bool
    {
        return $this->isAdmin() || $this->isSuperAdmin() || ($this->company && $this->company->is_group);
    }

    public function accessibleCompanyIds(): array
    {
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return \App\Models\Company::pluck('id')->toArray();
        }
        if ($this->company && $this->company->is_group) {
            return \App\Models\Company::where('is_active', true)->pluck('id')->toArray();
        }
        return $this->company_id ? [$this->company_id] : [];
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function assignedTasks()
    {
        return $this->hasMany(ProjectTask::class, 'assigned_to');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isGroupUser(): bool
    {
        return $this->company_id === null || ($this->company && $this->company->is_group);
    }

    public function scopeForCompany($query, ?int $companyId = null)
    {
        if ($companyId) {
            return $query->where('company_id', $companyId);
        }
        if (auth()->check() && !auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            $activeId = auth()->user()->activeCompanyId();
            if ($activeId) {
                return $query->where('company_id', $activeId);
            }
        }
        return $query;
    }

    public static function currentCompanyId(): ?int
    {
        return session('current_company_id') ?: session('switched_company_id');
    }

    public function activeCompanyId(): ?int
    {
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return session('switched_company_id');
        }
        return $this->company_id;
    }

    public function currentCompany(): ?Company
    {
        $id = $this->activeCompanyId();
        return $id ? Company::find($id) : $this->company;
    }
}
