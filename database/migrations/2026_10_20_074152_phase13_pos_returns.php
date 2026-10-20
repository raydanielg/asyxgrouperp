<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * POS returns & refunds.
     *
     * pos_returns: pending → processed (immutable after processing).
     * Stock disposition is per-item: 'restock' returns to the product's
     * warehouse; 'damaged' does NOT restore stock (documented gap:
     * no quarantine location in current schema).
     *
     * refund_status: 'none' (no monetary refund) | 'processed' (internal
     * refund record — external money movement requires a configured
     * provider and is not claimed by this implementation).
     */
    public function up(): void
    {
        Schema::create('pos_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->foreignId('pos_sale_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending | processed
            $table->text('reason')->nullable();
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->string('refund_status')->default('none'); // none | processed
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index('pos_sale_id');
            $table->index('status');
        });

        Schema::create('pos_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pos_sale_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('quantity', 15, 2);
            $table->string('disposition')->default('restock'); // restock | damaged
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->timestamps();
            $table->index('pos_sale_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_return_items');
        Schema::dropIfExists('pos_returns');
    }
};
