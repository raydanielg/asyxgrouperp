<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalaryAdvanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $status = $request->get('status');

        $query = SalaryAdvanceRequest::with('user', 'approver')
            ->where('user_id', $user->id)
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(15);

        return response()->json([
            'success' => true,
            'requests' => $requests,
            'pendingCount' => SalaryAdvanceRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approvedCount' => SalaryAdvanceRequest::where('user_id', $user->id)->where('status', 'approved')->count(),
            'totalRequested' => SalaryAdvanceRequest::where('user_id', $user->id)->sum('amount') ?? 0,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string|max:1000',
            'requested_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $user->id;
        $data['status'] = 'pending';

        $advance = SalaryAdvanceRequest::create($data);
        $advance->load('user');

        return response()->json(['success' => true, 'message' => 'Salary advance request submitted.', 'item' => $advance]);
    }

    public function destroy(SalaryAdvanceRequest $salaryAdvanceRequest)
    {
        $user = auth()->user();
        if ($salaryAdvanceRequest->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        $salaryAdvanceRequest->delete();
        return response()->json(['success' => true, 'message' => 'Request deleted.']);
    }

    public function markStatus(SalaryAdvanceRequest $salaryAdvanceRequest, Request $request)
    {
        $user = auth()->user();
        if ($salaryAdvanceRequest->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        $status = $request->validate(['status' => 'required|in:pending,approved,rejected'])['status'];
        $salaryAdvanceRequest->update([
            'status' => $status,
            'approved_by' => $status === 'approved' ? $user->id : null,
            'approved_at' => $status === 'approved' ? now() : null,
        ]);
        return response()->json(['success' => true, 'message' => 'Status updated.', 'item' => $salaryAdvanceRequest]);
    }
}
