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
        Schema::create('cost_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cost_center_id')->constrained()->cascadeOnDelete();
            $table->morphs('cost_allocatable'); // expense_id, revenue_id, bill_id, etc.
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->timestamps();

            $table->index(['cost_allocatable_type', 'cost_allocatable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_allocations');
    }
};
