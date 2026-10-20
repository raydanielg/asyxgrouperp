<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Source-attributed stock reservations.
     *
     * FK-backed source linkage (transfer_id) — no polymorphic guesswork.
     * Future sources (sales orders etc.) add their own FK column here.
     *
     * Statuses: active → consumed | released
     * Outstanding = quantity − consumed_quantity (for partial consumption).
     * History is never deleted — released/consumed rows stay for audit.
     */
    public function up(): void
    {
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transfer_id')->nullable()->constrained('transfers')->nullOnDelete();
            $table->decimal('quantity', 15, 2);
            $table->decimal('consumed_quantity', 15, 2)->default(0);
            $table->string('status')->default('active'); // active | consumed | released
            $table->string('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['product_id', 'warehouse_id', 'status']);
            $table->index(['company_id', 'status']);
            // One active reservation per transfer prevents duplicate holds
            // on retries — enforced in service (DB-level uniqueness is not
            // portable for nullable partial indexes on all engines).
        });

        // Backfill: open transfers (approved / partially_transferred) get
        // reservation records for their outstanding quantity so that the
        // aggregate reserved_quantity rows become traceable.
        DB::statement("
            INSERT INTO stock_reservations (company_id, product_id, warehouse_id, transfer_id, quantity, consumed_quantity, status, created_by, created_at, updated_at)
            SELECT company_id, product_id, from_warehouse, id, quantity - transferred_quantity, 0, 'active', created_by, datetime('now'), datetime('now')
            FROM transfers
            WHERE status IN ('approved', 'partially_transferred')
              AND product_id IS NOT NULL
              AND (quantity - transferred_quantity) > 0
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_reservations');
    }
};
