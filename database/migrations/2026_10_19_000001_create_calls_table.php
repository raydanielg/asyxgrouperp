<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('calls')) {
            return;
        }

        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('caller_name')->nullable();
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('call_type')->default('incoming'); // incoming, outgoing
            $table->string('subject')->nullable();
            $table->string('status')->default('answered'); // answered, missed, voicemail, follow_up
            $table->unsignedInteger('duration')->default(0); // seconds
            $table->text('notes')->nullable();
            $table->dateTime('call_time')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index('call_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
