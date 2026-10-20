<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Partial warehouse transfers — track quantity moved so far.
     * Statuses: pending → approved (reserved) → partially_transferred → completed
     *           pending/approved → cancelled (reservation released)
     */
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->decimal('transferred_quantity', 15, 2)->default(0)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn('transferred_quantity');
        });
    }
};
