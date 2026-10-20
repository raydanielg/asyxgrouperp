<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 6:
     * - journal_entries: reversal link + branch attribution
     * - branch attribution for office_expenses, vendor_payments, lpos
     * - user_notifications inbox for approval events
     * - journal lifecycle permissions (submit/approve/post/reverse/update)
     */
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreignId('reversal_of_id')->nullable()->after('status')
                ->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->after('company_id')
                ->constrained('branches')->nullOnDelete();
        });

        foreach (['office_expenses', 'vendor_payments', 'lpos'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('company_id')
                    ->constrained('branches')->nullOnDelete();
                $table->index('branch_id');
            });
        }

        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->index();           // approval_submitted | approval_granted | ...
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('link')->nullable();
            $table->string('dedupe_key')->nullable();  // prevents duplicate notifications
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->unique(['dedupe_key']);
        });

        // New journal lifecycle permissions — attach to roles that already
        // hold create-journal-entries so finance roles keep working.
        $perms = [
            'update-journal-entries' => 'Journal Entries',
            'submit-journal-entries' => 'Journal Entries',
            'approve-journal-entries' => 'Journal Entries',
            'post-journal-entries' => 'Journal Entries',
            'reverse-journal-entries' => 'Journal Entries',
        ];
        $permIds = [];
        foreach ($perms as $name => $module) {
            $id = DB::table('permissions')->where('name', $name)->value('id');
            if (!$id) {
                $id = DB::table('permissions')->insertGetId([
                    'name' => $name, 'label' => $name, 'module' => $module,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            $permIds[] = $id;
        }

        $roles = DB::table('role_permission')
            ->join('permissions', 'permissions.id', '=', 'role_permission.permission_id')
            ->where('permissions.name', 'create-journal-entries')
            ->pluck('role_permission.role_id');

        $now = now();
        foreach ($roles as $roleId) {
            foreach ($permIds as $pid) {
                $exists = DB::table('role_permission')
                    ->where('role_id', $roleId)->where('permission_id', $pid)->exists();
                if (!$exists) {
                    DB::table('role_permission')->insert([
                        'role_id' => $roleId, 'permission_id' => $pid,
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
        foreach (['office_expenses', 'vendor_payments', 'lpos'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropIndex(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['reversal_of_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['reversal_of_id', 'branch_id']);
        });
    }
};
