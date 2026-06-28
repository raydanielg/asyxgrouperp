<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\FuelLog;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['assignedTo', 'company'])->latest()->paginate(20);
        return view('admin.fleet.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.fleet.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number',
            'registration_number' => 'required|string|unique:vehicles,registration_number',
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'nullable|integer',
            'color' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'fuel_capacity' => 'nullable|numeric',
            'odometer_reading' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric',
            'insurance_expiry' => 'nullable|date',
            'registration_expiry' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|string',
        ]);

        $validated['company_id'] = auth()->user()?->company_id;
        Vehicle::create($validated);

        return redirect()->route('admin.fleet.index')->with('success', 'Vehicle added.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['assignedTo', 'maintenanceRecords', 'fuelLogs']);
        $maintenance = $vehicle->maintenanceRecords()->latest()->paginate(10, ['*'], 'm_page');
        $fuelLogs = $vehicle->fuelLogs()->latest()->paginate(10, ['*'], 'f_page');
        return view('admin.fleet.show', compact('vehicle', 'maintenance', 'fuelLogs'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.fleet.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number,' . $vehicle->id,
            'registration_number' => 'required|string|unique:vehicles,registration_number,' . $vehicle->id,
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'nullable|integer',
            'color' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'fuel_capacity' => 'nullable|numeric',
            'odometer_reading' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric',
            'insurance_expiry' => 'nullable|date',
            'registration_expiry' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|string',
        ]);

        $vehicle->update($validated);
        return redirect()->route('admin.fleet.index')->with('success', 'Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.fleet.index')->with('success', 'Vehicle deleted.');
    }

    public function storeMaintenance(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|string',
            'service_date' => 'required|date',
            'odometer_at_service' => 'nullable|numeric',
            'service_provider' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'next_service_date' => 'nullable|date',
            'next_service_odometer' => 'nullable|numeric',
        ]);

        $validated['vehicle_id'] = $vehicle->id;
        $validated['company_id'] = auth()->user()?->company_id;
        $validated['created_by'] = auth()->id();
        VehicleMaintenance::create($validated);

        return back()->with('success', 'Maintenance record added.');
    }

    public function storeFuel(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'fuel_date' => 'required|date',
            'litres' => 'required|numeric|min:0',
            'cost_per_litre' => 'required|numeric|min:0',
            'odometer_reading' => 'nullable|numeric',
            'fuel_station' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        $validated['total_cost'] = $validated['litres'] * $validated['cost_per_litre'];
        $validated['vehicle_id'] = $vehicle->id;
        $validated['company_id'] = auth()->user()?->company_id;
        $validated['created_by'] = auth()->id();
        FuelLog::create($validated);

        if (!empty($validated['odometer_reading'])) {
            $vehicle->update(['odometer_reading' => $validated['odometer_reading']]);
        }

        return back()->with('success', 'Fuel log added.');
    }
}
