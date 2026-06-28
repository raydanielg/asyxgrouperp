<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id', 'fuel_date', 'litres', 'cost_per_litre', 'total_cost',
        'odometer_reading', 'fuel_station', 'payment_method', 'company_id', 'created_by',
    ];

    protected $casts = ['fuel_date' => 'date'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
