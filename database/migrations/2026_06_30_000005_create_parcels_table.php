<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('courier')->nullable();
            $table->string('status')->default('received');
            $table->timestamp('received_date')->nullable();
            $table->timestamp('delivered_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
