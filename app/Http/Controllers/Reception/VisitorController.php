<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Visitor::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('badge_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $visitors = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'visitors' => $visitors,
            'todayCount' => Visitor::whereDate('check_in_at', today())->count(),
            'weekCount' => Visitor::whereBetween('check_in_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'pendingCount' => Visitor::whereNull('check_out_at')->where('status', 'checked_in')->count(),
            'totalCount' => Visitor::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'host' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'badge_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'check_in_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $data['status'] = 'checked_in';
        $data['created_by'] = auth()->id();
        if (empty($data['check_in_at'])) {
            $data['check_in_at'] = now();
        }

        $visitor = Visitor::create($data);

        return response()->json(['success' => true, 'message' => 'Visitor checked in successfully.', 'visitor' => $visitor]);
    }

    public function update(Request $request, Visitor $visitor)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'host' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'badge_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $visitor->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Visitor updated successfully.', 'visitor' => $visitor]);
    }

    public function destroy(Visitor $visitor)
    {
        $visitor->delete();
        return response()->json(['success' => true, 'message' => 'Visitor deleted successfully.']);
    }

    public function checkOut(Visitor $visitor)
    {
        $visitor->update(['status' => 'checked_out', 'check_out_at' => now()]);
        return response()->json(['success' => true, 'message' => 'Visitor checked out successfully.', 'visitor' => $visitor]);
    }
}
