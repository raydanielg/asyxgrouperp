<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'nhif_opt_in')) {
                    $table->boolean('nhif_opt_in')->default(false)->after('salary');
                }
                if (!Schema::hasColumn('employees', 'has_student_loan')) {
                    $table->boolean('has_student_loan')->default(false)->after('nhif_opt_in');
                }
                if (!Schema::hasColumn('employees', 'student_loan_rate')) {
                    $table->decimal('student_loan_rate', 5, 2)->default(15.00)->after('has_student_loan');
                }
                if (!Schema::hasColumn('employees', 'bank_loan_deduction')) {
                    $table->decimal('bank_loan_deduction', 12, 2)->default(0)->after('student_loan_rate');
                }
                if (!Schema::hasColumn('employees', 'employer_loan_deduction')) {
                    $table->decimal('employer_loan_deduction', 12, 2)->default(0)->after('bank_loan_deduction');
                }
            });
        }

        if (!Schema::hasTable('salary_advances')) {
            Schema::create('salary_advances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->date('advance_date');
                $table->decimal('amount', 12, 2);
                $table->string('reason')->nullable();
                $table->timestamps();
                $table->unique(['employee_id', 'advance_date']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('salary_advances')) {
            Schema::dropIfExists('salary_advances');
        }
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                foreach (['employer_loan_deduction', 'bank_loan_deduction', 'student_loan_rate', 'has_student_loan', 'nhif_opt_in'] as $col) {
                    if (Schema::hasColumn('employees', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
