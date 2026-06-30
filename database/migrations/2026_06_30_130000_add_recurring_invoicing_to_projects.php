<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'recurring_invoicing')) {
                    $table->boolean('recurring_invoicing')->default(false)->after('budget');
                }
                if (!Schema::hasColumn('projects', 'billing_frequency')) {
                    $table->string('billing_frequency')->default('monthly')->after('recurring_invoicing');
                }
                if (!Schema::hasColumn('projects', 'billing_amount')) {
                    $table->decimal('billing_amount', 14, 2)->default(0)->after('billing_frequency');
                }
                if (!Schema::hasColumn('projects', 'billing_day')) {
                    $table->integer('billing_day')->default(1)->after('billing_amount');
                }
                if (!Schema::hasColumn('projects', 'last_invoiced_at')) {
                    $table->timestamp('last_invoiced_at')->nullable()->after('billing_day');
                }
                if (!Schema::hasColumn('projects', 'invoicing_end_date')) {
                    $table->date('invoicing_end_date')->nullable()->after('last_invoiced_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (Schema::hasColumn('projects', 'invoicing_end_date')) {
                    $table->dropColumn('invoicing_end_date');
                }
                if (Schema::hasColumn('projects', 'last_invoiced_at')) {
                    $table->dropColumn('last_invoiced_at');
                }
                if (Schema::hasColumn('projects', 'billing_day')) {
                    $table->dropColumn('billing_day');
                }
                if (Schema::hasColumn('projects', 'billing_amount')) {
                    $table->dropColumn('billing_amount');
                }
                if (Schema::hasColumn('projects', 'billing_frequency')) {
                    $table->dropColumn('billing_frequency');
                }
                if (Schema::hasColumn('projects', 'recurring_invoicing')) {
                    $table->dropColumn('recurring_invoicing');
                }
            });
        }
    }
};
