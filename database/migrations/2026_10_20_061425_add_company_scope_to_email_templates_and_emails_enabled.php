<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('created_by')->index();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('emails_enabled')->default(true)->after('suspension_reason');
        });
    }

    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('emails_enabled');
        });
    }
};
