<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('call_center_action_points', function (Blueprint $table) {
            $table->string('approval_status')->default('pending')->after('raw_data');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('call_center_action_points', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at']);
        });
    }
};
