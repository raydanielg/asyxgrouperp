<?php

namespace App\Services;

use App\Models\ApprovalRequest;
use App\Models\ApprovalTrack;
use App\Models\ApprovalWorkflow;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Central approval engine.
 *
 * submit()   — creates an ApprovalRequest for a business transaction when an
 *              active workflow exists for the module+company.
 * guard()    — validates that a user may decide on a request (pending state,
 *              self-approval, duplicate decision, designated approver).
 * approve()  — transactional approval, level advancement, audit.
 * reject()   — transactional rejection, audit.
 */
class ApprovalService
{
    /**
     * Find the active workflow for a module in the user's company context.
     */
    public static function findWorkflow(string $module, ?int $companyId): ?ApprovalWorkflow
    {
        return ApprovalWorkflow::withoutGlobalScopes()
            ->where('module', $module)
            ->where('is_active', true)
            ->where(function ($q) use ($companyId) {
                $q->whereNull('company_id')->orWhere('company_id', $companyId);
            })
            ->with('steps')
            ->first();
    }

    /**
     * Create a pending approval request linked to a business transaction.
     * Returns null when no workflow is configured (no approval required).
     */
    public static function submit(string $module, Model $model, string $label, ?float $amount, User $requester): ?ApprovalRequest
    {
        $workflow = self::findWorkflow($module, $model->company_id ?? $requester->company_id);
        if (!$workflow) {
            return null;
        }

        $request = DB::transaction(function () use ($module, $model, $label, $amount, $requester, $workflow) {
            $req = ApprovalRequest::create([
                'request_number' => 'APR-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(5)),
                'workflow_id' => $workflow->id,
                'module' => $module,
                'module_id' => $model->id,
                'module_label' => $label,
                'amount' => $amount,
                'status' => 'pending',
                'current_level' => $workflow->steps->min('level') ?? 1,
                'requested_by' => $requester->id,
                'company_id' => $model->company_id ?? $requester->company_id,
            ]);

            ApprovalTrack::create([
                'approval_request_id' => $req->id,
                'level' => $req->current_level,
                'action' => 'pending',
            ]);

            AuditLog::log('submit', 'approvals', [
                'module_action' => 'approval_submitted',
                'new_values' => [
                    'request' => $req->request_number, 'module' => $module,
                    'transaction' => class_basename($model) . '#' . $model->id,
                    'amount' => $amount,
                ],
            ]);

            return $req;
        });

        // Notify current-level approvers — post-commit, failures never abort
        if ($request) {
            $step = $workflow->steps->firstWhere('level', $request->current_level);
            if ($step) {
                UserNotifier::sendToStepApprovers(
                    $step, $request->company_id,
                    'approval_submitted',
                    "Approval required: {$label}",
                    "{$label} is waiting for your decision ({$request->request_number}).",
                    "/admin/approvals",
                    "apr-submit-{$request->id}"
                );
            }
        }

        return $request;
    }

    /**
     * Whether the user is the designated approver for the current level.
     * Returns null if allowed, or a human-readable reason if not.
     */
    public static function approverCheck(ApprovalRequest $req, User $user): ?string
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

    /**
     * Validate that $user may take $action on the request.
     * Aborts 403 and audit-logs violations.
     */
    public static function guard(ApprovalRequest $req, User $user, string $action): void
    {
        if ($req->status !== 'pending') {
            abort(403, 'This request is no longer pending.');
        }

        // Segregation of duties — creator cannot decide their own request
        if ($req->requested_by === $user->id) {
            AuditLog::log('deny', 'approvals', [
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

        if ($reason = self::approverCheck($req, $user)) {
            AuditLog::log('deny', 'approvals', [
                'module_action' => 'unauthorized_approval_attempt',
                'new_values' => ['request' => $req->request_number, 'level' => $req->current_level, 'attempted' => $action, 'reason' => $reason],
            ]);
            abort(403, $reason);
        }
    }

    /**
     * Approve the current level; mark the request approved when the final
     * level is reached. Returns the updated request.
     */
    public static function approve(ApprovalRequest $req, User $user, ?string $comment = null): ApprovalRequest
    {
        self::guard($req, $user, 'approve');

        $req = DB::transaction(function () use ($req, $user, $comment) {
            $previousLevel = $req->current_level;

            ApprovalTrack::create([
                'approval_request_id' => $req->id,
                'level' => $req->current_level,
                'approver_id' => $user->id,
                'action' => 'approved',
                'comment' => $comment,
                'acted_at' => now(),
            ]);

            $nextStep = $req->workflow->steps()->where('level', '>', $req->current_level)->first();

            if ($nextStep) {
                $req->update(['current_level' => $nextStep->level]);
                ApprovalTrack::create([
                    'approval_request_id' => $req->id,
                    'level' => $nextStep->level,
                    'action' => 'pending',
                ]);
                $outcome = "advanced to level {$nextStep->level}";
            } else {
                $req->update(['status' => 'approved', 'approved_at' => now()]);
                $outcome = 'approved (final)';
            }

            AuditLog::log('approve', 'approvals', [
                'module_action' => 'approval_granted',
                'old_values' => ['status' => 'pending', 'level' => $previousLevel],
                'new_values' => ['request' => $req->request_number, 'module' => $req->module, 'transaction' => $req->module_label, 'outcome' => $outcome],
            ]);

            $req = $req->fresh();
            if ($req->status === 'approved') {
                self::finalizeTransaction($req, $user);
            }

            return $req;
        });

        // Notify requester + next-level approvers — post-commit
        if ($req) {
            UserNotifier::send($req->requested_by, 'approval_step_decided',
                "Your request was approved: {$req->module_label}",
                "{$req->request_number} " . ($req->status === 'approved' ? 'is fully approved.' : "advanced to level {$req->current_level}."),
                "/admin/approvals",
                "apr-ok-{$req->id}-{$req->current_level}-{$req->requested_by}"
            );
            if ($req->status === 'pending') {
                $nextStep = $req->workflow->steps->firstWhere('level', $req->current_level);
                if ($nextStep) {
                    UserNotifier::sendToStepApprovers($nextStep, $req->company_id,
                        'approval_submitted',
                        "Approval required: {$req->module_label}",
                        "{$req->request_number} advanced to your level.",
                        "/admin/approvals",
                        "apr-level-{$req->id}-{$req->current_level}");
                }
            }
        }

        return $req;
    }

    /**
     * Sync the linked business transaction when a request is decided.
     */
    private static function finalizeTransaction(ApprovalRequest $req, User $user): void
    {
        match ($req->module) {
            'office_expense' => self::finalizeOfficeExpense($req, $user, true),
            'vendor_payment' => null, // payment stays 'pending' until explicit execution
            default => null,
        };
    }

    private static function finalizeOfficeExpense(ApprovalRequest $req, User $user, bool $approved): void
    {
        $expense = \App\Models\OfficeExpense::find($req->module_id);
        if (!$expense) {
            return;
        }
        $expense->update([
            'status' => $approved ? 'approved' : 'rejected',
            'approved_by' => $approved ? $user->id : null,
            'approved_at' => $approved ? now() : null,
            'approval_level' => $req->current_level,
        ]);
        if ($approved && $expense->project_id) {
            $expense->project?->increment('actual_cost', $expense->amount);
        }
    }

    /**
     * Invalidate prior approvals after a material change and resubmit.
     * - pending request → keeps pending (approver decides on new data)
     * - approved/rejected request → superseded + fresh pending request
     * Never revives executed transactions — caller must gate those.
     */
    public static function invalidateAndResubmit(string $module, Model $model, string $label, ?float $amount, User $actor): ?ApprovalRequest
    {
        $req = ApprovalRequest::where('module', $module)
            ->where('module_id', $model->id)->latest()->first();

        if (!$req) {
            return null;
        }

        if ($req->status === 'pending') {
            // Still awaiting decision — nothing to invalidate
            return $req;
        }

        return DB::transaction(function () use ($req, $module, $model, $label, $amount, $actor) {
            $req->update(['status' => 'superseded']);

            AuditLog::log('update', 'approvals', [
                'module_action' => 'approval_invalidated',
                'old_values' => ['status' => $req->status, 'request' => $req->request_number],
                'new_values' => ['module' => $module, 'transaction' => $label, 'reason' => 'Material change after decision'],
            ]);

            // Resubmit for fresh approval
            return self::submit($module, $model, $label, $amount, $actor);
        });
    }

    /**
     * Reject the request — terminal decision.
     */
    public static function reject(ApprovalRequest $req, User $user, string $reason): ApprovalRequest
    {
        self::guard($req, $user, 'reject');

        return DB::transaction(function () use ($req, $user, $reason) {
            ApprovalTrack::create([
                'approval_request_id' => $req->id,
                'level' => $req->current_level,
                'approver_id' => $user->id,
                'action' => 'rejected',
                'comment' => $reason,
                'acted_at' => now(),
            ]);

            $req->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            AuditLog::log('reject', 'approvals', [
                'module_action' => 'approval_rejected',
                'new_values' => ['request' => $req->request_number, 'module' => $req->module, 'transaction' => $req->module_label, 'reason' => $reason],
            ]);

            $req = $req->fresh();
            if ($req->module === 'office_expense') {
                self::finalizeOfficeExpense($req, $user, false);
            } elseif ($req->module === 'vendor_payment') {
                \App\Models\VendorPayment::where('id', $req->module_id)->update(['status' => 'rejected']);
            }

            return $req;
        });
    }
}
