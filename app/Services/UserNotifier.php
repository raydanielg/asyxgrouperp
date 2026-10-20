<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;

/**
 * Internal notification inbox — the safe fallback delivery channel.
 * Failures are logged and never propagate into business transactions.
 * External delivery (email/SMS) is documented as a future integration.
 */
class UserNotifier
{
    /**
     * Notify a single user. $dedupeKey prevents duplicates on retries.
     */
    public static function send(int $userId, string $type, string $title, ?string $body = null, ?string $link = null, ?string $dedupeKey = null): void
    {
        try {
            if ($dedupeKey && UserNotification::where('dedupe_key', $dedupeKey)->exists()) {
                return;
            }
            UserNotification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'link' => $link,
                'dedupe_key' => $dedupeKey,
            ]);
        } catch (\Throwable $e) {
            Log::warning("UserNotifier failed for user {$userId}: {$e->getMessage()}");
        }
    }

    /**
     * Notify every user matching the approver designation of a workflow step.
     */
    public static function sendToStepApprovers(\App\Models\ApprovalStep $step, int $companyId, string $type, string $title, ?string $body, ?string $link, ?string $dedupeKey = null): void
    {
        $query = User::withoutGlobalScopes()->where('is_enable_login', true)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhere('role', 'superadmin');
            });

        $ids = match ($step->approver_type) {
            'user' => [$step->approver_user_id],
            'role', 'manager' => $step->approver_role
                ? $query->where('role', $step->approver_role)->pluck('id')->all()
                : [],
            default => [],
        };

        foreach (array_filter($ids) as $id) {
            self::send($id, $type, $title, $body, $link, $dedupeKey ? "{$dedupeKey}:{$id}" : null);
        }
    }
}
