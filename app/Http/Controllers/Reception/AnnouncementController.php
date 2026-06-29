<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Announcement::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $items = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'announcements' => $items,
            'activeCount' => Announcement::where('status', 'active')->count(),
            'highPriorityCount' => Announcement::where('priority', 'high')->where('status', 'active')->count(),
            'totalCount' => Announcement::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'audience' => 'required|in:all,staff,visitors',
            'priority' => 'required|in:low,normal,high',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $data['created_by'] = auth()->id();

        $item = Announcement::create($data);

        return response()->json(['success' => true, 'message' => 'Announcement created.', 'announcement' => $item]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'audience' => 'required|in:all,staff,visitors',
            'priority' => 'required|in:low,normal,high',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $announcement->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Announcement updated.', 'announcement' => $announcement]);
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return response()->json(['success' => true, 'message' => 'Announcement deleted.']);
    }

    public function toggleStatus(Announcement $announcement)
    {
        $newStatus = $announcement->status === 'active' ? 'inactive' : 'active';
        $announcement->update(['status' => $newStatus]);
        return response()->json(['success' => true, 'message' => 'Announcement ' . $newStatus . '.', 'announcement' => $announcement]);
    }
}
