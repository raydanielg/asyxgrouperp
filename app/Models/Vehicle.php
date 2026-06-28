<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_number', 'registration_number', 'make', 'model', 'year', 'color',
        'vehicle_type', 'fuel_type', 'fuel_capacity', 'odometer_reading',
        'purchase_date', 'purchase_price', 'insurance_expiry', 'registration_expiry',
        'assigned_to', 'status', 'company_id', 'telematics_data',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'insurance_expiry' => 'date',
        'registration_expiry' => 'date',
        'telematics_data' => 'array',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
