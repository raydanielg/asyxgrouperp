<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with('employee')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $data['status'] = 'pending';
        $data['company_id'] = $request->user()->company_id;
        $leave = Leave::create($data);

        return response()->json($leave->load('employee'), 201);
    }

    public function approve(Request $request, Leave $leave)
    {
        $leave->update(['status' => 'approved', 'approved_by' => $request->user()->id]);
        return response()->json($leave->load('employee'));
    }

    public function reject(Request $request, Leave $leave)
    {
        $leave->update(['status' => 'rejected', 'approved_by' => $request->user()->id]);
        return response()->json($leave->load('employee'));
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return response()->json(['message' => 'Leave deleted']);
    }
}
