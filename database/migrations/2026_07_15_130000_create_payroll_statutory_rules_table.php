<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payroll_statutory_rules')) {
            Schema::create('payroll_statutory_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('name');
                $table->string('type');
                $table->string('calculation_basis')->default('gross');
                $table->decimal('employee_rate', 8, 4)->default(0);
                $table->decimal('employer_rate', 8, 4)->default(0);
                $table->decimal('minimum_amount', 14, 2)->nullable();
                $table->decimal('maximum_amount', 14, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->date('effective_from');
                $table->date('effective_to')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_statutory_rules');
    }
};
