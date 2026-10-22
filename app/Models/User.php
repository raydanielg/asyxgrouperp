<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'first_name', 'last_name', 'email', 'phone', 'password', 'role', 'company_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, \App\Traits\BelongsToCompany;

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

    /**
     * Database-driven permission resolution.
     * Permissions = union of role_permission for the user's assigned roles
     * (role_user pivot) + the user's role-column role. Falls back to the
     * legacy RolePermissions map only when no DB entry exists.
     * Cached per user and invalidated on role/permission changes.
     */
    public function directPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user')->withTimestamps();
    }

    /**
     * Branch scope resolution:
     * - superadmin → all branches
     * - user with no branch assignments → whole company (default access)
     * - user with assignments → only those branches
     */
    public function accessibleBranchIds(): ?array
    {
        if ($this->isSuperAdmin()) {
            return null; // null = unrestricted
        }
        $ids = $this->branches()->pluck('branches.id')->all();
        return $ids === [] ? null : $ids; // null = company-wide default
    }

    public function canAccessBranch(?int $branchId): bool
    {
        $allowed = $this->accessibleBranchIds();
        if ($allowed === null) {
            return true;
        }
        return in_array($branchId, $allowed);
    }

    /**
     * Database-driven permission resolution.
     * Resolution order:
     *   1. Role-derived permissions (role_user pivot + role column → role_permission)
     *   2. Explicit user denials REMOVE role-derived permissions
     *   3. Explicit user grants ADD permissions on top
     * Falls back to the legacy RolePermissions map only when no DB entry exists.
     * Cached per user and invalidated on role/permission changes.
     */
    public function permissionNames(): array
    {
        return cache()->remember("user_perms_{$this->id}", 300, function () {
            $perms = $this->resolvePermissionNames();
            return $perms['effective'];
        });
    }

    /**
     * Full permission resolution for the Effective Access panel.
     * Returns ['role' => [...], 'granted' => [...], 'denied' => [...], 'effective' => [...]]
     */
    public function resolvePermissionNames(): array
    {
        $roleNames = $this->roles()->pluck('name')->all();
        if ($this->role) {
            $roleNames[] = $this->role;
        }
        $roleNames = array_unique(array_filter($roleNames));

        $rolePerms = Permission::whereHas('roles', function ($q) use ($roleNames) {
                $q->whereIn('roles.name', $roleNames);
            })->pluck('name')->unique()->values()->all();

        if (empty($rolePerms)) {
            $rolePerms = \App\Support\RolePermissions::forRole($this->role);
        }

        $granted = [];
        $denied = [];
        $overrides = $this->directPermissions()->with('permission')->get();
        foreach ($overrides as $o) {
            $name = $o->permission?->name;
            if (!$name) continue;
            if ($o->granted) $granted[] = $name; else $denied[] = $name;
        }

        $effective = array_values(array_unique(array_merge(
            array_diff($rolePerms, $denied),
            $granted
        )));

        return ['role' => $rolePerms, 'granted' => $granted, 'denied' => $denied, 'effective' => $effective];
    }

    public function hasPermission(string $name): bool
    {
        if ($this->role === 'admin' || $this->role === 'superadmin') return true;
        return in_array($name, $this->permissionNames());
    }

    public function hasAnyPermission(array $names): bool
    {
        if ($this->role === 'admin' || $this->role === 'superadmin') return true;
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
        return $this->role === 'admin' || $this->role === 'superadmin' || $this->hasRole('admin') || $this->hasRole('superadmin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin' || $this->hasRole('superadmin');
    }

    public function canSwitchCompany(): bool
    {
        return $this->isSuperAdmin() || $this->hasPermission('switch-companies');
    }

    public function accessibleCompanyIds(): array
    {
        if ($this->isAdmin() || $this->isSuperAdmin() || $this->hasPermission('switch-companies')) {
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
