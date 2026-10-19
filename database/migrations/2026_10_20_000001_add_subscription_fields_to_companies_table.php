<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('subscription_plan')->default('enterprise')->after('is_active');
            $table->string('subscription_status')->default('active')->after('subscription_plan');
            $table->string('billing_cycle')->nullable()->after('subscription_status');
            $table->decimal('mrr', 15, 2)->nullable()->after('billing_cycle');
            $table->unsignedInteger('max_users')->nullable()->after('mrr');
            $table->date('subscription_starts_at')->nullable()->after('max_users');
            $table->date('subscription_ends_at')->nullable()->after('subscription_starts_at');
            $table->timestamp('suspended_at')->nullable()->after('subscription_ends_at');
            $table->string('suspension_reason')->nullable()->after('suspended_at');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_plan', 'subscription_status', 'billing_cycle',
                'mrr', 'max_users', 'subscription_starts_at', 'subscription_ends_at',
                'suspended_at', 'suspension_reason',
            ]);
        });
    }
};
