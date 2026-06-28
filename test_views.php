<?php
$_SERVER['HTTP_HOST']='localhost';
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test login history view - need to mock data
$user = App\Models\User::first();
if (!$user) { echo "No users found - run seed\n"; exit(1); }
auth()->login($user);

$pass = 0; $fail = 0;

// Share $errors like middleware does
view()->share('errors', new Illuminate\Support\ViewErrorBag);

// Test login history
try {
  $histories = App\Models\LoginHistory::with('user')->latest()->paginate(15);
  view('admin.users.login-history', compact('histories'))->render();
  echo "login-history: OK\n"; $pass++;
} catch (Throwable $e) { echo "login-history: FAIL - " . $e->getMessage() . "\n"; $fail++; }

// Test roles index
try {
  $roles = Spatie\Permission\Models\Role::withCount('permissions')->with('users')->latest()->paginate(15);
  view('admin.roles.index', compact('roles'))->render();
  echo "roles.index: OK\n"; $pass++;
} catch (Throwable $e) { echo "roles.index: FAIL - " . $e->getMessage() . "\n"; $fail++; }

// Test profile
try {
  view('admin.profile', ['user' => $user])->render();
  echo "profile: OK\n"; $pass++;
} catch (Throwable $e) { echo "profile: FAIL - " . $e->getMessage() . "\n"; $fail++; }

// Test users create
try {
  $roles = Spatie\Permission\Models\Role::with('permissions')->get();
  view('admin.users.create', compact('roles'))->render();
  echo "users.create: OK\n"; $pass++;
} catch (Throwable $e) { echo "users.create: FAIL - " . $e->getMessage() . "\n"; $fail++; }

// Test roles create
try {
  $permissions = Spatie\Permission\Models\Permission::all()->groupBy(function($p) {
    return explode('-', $p->name, 2)[0] ?? 'general';
  });
  view('admin.roles.create', compact('permissions'))->render();
  echo "roles.create: OK\n"; $pass++;
} catch (Throwable $e) { echo "roles.create: FAIL - " . $e->getMessage() . "\n"; $fail++; }

// Test roles edit
try {
  $role = Spatie\Permission\Models\Role::first();
  $permissions = Spatie\Permission\Models\Permission::all()->groupBy(function($p) {
    return explode('-', $p->name, 2)[0] ?? 'general';
  });
  $rolePermissions = $role->permissions->pluck('name')->toArray();
  view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'))->render();
  echo "roles.edit: OK\n"; $pass++;
} catch (Throwable $e) { echo "roles.edit: FAIL - " . $e->getMessage() . "\n"; $fail++; }

// Test dashboard.role
try {
  $role = 'admin';
  $roleLabel = 'Administrator';
  $stats = ['totalUsers' => 1, 'totalEmployees' => 1];
  $kpiCards = [];
  $recentItems = [];
  $quickActions = [];
  $chartData = [];
  $secondaryKpis = [];
  $money = fn($n) => 'TZS ' . number_format($n);
  view('dashboard.role', compact('role','roleLabel','stats','recentItems','kpiCards','quickActions','money','chartData','secondaryKpis'))->render();
  echo "dashboard.role: OK\n"; $pass++;
} catch (Throwable $e) { echo "dashboard.role: FAIL - " . $e->getMessage() . "\n"; $fail++; }

echo "\nResults: $pass passed, $fail failed\n";
exit($fail > 0 ? 1 : 0);
