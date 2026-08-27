<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('procurement_requisitions')) {
            Schema::create('procurement_requisitions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('requisition_number');
                $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('department_id')->nullable();
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                $table->date('required_date');
                $table->string('status')->default('draft');
                $table->text('notes')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->datetime('approved_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('procurement_requisition_items')) {
            Schema::create('procurement_requisition_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('requisition_id')->constrained('procurement_requisitions')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->string('product_name');
                $table->string('sku')->nullable();
                $table->decimal('quantity', 14, 2);
                $table->string('unit')->nullable();
                $table->decimal('estimated_price', 14, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rfqs')) {
            Schema::create('rfqs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('rfq_number');
                $table->foreignId('requisition_id')->nullable()->constrained('procurement_requisitions')->nullOnDelete();
                $table->date('issue_date');
                $table->date('closing_date');
                $table->string('status')->default('open');
                $table->text('terms')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rfq_suppliers')) {
            Schema::create('rfq_suppliers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
                $table->string('supplier_name');
                $table->string('supplier_email')->nullable();
                $table->string('status')->default('invited');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rfq_responses')) {
            Schema::create('rfq_responses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
                $table->foreignId('rfq_supplier_id')->constrained('rfq_suppliers')->cascadeOnDelete();
                $table->decimal('total_amount', 14, 2)->default(0);
                $table->string('status')->default('pending');
                $table->date('response_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rfq_response_items')) {
            Schema::create('rfq_response_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rfq_response_id')->constrained('rfq_responses')->cascadeOnDelete();
                $table->foreignId('requisition_item_id')->nullable()->constrained('procurement_requisition_items')->nullOnDelete();
                $table->string('product_name');
                $table->decimal('quantity', 14, 2);
                $table->decimal('unit_price', 14, 2);
                $table->decimal('total_price', 14, 2);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_response_items');
        Schema::dropIfExists('rfq_responses');
        Schema::dropIfExists('rfq_suppliers');
        Schema::dropIfExists('rfqs');
        Schema::dropIfExists('procurement_requisition_items');
        Schema::dropIfExists('procurement_requisitions');
    }
};
