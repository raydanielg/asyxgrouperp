<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\FuelLog;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json($vehicle->load(['maintenanceRecords', 'fuelLogs']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'registration_number' => 'required|string|unique:vehicles',
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'nullable|integer',
            'color' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'status' => 'nullable|string',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $vehicle = Vehicle::create($data);

        return response()->json($vehicle, 201);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'registration_number' => 'sometimes|string',
            'make' => 'sometimes|string',
            'model' => 'sometimes|string',
            'year' => 'nullable|integer',
            'color' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'status' => 'nullable|string',
        ]);

        $vehicle->update($data);
        return response()->json($vehicle);
    }

    public function addMaintenance(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'date' => 'required|date',
            'next_service_date' => 'nullable|date',
        ]);

        $data['vehicle_id'] = $vehicle->id;
        $data['company_id'] = $request->user()->company_id;
        $record = VehicleMaintenance::create($data);

        return response()->json($record, 201);
    }

    public function addFuelLog(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'liters' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'odometer' => 'nullable|numeric',
            'date' => 'required|date',
            'station' => 'nullable|string',
        ]);

        $data['vehicle_id'] = $vehicle->id;
        $data['company_id'] = $request->user()->company_id;
        $log = FuelLog::create($data);

        return response()->json($log, 201);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return response()->json(['message' => 'Vehicle deleted']);
    }
}
