<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\LoginHistory;
use App\Models\Company;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized access. Admin only.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::with('roles');

        // Tenant admins only manage users inside their own company
        $user = auth()->user();
        if ($user->isAdmin() && !$user->isSuperAdmin()) {
            $query->where('company_id', $user->company_id)
                  ->where('role', '!=', 'superadmin');
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        if ($request->filled('is_enable_login')) {
            $query->where('is_enable_login', $request->is_enable_login);
        }

        $perPage = $request->input('per_page', 10);
        $allowed = [10, 50, 100, 'all'];
        if (!in_array($perPage, $allowed, true)) {
            $perPage = 10;
        }

        if ($perPage === 'all') {
            $users = $query->orderBy('created_at', 'desc')->get();
        } else {
            $users = $query->orderBy('created_at', 'desc')->paginate((int) $perPage)->appends($request->except('page'));
        }

        $roles = Role::orderBy('name')->pluck('label', 'name')->toArray();
        $disabledCount = User::where('is_enable_login', false)->count();
        $activeSessions = \Illuminate\Support\Facades\DB::table('sessions')->where('last_activity', '>', now()->subMinutes(30)->getTimestamp())->count();

        if ($request->ajax()) {
            $infoText = '';
            if ($perPage !== 'all' && $users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
                $infoText = 'Showing ' . ($users->firstItem() ?? 0) . ' to ' . ($users->lastItem() ?? 0) . ' of ' . $users->total();
            }
            return response()->json([
                'html' => view('admin.users.partials.user_rows', compact('users'))->render(),
                'pagination' => $perPage === 'all' ? '' : view('admin.users.partials.pagination', compact('users', 'perPage'))->render(),
                'infoText' => $infoText,
                'perPage' => $perPage,
                'total' => $users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ? $users->total() : $users->count(),
            ]);
        }

        return view('admin.users.index', compact('users', 'roles', 'disabledCount', 'activeSessions', 'perPage'));
    }

    /**
     * Superadmin protection — only a superadmin may create or assign the
     * superadmin role, and the last superadmin can never be demoted,
     * disabled, or deleted.
     */
    private function guardSuperadminRole(string $role): void
    {
        if ($role === 'superadmin' && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Only a platform administrator can create or assign the Super Admin role.');
        }
    }

    private function guardSuperadminTarget(User $target, ?string $newRole = null, ?bool $enableLogin = null): void
    {
        $actor = auth()->user();

        if ($target->isSuperAdmin() && !$actor->isSuperAdmin()) {
            abort(403, 'You cannot modify a platform administrator account.');
        }

        if ($target->isSuperAdmin()) {
            $superadminCount = User::where('role', 'superadmin')->count();
            $demoting = $newRole !== null && $newRole !== 'superadmin';
            $disabling = $enableLogin === false;
            if (($demoting || $disabling) && $superadminCount <= 1) {
                abort(403, 'The last Super Admin account cannot be demoted or disabled.');
            }
        }
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,superadmin,user',
            'company_id' => 'nullable|exists:companies,id',
            'assigned_roles' => 'array',
            'assigned_roles.*' => 'exists:roles,id',
            'is_enable_login' => 'boolean',
        ]);

        $this->guardSuperadminRole($validated['role']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'company_id' => $validated['company_id'] ?? null,
            'is_enable_login' => $request->boolean('is_enable_login', true),
        ]);

        if (!empty($validated['assigned_roles'])) {
            $user->roles()->sync($validated['assigned_roles']);
        }

        Cache::forget("user_perms_{$user->id}");

        return back()->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $this->guardSuperadminTarget($user);
        $roles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        $companies = Company::orderBy('name')->get();

        // Effective access: full resolution with sources
        $resolution = $user->resolvePermissionNames();
        $isPrivileged = $user->isAdmin() || $user->isSuperAdmin();
        $effectivePerms = $isPrivileged
            ? null // admins bypass the catalogue
            : Permission::whereIn('name', $resolution['effective'])
                ->get()->groupBy('module')->sortKeys();
        $overrides = $user->directPermissions()->with(['permission', 'granter'])->latest()->get();
        $allPermissions = $isPrivileged ? collect() : Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        return view('admin.users.edit', compact('user', 'roles', 'userRoles', 'companies', 'effectivePerms', 'overrides', 'allPermissions', 'resolution', 'isPrivileged'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,superadmin,user',
            'company_id' => 'nullable|exists:companies,id',
            'assigned_roles' => 'array',
            'assigned_roles.*' => 'exists:roles,id',
            'is_enable_login' => 'boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $this->guardSuperadminRole($validated['role']);
        $this->guardSuperadminTarget($user, $validated['role'], $request->boolean('is_enable_login', true));

        // Tenant admin cannot move users to a different company
        $actor = auth()->user();
        if (!$actor->isSuperAdmin() && (int)($validated['company_id'] ?? $user->company_id) !== (int)$actor->company_id) {
            abort(403, 'You can only manage users inside your own company.');
        }

        // Branch assignments must belong to the user's company
        $branchIds = collect($request->input('assigned_branches', []))->filter()->values();
        if ($branchIds->isNotEmpty()) {
            $companyId = $validated['company_id'] ?? $user->company_id;
            $valid = \App\Models\Branch::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->whereIn('id', $branchIds)->count();
            abort_if($valid !== $branchIds->count(), 403, 'Invalid branch selection for this company.');
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'company_id' => $validated['company_id'] ?? null,
            'is_enable_login' => $request->boolean('is_enable_login', true),
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        if (!empty($validated['assigned_roles'])) {
            $user->roles()->sync($validated['assigned_roles']);
        } else {
            $user->roles()->detach();
        }

        $user->branches()->sync($branchIds->all());

        Cache::forget("user_perms_{$user->id}");

        return back()->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $this->guardSuperadminTarget($user);
        if ($user->isSuperAdmin() && User::where('role', 'superadmin')->count() <= 1) {
            return back()->with('error', 'The last Super Admin account cannot be deleted.');
        }

        $user->roles()->detach();
        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }

    /**
     * Manage a direct permission override for a user.
     * action = grant | deny | revoke
     * Only the platform superadmin can set direct permission overrides —
     * they bypass normal role delegation and must be tightly controlled.
     */
    public function updatePermission(Request $request, User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403, 'Only a platform administrator can manage direct permissions.');

        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
            'action' => 'required|in:grant,deny,revoke',
            'reason' => 'nullable|string|max:255',
        ]);

        $permission = Permission::where('name', $validated['permission'])->firstOrFail();
        $existing = $user->directPermissions()->where('permission_id', $permission->id)->first();

        DB::transaction(function () use ($validated, $user, $permission, $existing) {
            if ($validated['action'] === 'revoke') {
                $existing?->delete();
                AuditLog::log('revoke', 'permissions', [
                    'module_action' => 'user_permission_revoked',
                    'new_values' => ['target_user_id' => $user->id, 'target_user' => $user->name, 'permission' => $permission->name, 'revoked_state' => $existing?->granted ? 'granted' : 'denied'],
                ]);
            } else {
                $user->directPermissions()->updateOrCreate(
                    ['permission_id' => $permission->id],
                    [
                        'granted' => $validated['action'] === 'grant',
                        'granted_by' => auth()->id(),
                        'reason' => $validated['reason'] ?? null,
                    ]
                );
                AuditLog::log($validated['action'], 'permissions', [
                    'module_action' => 'user_permission_' . ($validated['action'] === 'grant' ? 'granted' : 'denied'),
                    'old_values' => $existing ? ['state' => $existing->granted ? 'granted' : 'denied'] : null,
                    'new_values' => ['target_user_id' => $user->id, 'target_user' => $user->name, 'permission' => $permission->name, 'reason' => $validated['reason'] ?? null],
                ]);
            }
        });

        Cache::forget("user_perms_{$user->id}");

        $msg = match ($validated['action']) {
            'grant' => "Permission '{$permission->name}' granted to {$user->name}.",
            'deny' => "Permission '{$permission->name}' denied for {$user->name}.",
            default => "Override for '{$permission->name}' removed.",
        };
        return back()->with('success', $msg);
    }

    public function changePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $this->guardSuperadminTarget($user);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password changed successfully!');
    }

    public function bulkToggleLogin(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'is_enable_login' => 'required|boolean',
        ]);

        $ownId = auth()->id();
        $actor = auth()->user();
        $lastSuperadminId = User::where('role', 'superadmin')->count() <= 1
            ? User::where('role', 'superadmin')->value('id')
            : null;

        $ids = collect($validated['ids'])
            ->reject(fn($id) => $id == $ownId)
            ->reject(fn($id) => $id == $lastSuperadminId)
            ->when(!$actor->isSuperAdmin(), function ($ids) {
                // Non-superadmin cannot toggle superadmin accounts
                return $ids->reject(fn($id) => User::where('id', $id)->where('role', 'superadmin')->exists());
            })
            ->toArray();

        User::whereIn('id', $ids)->update(['is_enable_login' => $validated['is_enable_login']]);

        foreach ($ids as $id) {
            Cache::forget("user_perms_{$id}");
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'updated' => count($ids)]);
        }

        $status = $validated['is_enable_login'] ? 'enabled' : 'disabled';
        return back()->with('success', count($ids) . ' users ' . $status . ' successfully.');
    }

    public function loginHistory()
    {
        $histories = LoginHistory::with('user')->latest()->paginate(15);
        return view('admin.users.login-history', compact('histories'));
    }

    public function impersonate(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot impersonate yourself.');
        }

        session(['impersonated_by' => auth()->id()]);
        auth()->login($user);

        return redirect()->route('role.dashboard');
    }

    public function stopImpersonating()
    {
        $originalId = session('impersonated_by');
        if ($originalId) {
            session()->forget('impersonated_by');
            auth()->loginUsingId($originalId);
        }
        return redirect()->route('role.dashboard');
    }
}
