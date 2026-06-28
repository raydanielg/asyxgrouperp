<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══ Fleet Vehicles ═══
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->unique();
            $table->string('registration_number')->unique();
            $table->string('make');
            $table->string('model');
            $table->integer('year')->nullable();
            $table->string('color')->nullable();
            $table->string('vehicle_type')->nullable(); // car, truck, van, motorcycle
            $table->string('fuel_type')->default('diesel');
            $table->decimal('fuel_capacity', 8, 2)->default(0);
            $table->decimal('odometer_reading', 12, 2)->default(0);
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 14, 2)->default(0);
            $table->date('insurance_expiry')->nullable();
            $table->date('registration_expiry')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('active'); // active, maintenance, retired
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->json('telematics_data')->nullable();
            $table->timestamps();
        });

        // ═══ Maintenance Records ═══
        Schema::create('vehicle_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('maintenance_type'); // service, repair, inspection
            $table->date('service_date');
            $table->decimal('odometer_at_service', 12, 2)->default(0);
            $table->string('service_provider')->nullable();
            $table->decimal('cost', 14, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('next_service_date')->nullable();
            $table->decimal('next_service_odometer', 12, 2)->nullable();
            $table->string('status')->default('completed');
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ═══ Fuel Logs ═══
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->date('fuel_date');
            $table->decimal('litres', 8, 2)->default(0);
            $table->decimal('cost_per_litre', 10, 2)->default(0);
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->decimal('odometer_reading', 12, 2)->default(0);
            $table->string('fuel_station')->nullable();
            $table->string('payment_method')->default('cash');
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
        Schema::dropIfExists('vehicle_maintenance');
        Schema::dropIfExists('vehicles');
    }
};
