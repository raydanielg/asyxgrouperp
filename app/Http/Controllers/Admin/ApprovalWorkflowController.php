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
        $modules = ['lpo' => 'LPO / Purchase Order', 'office_expense' => 'Office Expense', 'budget' => 'Project Budget', 'vendor_invoice' => 'Vendor Invoice', 'quotation' => 'Quotation', 'tender' => 'Tender', 'cash_topup' => 'Petty Cash / Project Cash Top-up'];
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

        return back()->with('success', 'Approval workflow created.');
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
        return back()->with('success', 'Workflow deleted.');
    }

    public function requests()
    {
        $requests = ApprovalRequest::with(['workflow', 'requestedBy', 'tracks.approver'])->latest()->paginate(20);
        return view('admin.approvals.requests', compact('requests'));
    }

    /**
     * Determine whether $user is the designated approver for the request's
     * current level. Returns null if allowed, or a reason string if not.
     */
    private function approverCheck(ApprovalRequest $req, \App\Models\User $user): ?string
    {
        $step = $req->workflow?->steps?->firstWhere('level', $req->current_level);
        if (!$step) {
            return 'No approval step is configured for the current level.';
        }

        $matchesRole = fn(string $role) => $user->role === $role || $user->hasRole($role);

        return match ($step->approver_type) {
            'user' => $user->id === $step->approver_user_id
                ? null
                : "This step requires the designated approver ({$step->approver?->name}).",
            'role', 'manager' => ($step->approver_role && $matchesRole($step->approver_role))
                ? null
                : "This step requires the '{$step->approver_role}' role.",
            default => 'Unknown approver type for this step.',
        };
    }

    private function guardApprovalAction(ApprovalRequest $req, \App\Models\User $user, string $action): void
    {
        if ($req->status !== 'pending') {
            abort(403, 'This request is no longer pending.');
        }

        // Segregation of duties — creator cannot decide their own request
        if ($req->requested_by === $user->id) {
            \App\Models\AuditLog::log('deny', 'approvals', [
                'module_action' => 'self_approval_attempt',
                'new_values' => ['request' => $req->request_number, 'module' => $req->module, 'attempted' => $action],
            ]);
            abort(403, 'You cannot ' . $action . ' a request you created.');
        }

        // Duplicate decision at the same level
        $alreadyActed = $req->tracks()
            ->where('level', $req->current_level)
            ->where('approver_id', $user->id)
            ->whereIn('action', ['approved', 'rejected'])
            ->exists();
        if ($alreadyActed) {
            abort(403, 'You have already decided on this level.');
        }

        if ($reason = $this->approverCheck($req, $user)) {
            \App\Models\AuditLog::log('deny', 'approvals', [
                'module_action' => 'unauthorized_approval_attempt',
                'new_values' => ['request' => $req->request_number, 'level' => $req->current_level, 'attempted' => $action, 'reason' => $reason],
            ]);
            abort(403, $reason);
        }
    }

    public function approve(Request $request, ApprovalRequest $approvalRequest)
    {
        $user = auth()->user();
        $this->guardApprovalAction($approvalRequest, $user, 'approve');
        $validated = $request->validate(['comment' => 'nullable|string']);

        \Illuminate\Support\Facades\DB::transaction(function () use ($approvalRequest, $validated, $user) {
            $previousLevel = $approvalRequest->current_level;

            ApprovalTrack::create([
                'approval_request_id' => $approvalRequest->id,
                'level' => $approvalRequest->current_level,
                'approver_id' => $user->id,
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
                $outcome = "advanced to level {$nextStep->level}";
            } else {
                $approvalRequest->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);
                $outcome = 'approved (final)';
            }

            \App\Models\AuditLog::log('approve', 'approvals', [
                'module_action' => 'approval_granted',
                'old_values' => ['status' => 'pending', 'level' => $previousLevel],
                'new_values' => ['request' => $approvalRequest->request_number, 'module' => $approvalRequest->module, 'outcome' => $outcome],
            ]);
        });

        return back()->with('success', 'Request approved.');
    }

    public function reject(Request $request, ApprovalRequest $approvalRequest)
    {
        $user = auth()->user();
        $this->guardApprovalAction($approvalRequest, $user, 'reject');
        $validated = $request->validate(['rejection_reason' => 'required|string']);

        \Illuminate\Support\Facades\DB::transaction(function () use ($approvalRequest, $validated, $user) {
            ApprovalTrack::create([
                'approval_request_id' => $approvalRequest->id,
                'level' => $approvalRequest->current_level,
                'approver_id' => $user->id,
                'action' => 'rejected',
                'comment' => $validated['rejection_reason'],
                'acted_at' => now(),
            ]);

            $approvalRequest->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            \App\Models\AuditLog::log('reject', 'approvals', [
                'module_action' => 'approval_rejected',
                'old_values' => ['status' => 'pending', 'level' => $approvalRequest->current_level],
                'new_values' => ['request' => $approvalRequest->request_number, 'module' => $approvalRequest->module, 'reason' => $validated['rejection_reason']],
            ]);
        });

        return back()->with('success', 'Request rejected.');
    }
}
