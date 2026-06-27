<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sales_invoices')) {
            Schema::create('sales_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number');
                $table->date('invoice_date');
                $table->date('due_date');
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->decimal('balance_amount', 15, 2)->default(0);
                $table->enum('status', ['draft', 'posted', 'partial', 'paid', 'overdue'])->default('draft');
                $table->enum('type', ['product', 'service'])->default('product');
                $table->string('payment_terms')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();
                $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sales_invoice_items')) {
            Schema::create('sales_invoice_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id');
                $table->string('product_name');
                $table->integer('quantity');
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('discount_percentage', 5, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_percentage', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->foreign('invoice_id')->references('id')->on('sales_invoices')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sales_invoice_returns')) {
            Schema::create('sales_invoice_returns', function (Blueprint $table) {
                $table->id();
                $table->string('return_number');
                $table->date('return_date');
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->unsignedBigInteger('original_invoice_id');
                $table->enum('reason', ['defective', 'wrong_item', 'damaged', 'excess_quantity', 'other'])->default('defective');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->enum('status', ['draft', 'approved', 'completed', 'cancelled'])->default('draft');
                $table->text('notes')->nullable();
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();
                $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('original_invoice_id')->references('id')->on('sales_invoices')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sales_invoice_return_items')) {
            Schema::create('sales_invoice_return_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('return_id');
                $table->string('product_name');
                $table->integer('quantity');
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->foreign('return_id')->references('id')->on('sales_invoice_returns')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_return_items');
        Schema::dropIfExists('sales_invoice_returns');
        Schema::dropIfExists('sales_invoice_items');
        Schema::dropIfExists('sales_invoices');
    }
};
