<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Attach warehouses to branches. NULL = unassigned (company-wide, legacy).
     * Unassigned warehouses remain visible to all branch-restricted users of
     * the same company — documented legacy-data policy.
     */
    public function up(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')
                ->constrained('branches')->nullOnDelete();
            $table->index(['branch_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropIndex(['branch_id', 'is_active']);
            $table->dropColumn('branch_id');
        });
    }
};
