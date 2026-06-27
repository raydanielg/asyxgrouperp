<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('from')->nullable();
                $table->string('module_name')->nullable();
                $table->string('subject')->nullable();
                $table->longText('content')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->index();
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('module')->nullable();
                $table->string('type', 188)->nullable();
                $table->string('action')->nullable();
                $table->string('status')->nullable();
                $table->string('permissions')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('email_templates');
    }
};
