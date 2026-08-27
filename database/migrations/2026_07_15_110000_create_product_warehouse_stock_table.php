<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('product_warehouse_stock')) {
            Schema::create('product_warehouse_stock', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
                $table->decimal('quantity', 14, 2)->default(0);
                $table->decimal('reserved_quantity', 14, 2)->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'warehouse_id']);
                $table->index(['warehouse_id', 'quantity']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_warehouse_stock');
    }
};
