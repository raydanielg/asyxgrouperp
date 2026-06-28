<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\LoginHistory;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

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

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->except('page'));
        $roles = Role::orderBy('name')->pluck('label', 'name')->toArray();

        return view('admin.users.index', compact('users', 'roles'));
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
            'role' => 'required|in:admin,user',
            'company_id' => 'nullable|exists:companies,id',
            'assigned_roles' => 'array',
            'assigned_roles.*' => 'exists:roles,id',
            'is_enable_login' => 'boolean',
        ]);

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

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        $companies = Company::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles', 'companies'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,user',
            'company_id' => 'nullable|exists:companies,id',
            'assigned_roles' => 'array',
            'assigned_roles.*' => 'exists:roles,id',
            'is_enable_login' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'company_id' => $validated['company_id'] ?? null,
            'is_enable_login' => $request->boolean('is_enable_login', true),
        ]);

        if (!empty($validated['assigned_roles'])) {
            $user->roles()->sync($validated['assigned_roles']);
        } else {
            $user->roles()->detach();
        }

        Cache::forget("user_perms_{$user->id}");

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    public function changePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('admin.users.index')->with('success', 'Password changed successfully!');
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

        return redirect()->route('admin.dashboard');
    }

    public function stopImpersonating()
    {
        $originalId = session('impersonated_by');
        if ($originalId) {
            session()->forget('impersonated_by');
            auth()->loginUsingId($originalId);
        }
        return redirect()->route('admin.dashboard');
    }
}
