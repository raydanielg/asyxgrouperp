<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a workflow status to stock transfers (SRS 3.8 Inventory & Warehousing:
     * "Stock Transfers"). Without this column, RoleDashboardController's queries
     * against Transfer::where('status', 'pending') throw a QueryException, which
     * getSafeStatsForRole() silently swallows — resulting in EVERY stat/KPI for
     * procurement, inventory, store, logistics, fleet and operations roles
     * rendering as blank/zero (the whole $stats array is lost, not just this key).
     */
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('transfers', 'status')) {
                $table->string('status')->default('pending')->after('quantity');
            }
            if (!Schema::hasColumn('transfers', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('transfers', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            if (Schema::hasColumn('transfers', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }
            if (Schema::hasColumn('transfers', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('transfers', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
