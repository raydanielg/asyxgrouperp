<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                return redirect()->route('login');
            }
            $user = auth()->user();
            if ($user->role === 'admin' || $user->hasRole('admin')) {
                return $next($request);
            }
            abort(403, 'Only super administrators can perform this action.');
        });
    }

    public function setSystemMode(Request $request)
    {
        $request->validate(['mode' => 'required|in:maintenance,online']);

        $mode = $request->mode === 'maintenance';

        // Update .env APP_MAINTENANCE or config file
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $content = file_get_contents($envPath);
            $line = 'APP_MAINTENANCE_MODE=' . ($mode ? 'true' : 'false');
            if (preg_match('/^APP_MAINTENANCE_MODE=/m', $content)) {
                $content = preg_replace('/^APP_MAINTENANCE_MODE=.*/m', $line, $content);
            } else {
                $content .= "\n" . $line;
            }
            file_put_contents($envPath, $content);
        }

        config(['app.maintenance_mode' => $mode]);

        // If maintenance mode, log out non-admin users
        if ($mode) {
            DB::table('sessions')->where('user_id', '!=', auth()->id())->delete();
        }

        return response()->json([
            'success' => true,
            'message' => $mode ? 'Maintenance mode enabled.' : 'System is now online.'
        ]);
    }

    public function downloadBackup()
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host') ?: '127.0.0.1';
        $filename = 'backup-' . date('Y-m-d_His') . '.sql';
        $path = storage_path('app/' . $filename);

        $command = sprintf(
            'mysqldump -h %s -u %s %s %s > %s',
            escapeshellarg($host),
            escapeshellarg($username),
            $password ? '-p' . escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($path)
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($path)) {
            return response()->json(['success' => false, 'message' => 'Backup failed. Ensure mysqldump is available.'], 500);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ])->deleteFileAfterSend(true);
    }
}
