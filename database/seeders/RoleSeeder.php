<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Legacy seeder — superseded by RolePermissionSeeder, which is now the
 * single source of truth for the consolidated 11-role structure
 * (superadmin, admin, director, accountant, finance_manager,
 * procurement_manager, sales_manager, project_manager, technical_manager,
 * operations_manager, hr_manager). Kept as a no-op to avoid breaking any
 * existing DatabaseSeeder::call() references.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Intentionally empty. See RolePermissionSeeder.
    }
}
