<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallCampaign;
use App\Models\CallLog;
use Illuminate\Http\Request;

class CallCenterController extends Controller
{
    public function index()
    {
        $campaigns = CallCampaign::withCount('callLogs')->latest()->paginate(10, ['*'], 'c_page');
        $recentCalls = CallLog::with(['agent', 'campaign'])->latest()->limit(20)->get();
        $stats = [
            'total_calls' => CallLog::count(),
            'inbound' => CallLog::where('call_direction', 'inbound')->count(),
            'outbound' => CallLog::where('call_direction', 'outbound')->count(),
            'missed' => CallLog::where('status', 'missed')->count(),
            'avg_duration' => round(CallLog::avg('duration_seconds') ?? 0),
        ];
        return view('admin.call-center.index', compact('campaigns', 'recentCalls', 'stats'));
    }

    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $validated['company_id'] = auth()->user()?->company_id;
        $validated['created_by'] = auth()->id();
        CallCampaign::create($validated);

        return back()->with('success', 'Campaign created.');
    }

    public function storeCall(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'nullable|exists:call_campaigns,id',
            'call_direction' => 'required|string',
            'caller_name' => 'nullable|string',
            'caller_phone' => 'required|string',
            'callee_name' => 'nullable|string',
            'callee_phone' => 'nullable|string',
            'call_start' => 'required|date',
            'call_end' => 'nullable|date',
            'duration_seconds' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
            'disposition' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (!empty($validated['call_end']) && empty($validated['duration_seconds'])) {
            $validated['duration_seconds'] = strtotime($validated['call_end']) - strtotime($validated['call_start']);
        }

        $validated['agent_id'] = auth()->id();
        $validated['company_id'] = auth()->user()?->company_id;
        CallLog::create($validated);

        return back()->with('success', 'Call logged.');
    }

    public function calls()
    {
        $calls = CallLog::with(['agent', 'campaign'])->latest()->paginate(25);
        return view('admin.call-center.calls', compact('calls'));
    }
}
