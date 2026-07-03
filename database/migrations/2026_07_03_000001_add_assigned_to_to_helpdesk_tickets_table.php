<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds ticket-assignment support (SRS 3.6 Helpdesk & Ticketing: "Ticket assignment").
     * Without this column, RoleDashboardController's queries against
     * HelpdeskTicket::where('assigned_to', ...) throw a QueryException, which
     * getSafeStatsForRole() silently swallows — resulting in every stat/KPI for
     * technicians and technical/service-desk roles rendering as blank/zero.
     */
    public function up(): void
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('helpdesk_tickets', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('helpdesk_tickets', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('assigned_to');
            }
        });
    }

    public function down(): void
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('helpdesk_tickets', 'assigned_to')) {
                $table->dropConstrainedForeignId('assigned_to');
            }
            if (Schema::hasColumn('helpdesk_tickets', 'assigned_at')) {
                $table->dropColumn('assigned_at');
            }
        });
    }
};
