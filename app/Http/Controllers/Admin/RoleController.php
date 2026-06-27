<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
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
        $query = Role::withCount('permissions')->with('users');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%')
                  ->orWhere('label', 'like', '%' . $request->name . '%');
        }

        $roles = $query->orderBy('name')->paginate(10)->appends($request->except('page'));
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::groupedByModule();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'label' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'label' => $validated['label'],
            'editable' => true,
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        // Clear permission cache for all users with this role
        foreach ($role->users as $user) {
            cache()->forget("user_perms_{$user->id}");
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully!');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::groupedByModule();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'label' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($role->editable) {
            $role->update([
                'name' => $validated['name'],
                'label' => $validated['label'],
            ]);
        } else {
            $role->update(['label' => $validated['label']]);
        }

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->permissions()->detach();
        }

        // Clear permission cache for all users with this role
        foreach ($role->users as $user) {
            cache()->forget("user_perms_{$user->id}");
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        if (!$role->editable) {
            return redirect()->route('admin.roles.index')->with('error', 'This role cannot be deleted.');
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully!');
    }
}
