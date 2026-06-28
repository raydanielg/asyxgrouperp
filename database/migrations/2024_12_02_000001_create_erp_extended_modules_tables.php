<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══ HRM ═══
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->text('address')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('leaving_date')->nullable();
            $table->string('employment_type')->default('full_time');
            $table->decimal('salary', 12, 2)->default(0);
            $table->string('status')->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status')->default('present');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('payroll_number')->unique();
            $table->string('month');
            $table->integer('year');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('leave_type')->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('review_period')->nullable();
            $table->text('goals')->nullable();
            $table->text('achievements')->nullable();
            $table->text('feedback')->nullable();
            $table->integer('rating')->default(3);
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('trainer')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('scheduled');
            $table->timestamps();
        });

        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department')->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('location')->nullable();
            $table->string('job_type')->default('full_time');
            $table->integer('vacancies')->default(1);
            $table->date('deadline')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
        });

        Schema::create('employee_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('asset_name');
            $table->string('asset_type')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('assigned_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('status')->default('assigned');
            $table->timestamps();
        });

        Schema::create('hr_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->default('meeting');
            $table->timestamps();
        });

        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ═══ CRM ═══
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_number')->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('new');
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('crm_deals', function (Blueprint $table) {
            $table->id();
            $table->string('deal_number')->unique();
            $table->string('title');
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->decimal('value', 14, 2)->default(0);
            $table->string('stage')->default('prospecting');
            $table->date('expected_close_date')->nullable();
            $table->string('status')->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->foreignId('deal_id')->nullable()->constrained('crm_deals')->nullOnDelete();
            $table->string('client_name');
            $table->decimal('value', 14, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('terms')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ═══ Accounting ═══
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('branch')->nullable();
            $table->string('currency')->default('USD');
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->decimal('current_balance', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bank_transfers_acc', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->foreignId('from_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->foreignId('to_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('transfer_date');
            $table->text('notes')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->string('category')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('expense_date');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('payment_method')->default('cash');
            $table->string('payee')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->string('revenue_number')->unique();
            $table->string('category')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('revenue_date');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('payment_method')->default('cash');
            $table->string('payer')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->string('vendor_name');
            $table->date('bill_date');
            $table->date('due_date')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->string('status')->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->string('estimate_number')->unique();
            $table->string('client_name');
            $table->date('estimate_date');
            $table->date('expiry_date')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ═══ Projects ═══
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('planning');
            $table->string('priority')->default('medium');
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('progress')->default(0);
            $table->decimal('budget', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->string('status')->default('todo');
            $table->string('priority')->default('medium');
            $table->integer('progress')->default(0);
            $table->timestamps();
        });

        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('project_tasks')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->decimal('hours', 5, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('project_bugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('severity')->default('low');
            $table->string('status')->default('open');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ Products & Inventory ═══
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('unit')->default('piece');
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->string('type')->default('product');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('payment_terms')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->string('type');
            $table->integer('quantity');
            $table->integer('balance_after');
            $table->text('reference')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ POS ═══
        Schema::create('pos_sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number')->unique();
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('payment_method')->default('cash');
            $table->string('status')->default('completed');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pos_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_sale_items');
        Schema::dropIfExists('pos_sales');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('project_bugs');
        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('project_tasks');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('estimates');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('revenues');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('bank_transfers_acc');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('crm_contacts');
        Schema::dropIfExists('crm_contracts');
        Schema::dropIfExists('crm_deals');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('policies');
        Schema::dropIfExists('hr_events');
        Schema::dropIfExists('employee_assets');
        Schema::dropIfExists('job_postings');
        Schema::dropIfExists('trainings');
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('employees');
    }
};
