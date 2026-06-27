<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('password_reset_tokens', 'code')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->string('code', 6)->nullable()->after('token');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('password_reset_tokens', 'code')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }
};
