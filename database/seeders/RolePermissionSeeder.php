<?php

namespace Database\Seeders;

use App\Http\Controllers\RolePageController;
use App\Support\RolePermissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ═══════════════════════════════════════════════════════
        // PERMISSIONS — driven by App\Support\RolePermissions
        // (the single source of truth), so the DB always matches
        // what the runtime permission checks expect.
        // ═══════════════════════════════════════════════════════
        foreach (RolePermissions::ALL_PERMISSIONS as $permName) {
            $existing = DB::table('permissions')->where('name', $permName)->first();
            if (!$existing) {
                DB::table('permissions')->insert([
                    'name' => $permName,
                    'label' => ucwords(str_replace('-', ' ', $permName)),
                    'module' => 'Role Based',
                    'group' => 'roles',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $permMap = DB::table('permissions')->pluck('id', 'name')->toArray();
        $labels = RolePageController::roleLabels();

        // ═══════════════════════════════════════════════════════
        // ROLES — every role defined in RolePermissions plus the
        // two system-level roles (admin/superadmin = full access).
        // ═══════════════════════════════════════════════════════
        $roleNames = array_merge(
            ['admin', 'superadmin'],
            array_keys(RolePermissions::ROLE_PERMISSIONS)
        );

        foreach ($roleNames as $roleName) {
            $perms = RolePermissions::forRole($roleName);
            $label = $labels[$roleName] ?? ucwords(str_replace('_', ' ', $roleName));

            $role = DB::table('roles')->where('name', $roleName)->first();
            if (!$role) {
                $roleId = DB::table('roles')->insertGetId([
                    'name' => $roleName,
                    'label' => $label,
                    'editable' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $roleId = $role->id;
                DB::table('roles')->where('id', $roleId)->update([
                    'label' => $label,
                    'updated_at' => $now,
                ]);
                DB::table('role_permission')->where('role_id', $roleId)->delete();
            }

            foreach ($perms as $permName) {
                if (isset($permMap[$permName])) {
                    DB::table('role_permission')->insertOrIgnore([
                        'role_id' => $roleId,
                        'permission_id' => $permMap[$permName],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // ═══════════════════════════════════════════════════════
        // Remove roles that are NOT part of the spec role catalog.
        // Users on obsolete roles keep their accounts but are moved
        // to the least-privilege employee baseline — never deleted.
        // ═══════════════════════════════════════════════════════
        $obsoleteRoles = DB::table('roles')->whereNotIn('name', $roleNames)->get();

        foreach ($obsoleteRoles as $obsolete) {
            DB::table('role_user')->where('role_id', $obsolete->id)->delete();
            DB::table('role_permission')->where('role_id', $obsolete->id)->delete();
            DB::table('roles')->where('id', $obsolete->id)->delete();

            DB::table('users')->where('role', $obsolete->name)->update(['role' => 'employee']);
        }

        // Ensure every user with a supported role column also has the
        // matching role_user pivot row so hasRole()/pivot checks agree.
        $rolesByName = DB::table('roles')->pluck('id', 'name');
        $users = DB::table('users')->whereIn('role', $roleNames)->get(['id', 'role']);
        foreach ($users as $user) {
            $roleId = $rolesByName[$user->role] ?? null;
            if ($roleId) {
                DB::table('role_user')->updateOrInsert(
                    ['user_id' => $user->id, 'role_id' => $roleId],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }
}
