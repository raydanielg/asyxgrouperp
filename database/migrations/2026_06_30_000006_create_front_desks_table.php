<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('front_desks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('person_type')->default('visitor');
            $table->string('purpose')->nullable();
            $table->string('host')->nullable();
            $table->string('department')->nullable();
            $table->string('status')->default('waiting');
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('front_desks');
    }
};
