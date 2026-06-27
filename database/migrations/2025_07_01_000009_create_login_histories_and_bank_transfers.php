<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('login_histories')) {
            Schema::create('login_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip', 45);
                $table->date('date');
                $table->json('details')->nullable();
                $table->string('type', 50)->default('login');
                $table->foreignId('created_by')->nullable()->index();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('bank_transfer_payments')) {
            Schema::create('bank_transfer_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->decimal('price', 10, 2)->nullable();
                $table->string('order_id')->unique();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->string('price_currency', 3)->default('USD');
                $table->string('attachment')->nullable();
                $table->longText('request')->nullable();
                $table->string('type')->nullable();
                $table->foreignId('created_by')->nullable()->index();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transfer_payments');
        Schema::dropIfExists('login_histories');
    }
};
