<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══ Call Center Campaigns ═══
        Schema::create('call_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ Call Logs ═══
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('call_campaigns')->nullOnDelete();
            $table->string('call_direction')->default('outbound'); // inbound, outbound
            $table->string('caller_name')->nullable();
            $table->string('caller_phone');
            $table->string('callee_name')->nullable();
            $table->string('callee_phone')->nullable();
            $table->dateTime('call_start');
            $table->dateTime('call_end')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->string('status')->default('completed'); // completed, missed, failed, voicemail
            $table->string('disposition')->nullable(); // answered, no_answer, busy, callback
            $table->text('notes')->nullable();
            $table->string('call_recording_url')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_logs');
        Schema::dropIfExists('call_campaigns');
    }
};
