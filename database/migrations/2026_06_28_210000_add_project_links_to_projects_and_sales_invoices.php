<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'customer_id')) {
                    $table->foreignId('customer_id')->nullable()->after('project_number')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('projects', 'proposal_id')) {
                    $table->foreignId('proposal_id')->nullable()->after('customer_id')->constrained('sales_proposals')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('sales_invoices')) {
            Schema::table('sales_invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('sales_invoices', 'project_id')) {
                    $table->foreignId('project_id')->nullable()->after('warehouse_id')->constrained('projects')->nullOnDelete();
                }
                if (!Schema::hasColumn('sales_invoices', 'proposal_id')) {
                    $table->foreignId('proposal_id')->nullable()->after('project_id')->constrained('sales_proposals')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales_invoices')) {
            Schema::table('sales_invoices', function (Blueprint $table) {
                if (Schema::hasColumn('sales_invoices', 'proposal_id')) {
                    $table->dropConstrainedForeignId('proposal_id');
                }
                if (Schema::hasColumn('sales_invoices', 'project_id')) {
                    $table->dropConstrainedForeignId('project_id');
                }
            });
        }

        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (Schema::hasColumn('projects', 'proposal_id')) {
                    $table->dropConstrainedForeignId('proposal_id');
                }
                if (Schema::hasColumn('projects', 'customer_id')) {
                    $table->dropConstrainedForeignId('customer_id');
                }
            });
        }
    }
};
