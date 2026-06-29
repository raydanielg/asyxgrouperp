<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $modules = [
            'visitors', 'appointments', 'calls', 'correspondence', 'parcels',
            'front-desk', 'departments', 'announcements', 'messages', 'reports', 'my-account',
        ];

        $now = now();
        $permissionIds = [];

        foreach ($modules as $module) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $name = $action . '-' . $module;
                $existing = DB::table('permissions')->where('name', $name)->first();
                if (!$existing) {
                    $id = DB::table('permissions')->insertGetId([
                        'name' => $name,
                        'label' => ucwords($action) . ' ' . ucwords(str_replace('-', ' ', $module)),
                        'module' => ucwords(str_replace('-', ' ', $module)),
                        'group' => 'reception',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                } else {
                    $id = $existing->id;
                }
                $permissionIds[$name] = $id;
            }
        }

        $receptionistRole = DB::table('roles')->where('name', 'receptionist')->first();
        if ($receptionistRole) {
            foreach ($permissionIds as $pid) {
                DB::table('role_permission')->insertOrIgnore([
                    'role_id' => $receptionistRole->id,
                    'permission_id' => $pid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($permissionIds as $pid) {
                DB::table('role_permission')->insertOrIgnore([
                    'role_id' => $adminRole->id,
                    'permission_id' => $pid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $modules = [
            'visitors', 'appointments', 'calls', 'correspondence', 'parcels',
            'front-desk', 'departments', 'announcements', 'messages', 'reports', 'my-account',
        ];

        $names = [];
        foreach ($modules as $module) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $names[] = $action . '-' . $module;
            }
        }

        DB::table('role_permission')
            ->whereIn('permission_id', function ($query) use ($names) {
                $query->select('id')->from('permissions')->whereIn('name', $names);
            })
            ->delete();

        DB::table('permissions')->whereIn('name', $names)->delete();
    }
};
