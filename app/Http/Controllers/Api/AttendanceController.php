<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee')->latest();

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function today(Request $request)
    {
        $records = Attendance::with('employee')
            ->whereDate('date', today())
            ->latest()
            ->get();

        return response()->json([
            'records' => $records,
            'summary' => [
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'late' => $records->where('status', 'late')->count(),
                'total_employees' => Employee::count(),
            ],
        ]);
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $existing = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('date', today())
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Already clocked in today'], 422);
        }

        $attendance = Attendance::create([
            'employee_id' => $request->employee_id,
            'date' => today(),
            'clock_in' => now()->format('H:i:s'),
            'status' => now()->format('H:i') > '09:00' ? 'late' : 'present',
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json($attendance->load('employee'), 201);
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('date', today())
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No clock-in record found'], 422);
        }

        $attendance->update(['clock_out' => now()->format('H:i:s')]);

        return response()->json($attendance->load('employee'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|string',
            'clock_out' => 'nullable|string',
            'status' => 'required|in:present,absent,late,half_day,leave',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $attendance = Attendance::create($data);

        return response()->json($attendance->load('employee'), 201);
    }

    public function destroy(Request $request, Attendance $attendance)
    {
        $attendance->delete();
        return response()->json(['message' => 'Record deleted']);
    }
}
