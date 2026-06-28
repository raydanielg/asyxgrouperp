<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->datetime('clock_in_at')->nullable()->after('check_in');
            $table->datetime('clock_out_at')->nullable()->after('check_out');
            $table->string('clock_in_ip')->nullable()->after('clock_out_at');
            $table->string('clock_out_ip')->nullable()->after('clock_in_ip');
            $table->decimal('work_hours', 5, 2)->default(0)->after('clock_out_ip');
            $table->decimal('overtime_hours', 5, 2)->default(0)->after('work_hours');
            $table->string('clock_in_location')->nullable()->after('overtime_hours');
            $table->string('clock_out_location')->nullable()->after('clock_in_location');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'clock_in_at', 'clock_out_at', 'clock_in_ip', 'clock_out_ip',
                'work_hours', 'overtime_hours', 'clock_in_location', 'clock_out_location',
            ]);
        });
    }
};
