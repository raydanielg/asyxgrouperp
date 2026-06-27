<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transfers')) {
            Schema::create('transfers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('from_warehouse')->nullable()->index();
                $table->unsignedBigInteger('to_warehouse')->nullable()->index();
                $table->string('product_name')->nullable();
                $table->decimal('quantity', 15, 2)->default(0);
                $table->date('date')->nullable();
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();
                $table->foreign('from_warehouse')->references('id')->on('warehouses')->onDelete('cascade');
                $table->foreign('to_warehouse')->references('id')->on('warehouses')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ch_messages')) {
            Schema::create('ch_messages', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('from_id');
                $table->bigInteger('to_id');
                $table->string('body', 5000)->nullable();
                $table->string('attachment')->nullable();
                $table->boolean('seen')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ch_favorites')) {
            Schema::create('ch_favorites', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('user_id');
                $table->bigInteger('favorite_id');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ch_favorites');
        Schema::dropIfExists('ch_messages');
        Schema::dropIfExists('transfers');
    }
};
