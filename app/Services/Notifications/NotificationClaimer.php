<?php

namespace App\Services\Notifications;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\DB;

class NotificationClaimer
{
    public function claim(int $notificationId): bool
    {
        return DB::transaction(function () use ($notificationId) {
            $notification = NotificationLog::query()->whereKey($notificationId)->lockForUpdate()->first();

            if (!$notification || $notification->status !== 'pending' || $notification->attempts >= config('services.notifications.max_attempts')) {
                return false;
            }

            $notification->update([
                'status' => 'processing',
                'processing_started_at' => now(),
                'attempts' => $notification->attempts + 1,
            ]);

            return true;
        });
    }
}
