<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Call;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CallController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Call::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('caller_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('call_type', $request->type);
        }

        $calls = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'calls' => $calls,
            'todayCount' => Call::whereDate('call_time', today())->count(),
            'weekCount' => Call::whereBetween('call_time', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'pendingCount' => Call::where('status', 'follow_up')->count(),
            'totalCount' => Call::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caller_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'call_type' => 'required|in:incoming,outgoing',
            'subject' => 'nullable|string|max:255',
            'status' => 'required|in:answered,missed,voicemail,follow_up',
            'duration' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'call_time' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $data['created_by'] = auth()->id();
        if (empty($data['call_time'])) {
            $data['call_time'] = now();
        }

        $call = Call::create($data);

        return response()->json(['success' => true, 'message' => 'Call log saved.', 'call' => $call]);
    }

    public function update(Request $request, Call $call)
    {
        $validator = Validator::make($request->all(), [
            'caller_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'call_type' => 'required|in:incoming,outgoing',
            'subject' => 'nullable|string|max:255',
            'status' => 'required|in:answered,missed,voicemail,follow_up',
            'duration' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'call_time' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $call->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Call log updated.', 'call' => $call]);
    }

    public function destroy(Call $call)
    {
        $call->delete();
        return response()->json(['success' => true, 'message' => 'Call log deleted.']);
    }

    public function markStatus(Request $request, Call $call)
    {
        $status = $request->validate(['status' => 'required|in:answered,missed,voicemail,follow_up'])['status'];
        $call->update(['status' => $status]);
        return response()->json(['success' => true, 'message' => 'Call status updated.', 'call' => $call]);
    }
}
