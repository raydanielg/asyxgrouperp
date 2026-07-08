<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('expenses', 'project_id')) {
                    $table->foreignId('project_id')->nullable()->after('bank_account_id')->constrained('projects')->nullOnDelete();
                }
                if (!Schema::hasColumn('expenses', 'cash_account_id')) {
                    $table->foreignId('cash_account_id')->nullable()->after('project_id')->constrained('cash_accounts')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('revenues')) {
            Schema::table('revenues', function (Blueprint $table) {
                if (!Schema::hasColumn('revenues', 'project_id')) {
                    $table->foreignId('project_id')->nullable()->after('bank_account_id')->constrained('projects')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'cash_account_id')) {
                    $table->dropConstrainedForeignId('cash_account_id');
                }
                if (Schema::hasColumn('expenses', 'project_id')) {
                    $table->dropConstrainedForeignId('project_id');
                }
            });
        }
        if (Schema::hasTable('revenues')) {
            Schema::table('revenues', function (Blueprint $table) {
                if (Schema::hasColumn('revenues', 'project_id')) {
                    $table->dropConstrainedForeignId('project_id');
                }
            });
        }
    }
};
