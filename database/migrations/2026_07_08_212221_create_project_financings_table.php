<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_financings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('type')->default('internal');
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->unsignedSmallInteger('repayment_period_months')->nullable();
            $table->string('status')->default('active');
            $table->date('disbursed_at');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_financings');
    }
};
