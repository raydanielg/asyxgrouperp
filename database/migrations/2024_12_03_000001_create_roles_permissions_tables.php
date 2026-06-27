<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->boolean('editable')->default(true);
            $table->timestamps();
        });

        // Permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->string('module')->nullable();
            $table->string('group')->nullable();
            $table->timestamps();
        });

        // Role-Permission pivot
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
        });

        // Role-User pivot
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'role_id']);
        });

        // Login History
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('login_at')->useCurrent();
            $table->timestamp('logout_at')->nullable();
            $table->timestamps();
        });

        // Seed default permissions
        $modules = [
            'Dashboard' => ['view-dashboard'],
            'Users' => ['view-users', 'create-users', 'edit-users', 'delete-users', 'change-password-users', 'impersonate-users', 'view-login-history'],
            'Roles' => ['view-roles', 'create-roles', 'edit-roles', 'delete-roles', 'manage-roles'],
            'Employees' => ['view-employees', 'create-employees', 'edit-employees', 'delete-employees'],
            'Attendance' => ['view-attendance', 'create-attendance', 'delete-attendance'],
            'Payroll' => ['view-payroll', 'create-payroll', 'delete-payroll'],
            'Leaves' => ['view-leaves', 'create-leaves', 'approve-leaves', 'delete-leaves'],
            'Performance' => ['view-performance', 'create-performance', 'delete-performance'],
            'Training' => ['view-training', 'create-training', 'delete-training'],
            'Recruitment' => ['view-recruitment', 'create-recruitment', 'delete-recruitment'],
            'Assets' => ['view-assets', 'create-assets', 'delete-assets'],
            'Events' => ['view-events', 'create-events', 'delete-events'],
            'Policies' => ['view-policies', 'create-policies', 'delete-policies'],
            'CRM Leads' => ['view-crm-leads', 'create-crm-leads', 'edit-crm-leads', 'delete-crm-leads'],
            'CRM Deals' => ['view-crm-deals', 'create-crm-deals', 'edit-crm-deals', 'delete-crm-deals'],
            'CRM Contracts' => ['view-crm-contracts', 'create-crm-contracts', 'delete-crm-contracts'],
            'CRM Contacts' => ['view-crm-contacts', 'create-crm-contacts', 'delete-crm-contacts'],
            'Bank Accounts' => ['view-bank-accounts', 'create-bank-accounts', 'delete-bank-accounts'],
            'Transfers' => ['view-acc-transfers', 'create-acc-transfers', 'delete-acc-transfers'],
            'Expenses' => ['view-expenses', 'create-expenses', 'delete-expenses'],
            'Revenues' => ['view-revenues', 'create-revenues', 'delete-revenues'],
            'Bills' => ['view-bills', 'create-bills', 'delete-bills'],
            'Projects' => ['view-projects', 'create-projects', 'edit-projects', 'delete-projects'],
            'Timesheets' => ['view-timesheets', 'create-timesheets', 'delete-timesheets'],
            'Bugs' => ['view-bugs', 'create-bugs', 'delete-bugs'],
            'Products' => ['view-products', 'create-products', 'edit-products', 'delete-products'],
            'Categories' => ['view-product-categories', 'create-product-categories', 'delete-product-categories'],
            'Suppliers' => ['view-suppliers', 'create-suppliers', 'delete-suppliers'],
            'Stock' => ['view-stock-movements', 'create-stock-movements'],
            'POS' => ['view-pos', 'create-pos', 'delete-pos'],
            'Sales' => ['view-sales-invoices', 'create-sales-invoices', 'edit-sales-invoices', 'delete-sales-invoices'],
            'Purchases' => ['view-purchase-invoices', 'create-purchase-invoices', 'edit-purchase-invoices', 'delete-purchase-invoices'],
            'Warehouses' => ['view-warehouses', 'create-warehouses', 'edit-warehouses', 'delete-warehouses'],
            'Helpdesk' => ['view-helpdesk-tickets', 'create-helpdesk-tickets', 'edit-helpdesk-tickets', 'delete-helpdesk-tickets'],
            'Settings' => ['view-settings', 'edit-settings'],
            'Reports' => ['view-reports'],
        ];

        $now = now();
        foreach ($modules as $module => $perms) {
            foreach ($perms as $perm) {
                \DB::table('permissions')->insert([
                    'name' => $perm,
                    'label' => ucwords(str_replace('-', ' ', $perm)),
                    'module' => $module,
                    'group' => 'core',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Create default Admin role with all permissions
        $adminRoleId = \DB::table('roles')->insertGetId([
            'name' => 'admin',
            'label' => 'Administrator',
            'editable' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $allPermIds = \DB::table('permissions')->pluck('id');
        foreach ($allPermIds as $pid) {
            \DB::table('role_permission')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $pid,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Create Manager role
        $managerRoleId = \DB::table('roles')->insertGetId([
            'name' => 'manager',
            'label' => 'Manager',
            'editable' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create Employee role with limited permissions
        $employeeRoleId = \DB::table('roles')->insertGetId([
            'name' => 'employee',
            'label' => 'Employee',
            'editable' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $employeePerms = ['view-dashboard', 'view-projects', 'view-timesheets', 'create-timesheets', 'view-bugs', 'create-bugs'];
        foreach ($employeePerms as $pname) {
            $pid = \DB::table('permissions')->where('name', $pname)->value('id');
            if ($pid) {
                \DB::table('role_permission')->insert([
                    'role_id' => $employeeRoleId,
                    'permission_id' => $pid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Assign admin role to existing admin users
        $adminUsers = \DB::table('users')->where('role', 'admin')->get();
        foreach ($adminUsers as $user) {
            \DB::table('role_user')->insertOrIgnore([
                'user_id' => $user->id,
                'role_id' => $adminRoleId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
