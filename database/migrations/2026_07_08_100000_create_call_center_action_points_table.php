<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_center_action_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('import_batch')->index();
            $table->string('source_filename')->nullable();
            $table->string('sheet_name')->nullable();
            $table->integer('row_number')->default(0);
            $table->text('activity')->nullable();
            $table->string('responsible_person')->nullable()->index();
            $table->date('due_date')->nullable()->index();
            $table->string('status')->nullable()->index();
            $table->text('notes')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->index(['import_batch', 'status']);
            $table->index(['responsible_person', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_center_action_points');
    }
};
