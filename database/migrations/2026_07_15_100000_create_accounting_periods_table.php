<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('accounting_periods')) {
            Schema::create('accounting_periods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('name');
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status')->default('open');
                $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->datetime('closed_at')->nullable();
                $table->text('closing_notes')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'start_date', 'end_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_periods');
    }
};
