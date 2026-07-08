<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('cash_accounts')) {
            Schema::create('cash_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('type'); // petty_cash | project
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                $table->foreignId('chart_of_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
                $table->string('name');
                $table->string('code')->nullable()->unique();
                $table->foreignId('custodian_id')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('opening_balance', 14, 2)->default(0);
                $table->decimal('current_balance', 14, 2)->default(0);
                $table->string('currency')->default('TZS');
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cash_account_transactions')) {
            Schema::create('cash_account_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->foreignId('cash_account_id')->constrained('cash_accounts')->cascadeOnDelete();
                $table->string('type'); // credit (money in) | debit (money out)
                $table->string('category')->nullable(); // topup, expense, transfer_out, transfer_in, adjustment
                $table->decimal('amount', 14, 2);
                $table->decimal('balance_after', 14, 2)->default(0);
                $table->text('description')->nullable();
                $table->string('reference')->nullable();
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->date('transaction_date');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cash_topup_requests')) {
            Schema::create('cash_topup_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->foreignId('cash_account_id')->constrained('cash_accounts')->cascadeOnDelete();
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                $table->foreignId('requested_by')->constrained('users');
                $table->decimal('amount', 14, 2);
                $table->text('purpose')->nullable();
                $table->string('status')->default('pending'); // pending | approved | rejected | disbursed
                $table->foreignId('approval_request_id')->nullable()->constrained('approval_requests')->nullOnDelete();
                $table->foreignId('cash_account_transaction_id')->nullable()->constrained('cash_account_transactions')->nullOnDelete();
                $table->timestamp('disbursed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_topup_requests');
        Schema::dropIfExists('cash_account_transactions');
        Schema::dropIfExists('cash_accounts');
    }
};
