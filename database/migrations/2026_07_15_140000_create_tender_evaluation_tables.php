<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tender_evaluations')) {
            Schema::create('tender_evaluations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tender_id')->nullable()->constrained('tenders')->cascadeOnDelete();
                $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
                $table->string('bidder_name');
                $table->decimal('technical_score', 5, 2)->default(0);
                $table->decimal('financial_score', 5, 2)->default(0);
                $table->decimal('total_score', 5, 2)->default(0);
                $table->text('comments')->nullable();
                $table->string('recommendation')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tender_checklists')) {
            Schema::create('tender_checklists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tender_id')->nullable()->constrained('tenders')->cascadeOnDelete();
                $table->string('item');
                $table->boolean('is_required')->default(true);
                $table->boolean('is_checked')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tender_bid_securities')) {
            Schema::create('tender_bid_securities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tender_id')->nullable()->constrained('tenders')->cascadeOnDelete();
                $table->string('bidder_name');
                $table->string('security_type')->nullable();
                $table->decimal('amount', 14, 2)->default(0);
                $table->string('reference_number')->nullable();
                $table->string('issuing_institution')->nullable();
                $table->date('valid_until')->nullable();
                $table->string('status')->default('submitted');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_bid_securities');
        Schema::dropIfExists('tender_checklists');
        Schema::dropIfExists('tender_evaluations');
    }
};
