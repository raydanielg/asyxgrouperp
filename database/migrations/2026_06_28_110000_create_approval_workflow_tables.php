<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══ Approval Workflow Definitions ═══
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('module'); // lpo, office_expense, budget, vendor_invoice, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('approval_workflows')->cascadeOnDelete();
            $table->integer('level')->default(1);
            $table->string('name');
            $table->string('approver_role')->nullable(); // role name
            $table->foreignId('approver_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approver_type')->default('role'); // role, user, manager
            $table->boolean('is_final')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // ═══ Approval Requests (instances) ═══
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('workflow_id')->constrained('approval_workflows')->cascadeOnDelete();
            $table->string('module'); // lpo, office_expense, budget, etc.
            $table->unsignedBigInteger('module_id');
            $table->string('module_label')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->integer('current_level')->default(1);
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('approval_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_request_id')->constrained('approval_requests')->cascadeOnDelete();
            $table->integer('level');
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action')->default('pending'); // pending, approved, rejected
            $table->text('comment')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_tracks');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_steps');
        Schema::dropIfExists('approval_workflows');
    }
};
