<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Appointment::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('visitor_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('host', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')->paginate(15);

        return response()->json([
            'success' => true,
            'appointments' => $appointments,
            'todayCount' => Appointment::whereDate('appointment_date', today())->count(),
            'weekCount' => Appointment::whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'pendingCount' => Appointment::where('status', 'scheduled')->where('appointment_date', '>=', now())->count(),
            'totalCount' => Appointment::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visitor_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'host' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'appointment_date' => 'required|date',
            'duration' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $data['status'] = 'scheduled';
        $data['created_by'] = auth()->id();

        $appointment = Appointment::create($data);

        return response()->json(['success' => true, 'message' => 'Appointment scheduled successfully.', 'appointment' => $appointment]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validator = Validator::make($request->all(), [
            'visitor_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'host' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'appointment_date' => 'required|date',
            'duration' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $appointment->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Appointment updated successfully.', 'appointment' => $appointment]);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return response()->json(['success' => true, 'message' => 'Appointment deleted successfully.']);
    }

    public function complete(Appointment $appointment)
    {
        $appointment->update(['status' => 'completed']);
        return response()->json(['success' => true, 'message' => 'Appointment marked as completed.', 'appointment' => $appointment]);
    }

    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        return response()->json(['success' => true, 'message' => 'Appointment cancelled.', 'appointment' => $appointment]);
    }
}
