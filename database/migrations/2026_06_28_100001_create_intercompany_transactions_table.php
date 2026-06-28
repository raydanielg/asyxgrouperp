<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intercompany_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('from_company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('to_company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('type'); // invoice, transfer, shared_service, shared_staff, loan
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('currency', 3)->default('TZS');
            $table->date('transaction_date');
            $table->string('reference_type')->nullable(); // invoice, transfer, project, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, completed, eliminated
            $table->timestamp('eliminated_at')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('intercompany_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intercompany_transaction_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intercompany_lines');
        Schema::dropIfExists('intercompany_transactions');
    }
};
