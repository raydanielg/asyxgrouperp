<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══ Fixed Assets ═══
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->unique();
            $table->string('asset_tag')->unique()->nullable();
            $table->string('name');
            $table->string('category')->nullable(); // it_equipment, furniture, vehicle, machinery, building
            $table->text('description')->nullable();
            $table->date('acquisition_date');
            $table->decimal('acquisition_cost', 14, 2)->default(0);
            $table->decimal('salvage_value', 14, 2)->default(0);
            $table->integer('useful_life_years')->default(5);
            $table->string('depreciation_method')->default('straight_line'); // straight_line, declining_balance
            $table->decimal('accumulated_depreciation', 14, 2)->default(0);
            $table->decimal('net_book_value', 14, 2)->default(0);
            $table->string('location')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('in_use'); // in_use, under_maintenance, disposed, written_off
            $table->date('disposal_date')->nullable();
            $table->decimal('disposal_value', 14, 2)->default(0);
            $table->text('disposal_notes')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ Depreciation Records ═══
        Schema::create('depreciation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained('fixed_assets')->cascadeOnDelete();
            $table->date('depreciation_date');
            $table->decimal('depreciation_amount', 14, 2)->default(0);
            $table->decimal('accumulated_depreciation', 14, 2)->default(0);
            $table->decimal('net_book_value', 14, 2)->default(0);
            $table->string('period'); // e.g. 2026-06
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciation_records');
        Schema::dropIfExists('fixed_assets');
    }
};
