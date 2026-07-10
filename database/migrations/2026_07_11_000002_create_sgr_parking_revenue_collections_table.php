<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgr_parking_revenue_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('import_batch')->index();
            $table->string('source_filename')->nullable();
            $table->string('sheet_name')->nullable();
            $table->integer('row_number')->default(0);
            $table->string('sn')->nullable();
            $table->date('date_in')->nullable()->index();
            $table->date('date_out')->nullable()->index();
            $table->string('time_in')->nullable();
            $table->string('time_out')->nullable();
            $table->decimal('amount_collected', 15, 2)->default(0);
            $table->decimal('amount_deposited', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->string('control_no')->nullable()->index();
            $table->string('receipt_no')->nullable();
            $table->string('cashier_name')->nullable()->index();
            $table->string('control_status')->nullable();
            $table->string('comments')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->index(['import_batch', 'cashier_name']);
            $table->index(['import_batch', 'date_in']);
            $table->index(['date_in', 'cashier_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgr_parking_revenue_collections');
    }
};
