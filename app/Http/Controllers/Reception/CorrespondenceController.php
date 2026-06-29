<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Correspondence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CorrespondenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Correspondence::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $items = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'correspondence' => $items,
            'todayCount' => Correspondence::whereDate('received_date', today())->orWhereDate('dispatched_date', today())->count(),
            'weekCount' => Correspondence::whereBetween('received_date', [now()->startOfWeek(), now()->endOfWeek()])->orWhereBetween('dispatched_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'pendingCount' => Correspondence::where('status', 'pending')->count(),
            'totalCount' => Correspondence::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_number' => 'nullable|string|max:100',
            'type' => 'required|in:incoming,outgoing,internal',
            'sender_name' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:received,sent,pending,delivered',
            'received_date' => 'nullable|date',
            'dispatched_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $data['created_by'] = auth()->id();
        if (empty($data['reference_number'])) {
            $data['reference_number'] = 'COR-' . strtoupper(uniqid());
        }

        $item = Correspondence::create($data);

        return response()->json(['success' => true, 'message' => 'Correspondence recorded.', 'correspondence' => $item]);
    }

    public function update(Request $request, Correspondence $correspondence)
    {
        $validator = Validator::make($request->all(), [
            'reference_number' => 'nullable|string|max:100',
            'type' => 'required|in:incoming,outgoing,internal',
            'sender_name' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:received,sent,pending,delivered',
            'received_date' => 'nullable|date',
            'dispatched_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $correspondence->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Correspondence updated.', 'correspondence' => $correspondence]);
    }

    public function destroy(Correspondence $correspondence)
    {
        $correspondence->delete();
        return response()->json(['success' => true, 'message' => 'Correspondence deleted.']);
    }

    public function markStatus(Request $request, Correspondence $correspondence)
    {
        $status = $request->validate(['status' => 'required|in:received,sent,pending,delivered'])['status'];
        $correspondence->update(['status' => $status]);
        return response()->json(['success' => true, 'message' => 'Status updated.', 'correspondence' => $correspondence]);
    }
}
