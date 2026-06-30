<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Employee-Project pivot (many-to-many: one employee can work on multiple projects)
        Schema::create('employee_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('role')->nullable(); // e.g. Developer, Supervisor, Technician
            $table->date('assigned_from')->nullable();
            $table->date('assigned_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['employee_id', 'project_id']);
        });

        // Employee bonuses (linked to projects)
        Schema::create('employee_bonuses', function (Blueprint $table) {
            $table->id();
            $table->string('bonus_number')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('performance'); // performance, project_completion, milestone, special
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('bonus_date');
            $table->string('status')->default('pending'); // pending, approved, paid, rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_bonuses');
        Schema::dropIfExists('employee_project');
    }
};
