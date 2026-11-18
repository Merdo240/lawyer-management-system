<?php

namespace App\Services\Notifications;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\DB;

class NotificationRetryService
{
    public function retry(int $notificationId): bool
    {
        return DB::transaction(function () use ($notificationId) {
            $notification = NotificationLog::query()->whereKey($notificationId)->lockForUpdate()->first();

            if (!$notification) {
                return false;
            }

            if ($notification->status !== 'failed') {
                return false;
            }

            if ($notification->attempts >= config('services.notifications.max_attempts')) {
                return false;
            }

            if ($notification->failure_code !== 'provider_not_configured') {
                return false;
            }

            if (!config('services.notifications.provider_enabled')) {
                return false;
            }

            return false;
        });
    }
}
