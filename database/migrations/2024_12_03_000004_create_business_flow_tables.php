<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══ 1. TENDERS ═══
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('client_name');
            $table->string('client_organization')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->decimal('estimated_value', 14, 2)->default(0);
            $table->string('status')->default('received'); // received, under_review, converted, rejected
            $table->text('requirements')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ 2. QUOTATIONS / PROPOSALS (before Lead→Deal) ═══
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->date('quotation_date');
            $table->date('valid_until')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->string('status')->default('draft'); // draft, sent, accepted, rejected, expired
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->timestamps();
        });

        // ═══ 3. PROJECT BUDGETS & APPROVAL ═══
        Schema::create('project_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('budget_number')->unique();
            $table->decimal('total_budget', 14, 2)->default(0);
            $table->decimal('procurement_budget', 14, 2)->default(0);
            $table->decimal('office_expense_budget', 14, 2)->default(0);
            $table->decimal('labor_budget', 14, 2)->default(0);
            $table->decimal('contingency', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('draft'); // draft, pending, approved, rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ 4. LPO (Local Purchase Orders) ═══
        Schema::create('lpos', function (Blueprint $table) {
            $table->id();
            $table->string('lpo_number')->unique();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier_name')->nullable();
            $table->date('lpo_date');
            $table->date('expected_delivery_date')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->string('status')->default('draft'); // draft, sent, partially_received, received, closed, cancelled
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('lpo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lpo_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity_ordered', 10, 2)->default(1);
            $table->decimal('quantity_received', 10, 2)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->timestamps();
        });

        // ═══ 5. GOODS RECEIVED NOTES (GRN) ═══
        Schema::create('grns', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->foreignId('lpo_id')->nullable()->constrained('lpos')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->date('received_date');
            $table->string('received_by')->nullable();
            $table->string('delivery_note_number')->nullable();
            $table->string('status')->default('received'); // received, discrepant, rejected
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lpo_item_id')->nullable()->constrained('lpo_items')->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity_expected', 10, 2)->default(0);
            $table->decimal('quantity_received', 10, 2)->default(0);
            $table->decimal('quantity_discrepant', 10, 2)->default(0);
            $table->string('unit')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // ═══ 6. DELIVERY NOTES ═══
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_note_number')->unique();
            $table->foreignId('lpo_id')->nullable()->constrained('lpos')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('grn_id')->nullable()->constrained('grns')->nullOnDelete();
            $table->date('delivery_date');
            $table->string('delivered_by')->nullable();
            $table->string('received_by')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('status')->default('delivered'); // delivered, pending_verification, verified
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ 7. VENDOR INVOICES ═══
        Schema::create('vendor_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_invoice_number')->unique();
            $table->foreignId('lpo_id')->nullable()->constrained('lpos')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('supplier_invoice_ref')->nullable();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('balance', 14, 2)->default(0);
            $table->string('status')->default('unpaid'); // unpaid, partially_paid, paid, overdue
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ 8. VENDOR PAYMENTS / RECEIPTS ═══
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('vendor_invoice_id')->constrained('vendor_invoices')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('payment_method')->default('bank_transfer'); // cash, bank_transfer, cheque, mobile_money
            $table->string('reference_number')->nullable();
            $table->string('status')->default('completed');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ 9. OFFICE EXPENSES (with approval workflow) ═══
        Schema::create('office_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->nullable(); // transport, supplies, meals, utilities, misc
            $table->string('description');
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('expense_date');
            $table->string('payment_method')->default('cash');
            $table->string('receipt_number')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, disbursed
            $table->text('notes')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->integer('approval_level')->default(0); // 0=pending, 1=level1, 2=level2, 3=final
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ 10. CLIENT PAYMENT RECEIPTS ═══
        Schema::create('client_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('client_name');
            $table->date('receipt_date');
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('payment_method')->default('bank_transfer');
            $table->string('reference_number')->nullable();
            $table->string('invoice_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ Add columns to existing tables ═══
        // Add deal_id to projects (for Deal→Project conversion)
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('deal_id')->nullable()->after('manager_id')->constrained('crm_deals')->nullOnDelete();
            $table->decimal('actual_cost', 14, 2)->default(0)->after('budget');
            $table->decimal('actual_revenue', 14, 2)->default(0)->after('actual_cost');
        });

        // Add tender_id to crm_leads (for Tender→Lead conversion)
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->foreignId('tender_id')->nullable()->after('lead_number')->constrained('tenders')->nullOnDelete();
        });

        // Add project_id to crm_deals (for Deal→Project tracking)
        Schema::table('crm_deals', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('lead_id')->constrained('projects')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crm_deals', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropForeign(['tender_id']);
            $table->dropColumn('tender_id');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['deal_id']);
            $table->dropColumn(['deal_id', 'actual_cost', 'actual_revenue']);
        });

        Schema::dropIfExists('client_receipts');
        Schema::dropIfExists('office_expenses');
        Schema::dropIfExists('vendor_payments');
        Schema::dropIfExists('vendor_invoices');
        Schema::dropIfExists('delivery_notes');
        Schema::dropIfExists('grn_items');
        Schema::dropIfExists('grns');
        Schema::dropIfExists('lpo_items');
        Schema::dropIfExists('lpos');
        Schema::dropIfExists('project_budgets');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('tenders');
    }
};
