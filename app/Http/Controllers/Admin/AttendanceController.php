<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', today());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->latest('clock_in_at')->paginate(20);
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        // Stats
        $todayPresent = Attendance::today()->where('status', 'present')->count();
        $todayAbsent = Attendance::today()->where('status', 'absent')->count();
        $todayLate = Attendance::today()->where('status', 'late')->count();
        $todayRemote = Attendance::today()->where('status', 'remote')->count();
        $currentlyClockedIn = Attendance::today()->clockedIn()->count();
        $totalEmployees = Employee::where('status', 'active')->count();
        $notClockedIn = $totalEmployees - Attendance::today()->count();

        return view('admin.hrm.attendance.index', compact(
            'attendances', 'employees',
            'todayPresent', 'todayAbsent', 'todayLate', 'todayRemote',
            'currentlyClockedIn', 'totalEmployees', 'notClockedIn'
        ));
    }

    public function clockIn(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $existing = Attendance::today()->where('employee_id', $validated['employee_id'])->first();
        if ($existing && $existing->clock_in_at) {
            return back()->with('error', 'This employee has already clocked in today.');
        }

        $now = now();
        $isLate = $now->format('H:i') > '09:00';

        if ($existing) {
            $existing->update([
                'clock_in_at' => $now,
                'check_in' => $now->format('H:i'),
                'status' => $isLate ? 'late' : 'present',
                'clock_in_ip' => $request->ip(),
            ]);
        } else {
            Attendance::create([
                'employee_id' => $validated['employee_id'],
                'date' => today(),
                'clock_in_at' => $now,
                'check_in' => $now->format('H:i'),
                'status' => $isLate ? 'late' : 'present',
                'clock_in_ip' => $request->ip(),
                'created_by' => auth()->id(),
            ]);
        }

        $employee = Employee::find($validated['employee_id']);
        return back()->with('success', $employee->full_name . ' clocked in at ' . $now->format('H:i:s'));
    }

    public function clockOut(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $attendance = Attendance::today()->where('employee_id', $validated['employee_id'])->first();
        if (!$attendance) {
            return back()->with('error', 'No clock-in record found for today.');
        }
        if ($attendance->clock_out_at) {
            return back()->with('error', 'This employee has already clocked out today.');
        }

        $now = now();
        $workHours = 0;
        $overtimeHours = 0;

        if ($attendance->clock_in_at) {
            $workHours = $attendance->clock_in_at->diffInHours($now);
            if ($workHours > 8) {
                $overtimeHours = $workHours - 8;
            }
        }

        $attendance->update([
            'clock_out_at' => $now,
            'check_out' => $now->format('H:i'),
            'clock_out_ip' => $request->ip(),
            'work_hours' => round($workHours, 2),
            'overtime_hours' => round($overtimeHours, 2),
        ]);

        $employee = Employee::find($validated['employee_id']);
        return back()->with('success', $employee->full_name . ' clocked out at ' . $now->format('H:i:s') . ' (Worked: ' . sprintf('%02d:%02d', floor($workHours), ($workHours - floor($workHours)) * 60) . ')');
    }

    public function clockOutAll()
    {
        $clockedIn = Attendance::today()->clockedIn()->get();
        $count = 0;
        $now = now();

        foreach ($clockedIn as $attendance) {
            $workHours = 0;
            $overtimeHours = 0;

            if ($attendance->clock_in_at) {
                $workHours = $attendance->clock_in_at->diffInHours($now);
                if ($workHours > 8) {
                    $overtimeHours = $workHours - 8;
                }
            }

            $attendance->update([
                'clock_out_at' => $now,
                'check_out' => $now->format('H:i'),
                'clock_out_ip' => request()->ip(),
                'work_hours' => round($workHours, 2),
                'overtime_hours' => round($overtimeHours, 2),
            ]);
            $count++;
        }

        return back()->with('success', $count . ' employees clocked out at ' . $now->format('H:i:s'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        Attendance::create($data);
        return redirect()->route('admin.attendance.index')->with('success', 'Attendance recorded.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record deleted.');
    }
}
