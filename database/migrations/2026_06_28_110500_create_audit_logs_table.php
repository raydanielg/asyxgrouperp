<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('action'); // create, update, delete, login, logout, view, export
            $table->string('module')->nullable();
            $table->string('module_action')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::statement('CREATE INDEX audit_logs_action_index ON audit_logs (action)');
        \Illuminate\Support\Facades\DB::statement('CREATE INDEX audit_logs_module_index ON audit_logs (module)');
        \Illuminate\Support\Facades\DB::statement('CREATE INDEX audit_logs_user_id_index ON audit_logs (user_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
