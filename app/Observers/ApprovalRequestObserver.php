<?php

namespace App\Observers;

use App\Models\ApprovalRequest;
use App\Models\CashTopupRequest;
use App\Services\LedgerService;
use Illuminate\Support\Facades\Log;

class ApprovalRequestObserver
{
    /**
     * When a generic approval request tied to the 'cash_topup' module changes status,
     * credit (or release) the target petty cash / project account card automatically.
     */
    public function updated(ApprovalRequest $approvalRequest): void
    {
        if ($approvalRequest->module !== 'cash_topup') {
            return;
        }

        if (!$approvalRequest->wasChanged('status')) {
            return;
        }

        $topup = CashTopupRequest::with('cashAccount')->find($approvalRequest->module_id);
        if (!$topup) {
            return;
        }

        if ($approvalRequest->status === 'approved' && $topup->status !== 'disbursed') {
            try {
                $ledger = app(LedgerService::class);
                $transaction = $ledger->fundCashAccount(
                    $topup->cashAccount,
                    (float) $topup->amount,
                    'Cash top-up approved: ' . ($topup->purpose ?: ('Request #' . $topup->id)),
                    null,
                    'cash_topup',
                    $topup->id,
                    $approvalRequest->requested_by
                );

                $topup->update([
                    'status' => 'disbursed',
                    'cash_account_transaction_id' => $transaction->id,
                    'disbursed_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to disburse cash top-up #' . $topup->id . ': ' . $e->getMessage());
            }
        } elseif ($approvalRequest->status === 'rejected' && $topup->status !== 'rejected') {
            $topup->update(['status' => 'rejected']);
        }
    }
}
