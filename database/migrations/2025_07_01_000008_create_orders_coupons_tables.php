<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('plan_name')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('txn_id')->nullable();
            $table->enum('payment_status', ['pending', 'succeeded', 'failed', 'refunded'])->default('pending');
            $table->string('payment_type')->default('bank_transfer');
            $table->string('receipt')->nullable();
            $table->foreignId('created_by')->nullable()->index();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('code')->unique();
            $table->decimal('discount', 10, 2)->default(0);
            $table->integer('limit')->nullable();
            $table->enum('type', ['percentage', 'flat', 'fixed'])->default('percentage');
            $table->decimal('minimum_spend', 10, 2)->nullable();
            $table->decimal('maximum_spend', 10, 2)->nullable();
            $table->integer('limit_per_user')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->json('included_module')->nullable();
            $table->json('excluded_module')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->nullable()->index();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('user_id');
            $table->string('order_id')->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_coupons');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('orders');
    }
};
