<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('correspondence', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable();
            $table->string('type')->default('incoming');
            $table->string('sender_name')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('received');
            $table->timestamp('received_date')->nullable();
            $table->timestamp('dispatched_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correspondence');
    }
};
