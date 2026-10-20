<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * transfers.company_id — enables real company isolation through the
     * existing BelongsToCompany trait (which was previously skipping this
     * table because the column didn't exist). Backfilled from the source
     * warehouse's company; records whose source warehouse is missing stay
     * NULL and remain visible to Super Admin only.
     */
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')
                ->constrained('companies')->nullOnDelete();
            $table->index('company_id');
        });

        // Backfill from the source warehouse's company where possible
        DB::statement('UPDATE transfers SET company_id = (SELECT company_id FROM warehouses WHERE warehouses.id = transfers.from_warehouse) WHERE company_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
