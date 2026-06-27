<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('add_ons')) {
            Schema::create('add_ons', function (Blueprint $table) {
                $table->id();
                $table->string('module');
                $table->string('name');
                $table->decimal('monthly_price', 8, 2)->default(0);
                $table->decimal('yearly_price', 8, 2)->default(0);
                $table->string('image')->nullable();
                $table->boolean('is_enable')->default(false);
                $table->string('package_name')->nullable();
                $table->integer('priority')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_active_modules')) {
            Schema::create('user_active_modules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('module');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_active_modules');
        Schema::dropIfExists('add_ons');
    }
};
