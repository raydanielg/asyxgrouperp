<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('manager_id')->nullable()->after('created_by')->constrained('employees')->nullOnDelete();
            $table->string('marital_status')->nullable()->after('nationality');
            $table->string('shift')->nullable()->after('employment_type');
            $table->string('work_location')->nullable()->after('shift');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['manager_id', 'marital_status', 'shift', 'work_location']);
        });
    }
};
