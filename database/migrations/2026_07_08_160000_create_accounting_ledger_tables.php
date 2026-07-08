<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('chart_of_accounts')) {
            Schema::create('chart_of_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('type'); // asset, liability, equity, revenue, expense
                $table->string('subtype')->nullable(); // cash, bank, petty_cash, project_cash, accounts_receivable, accounts_payable, cost_of_sales, operating_expense, other_income, ...
                $table->string('normal_balance')->default('debit'); // debit | credit
                $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
                $table->text('description')->nullable();
                $table->boolean('is_system')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('entry_number')->unique();
                $table->date('entry_date');
                $table->string('reference')->nullable();
                $table->text('description')->nullable();
                $table->string('source_type')->nullable(); // manual, cash_topup, cash_transaction, expense, revenue, ...
                $table->unsignedBigInteger('source_id')->nullable();
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                $table->string('status')->default('posted'); // posted | void
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('posted_at')->nullable();
                $table->timestamps();

                $table->index(['source_type', 'source_id']);
            });
        }

        if (!Schema::hasTable('journal_entry_lines')) {
            Schema::create('journal_entry_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
                $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
                $table->decimal('debit', 14, 2)->default(0);
                $table->decimal('credit', 14, 2)->default(0);
                $table->string('description')->nullable();
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
    }
};
