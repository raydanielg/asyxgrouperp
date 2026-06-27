<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
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

    public function index()
    {
        $today = now()->startOfDay();
        $weekAgo = now()->subDays(7)->startOfDay();

        $stats = [
            'totalUsers' => User::where('role', 'user')->count(),
            'newUsersThisWeek' => User::where('role', 'user')->where('created_at', '>=', $weekAgo)->count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'totalAllUsers' => User::count(),
            'activeUsers' => User::whereNotNull('email_verified_at')->count(),
            'inactiveUsers' => User::whereNull('email_verified_at')->count(),
        ];

        $recentUsers = User::where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $allUsers = User::where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $dailyRegistrations = [];
        $dailyLabels = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels[] = $date->format('d');
            $dailyRegistrations[] = User::where('role', 'user')->whereDate('created_at', $date)->count();
        }

        $topUsers = User::where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentUsers', 'allUsers',
            'dailyRegistrations', 'dailyLabels', 'topUsers'
        ));
    }

    public function users()
    {
        $users = User::where('role', 'user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function reports()
    {
        $stats = [
            'totalUsers' => User::where('role', 'user')->count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'activeUsers' => User::whereNotNull('email_verified_at')->count(),
            'inactiveUsers' => User::whereNull('email_verified_at')->count(),
        ];
        return view('admin.reports', compact('stats'));
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
