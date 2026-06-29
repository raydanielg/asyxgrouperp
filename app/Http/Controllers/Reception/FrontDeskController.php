<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\FrontDesk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrontDeskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = FrontDesk::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('host', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->orderBy('check_in_at', 'asc')->paginate(15);

        return response()->json([
            'success' => true,
            'front_desks' => $items,
            'waitingCount' => FrontDesk::where('status', 'waiting')->count(),
            'inProgressCount' => FrontDesk::where('status', 'in_progress')->count(),
            'completedCount' => FrontDesk::where('status', 'completed')->count(),
            'totalCount' => FrontDesk::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'person_type' => 'required|in:visitor,appointment,delivery,staff,other',
            'purpose' => 'nullable|string|max:255',
            'host' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'status' => 'required|in:waiting,in_progress,completed',
            'check_in_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $data['created_by'] = auth()->id();
        if (empty($data['check_in_at'])) {
            $data['check_in_at'] = now();
        }

        $item = FrontDesk::create($data);

        return response()->json(['success' => true, 'message' => 'Front desk entry added.', 'front_desk' => $item]);
    }

    public function update(Request $request, FrontDesk $frontDesk)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'person_type' => 'required|in:visitor,appointment,delivery,staff,other',
            'purpose' => 'nullable|string|max:255',
            'host' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'status' => 'required|in:waiting,in_progress,completed',
            'check_in_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $frontDesk->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Front desk entry updated.', 'front_desk' => $frontDesk]);
    }

    public function destroy(FrontDesk $frontDesk)
    {
        $frontDesk->delete();
        return response()->json(['success' => true, 'message' => 'Front desk entry deleted.']);
    }

    public function markStatus(Request $request, FrontDesk $frontDesk)
    {
        $status = $request->validate(['status' => 'required|in:waiting,in_progress,completed'])['status'];
        $update = ['status' => $status];
        if ($status === 'completed') {
            $update['check_out_at'] = now();
        }
        $frontDesk->update($update);
        return response()->json(['success' => true, 'message' => 'Status updated.', 'front_desk' => $frontDesk]);
    }
}
