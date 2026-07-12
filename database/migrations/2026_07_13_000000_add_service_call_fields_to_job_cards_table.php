<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->string('csr_no')->nullable()->after('job_number');
            $table->date('report_date')->nullable()->after('csr_no');
            $table->string('customer_name')->nullable()->after('report_date');
            $table->text('customer_address')->nullable()->after('customer_name');
            $table->string('branch_name')->nullable()->after('customer_address');
            $table->string('department')->nullable()->after('branch_name');
            $table->text('equipment_type')->nullable()->after('department');
            $table->string('make_brand')->nullable()->after('equipment_type');
            $table->string('model')->nullable()->after('make_brand');
            $table->text('serial_number')->nullable()->after('model');
            $table->string('call_type')->nullable()->after('serial_number');
            $table->text('problem_reported')->nullable()->after('call_type');
            $table->text('defects_found')->nullable()->after('problem_reported');
            $table->text('action_taken')->nullable()->after('defects_found');
            $table->string('end_user_name')->nullable()->after('action_taken');
            $table->text('end_user_signature')->nullable()->after('end_user_name');
            $table->timestamp('end_user_signed_at')->nullable()->after('end_user_signature');
            $table->string('technician_name')->nullable()->after('end_user_signed_at');
            $table->text('technician_signature')->nullable()->after('technician_name');
            $table->timestamp('technician_signed_at')->nullable()->after('technician_signature');
            $table->foreignId('approved_by')->nullable()->after('technician_signed_at')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->string('payment_status')->default('pending')->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'csr_no', 'report_date', 'customer_name', 'customer_address', 'branch_name',
                'department', 'equipment_type', 'make_brand', 'model', 'serial_number',
                'call_type', 'problem_reported', 'defects_found', 'action_taken',
                'end_user_name', 'end_user_signature', 'end_user_signed_at',
                'technician_name', 'technician_signature', 'technician_signed_at',
                'approved_by', 'approved_at', 'payment_status',
            ]);
        });
    }
};
