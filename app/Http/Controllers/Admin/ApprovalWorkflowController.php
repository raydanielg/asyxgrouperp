<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalStep;
use App\Models\ApprovalRequest;
use App\Models\ApprovalTrack;
use Illuminate\Http\Request;

class ApprovalWorkflowController extends Controller
{
    public function index()
    {
        $workflows = ApprovalWorkflow::with('steps')->orderBy('module')->get();
        $pendingRequests = ApprovalRequest::with(['workflow', 'requestedBy'])->pending()->latest()->limit(10)->get();
        return view('admin.approvals.index', compact('workflows', 'pendingRequests'));
    }

    public function create()
    {
        $modules = ['lpo' => 'LPO / Purchase Order', 'office_expense' => 'Office Expense', 'budget' => 'Project Budget', 'vendor_invoice' => 'Vendor Invoice', 'quotation' => 'Quotation', 'tender' => 'Tender'];
        return view('admin.approvals.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'module' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|string',
            'steps.*.approver_type' => 'required|string',
            'steps.*.approver_role' => 'nullable|string',
            'steps.*.approver_user_id' => 'nullable|exists:users,id',
            'steps.*.is_final' => 'boolean',
        ]);

        $workflow = ApprovalWorkflow::create([
            'name' => $validated['name'],
            'module' => $validated['module'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'company_id' => auth()->user()?->company_id,
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['steps'] as $index => $step) {
            ApprovalStep::create([
                'workflow_id' => $workflow->id,
                'level' => $index + 1,
                'name' => $step['name'],
                'approver_type' => $step['approver_type'],
                'approver_role' => $step['approver_role'] ?? null,
                'approver_user_id' => $step['approver_user_id'] ?? null,
                'is_final' => isset($step['is_final']),
                'order' => $index,
            ]);
        }

        return redirect()->route('admin.approvals.index')->with('success', 'Approval workflow created.');
    }

    public function show(ApprovalWorkflow $workflow)
    {
        $workflow->load('steps.approver');
        $requests = ApprovalRequest::where('workflow_id', $workflow->id)->with(['requestedBy', 'tracks.approver'])->latest()->paginate(15);
        return view('admin.approvals.show', compact('workflow', 'requests'));
    }

    public function destroy(ApprovalWorkflow $workflow)
    {
        $workflow->steps()->delete();
        $workflow->delete();
        return redirect()->route('admin.approvals.index')->with('success', 'Workflow deleted.');
    }

    public function requests()
    {
        $requests = ApprovalRequest::with(['workflow', 'requestedBy', 'tracks.approver'])->latest()->paginate(20);
        return view('admin.approvals.requests', compact('requests'));
    }

    public function approve(Request $request, ApprovalRequest $approvalRequest)
    {
        $validated = $request->validate(['comment' => 'nullable|string']);

        $track = ApprovalTrack::create([
            'approval_request_id' => $approvalRequest->id,
            'level' => $approvalRequest->current_level,
            'approver_id' => auth()->id(),
            'action' => 'approved',
            'comment' => $validated['comment'] ?? null,
            'acted_at' => now(),
        ]);

        $nextStep = $approvalRequest->workflow->steps()->where('level', '>', $approvalRequest->current_level)->first();

        if ($nextStep) {
            $approvalRequest->update(['current_level' => $nextStep->level]);
            ApprovalTrack::create([
                'approval_request_id' => $approvalRequest->id,
                'level' => $nextStep->level,
                'action' => 'pending',
            ]);
        } else {
            $approvalRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        }

        return back()->with('success', 'Request approved.');
    }

    public function reject(Request $request, ApprovalRequest $approvalRequest)
    {
        $validated = $request->validate(['rejection_reason' => 'required|string']);

        ApprovalTrack::create([
            'approval_request_id' => $approvalRequest->id,
            'level' => $approvalRequest->current_level,
            'approver_id' => auth()->id(),
            'action' => 'rejected',
            'comment' => $validated['rejection_reason'],
            'acted_at' => now(),
        ]);

        $approvalRequest->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Request rejected.');
    }
}
