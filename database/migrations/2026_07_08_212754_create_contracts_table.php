<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->string('contractor_name');
            $table->string('type')->default('service');
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('contract_value', 14, 2)->default(0);
            $table->string('status')->default('draft');
            $table->text('description')->nullable();
            $table->text('terms')->nullable();
            $table->string('attachment_path')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
