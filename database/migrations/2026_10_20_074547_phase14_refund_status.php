<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Disambiguate refund_status.
     *
     * New states: none | awaiting_reconciliation | confirmed | failed | cancelled
     *
     * Legacy 'processed' records are mapped to 'awaiting_reconciliation' —
     * they were internal records only and must not be presented as
     * confirmed external money movement. No historical refund is marked
     * 'confirmed' without evidence.
     */
    public function up(): void
    {
        Schema::table('pos_returns', function (Blueprint $table) {
            $table->foreignId('refund_confirmed_by')->nullable()->after('refund_status')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('refund_confirmed_at')->nullable()->after('refund_confirmed_by');
            $table->string('refund_reference')->nullable()->after('refund_confirmed_at');
            $table->text('refund_notes')->nullable()->after('refund_reference');
        });

        // Legacy 'processed' → 'awaiting_reconciliation' (internal record only)
        DB::table('pos_returns')->where('refund_status', 'processed')
            ->update(['refund_status' => 'awaiting_reconciliation']);
    }

    public function down(): void
    {
        DB::table('pos_returns')->where('refund_status', 'awaiting_reconciliation')
            ->update(['refund_status' => 'processed']);
        Schema::table('pos_returns', function (Blueprint $table) {
            $table->dropForeign(['refund_confirmed_by']);
            $table->dropColumn(['refund_confirmed_by', 'refund_confirmed_at', 'refund_reference', 'refund_notes']);
        });
    }
};
