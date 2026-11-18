<?php

namespace App\Services\Notifications;

use App\Models\NotificationLog;

class StuckNotificationService
{
    public function detect(): int
    {
        return NotificationLog::query()
            ->where('status', 'processing')
            ->where('processing_started_at', '<=', now()->subMinutes(config('services.notifications.stuck_timeout_minutes')))
            ->update([
                'status' => 'unknown',
                'processing_started_at' => null,
                'failure_code' => 'processing_timeout',
                'error_message' => 'Processing timed out. Delivery status is unknown.',
            ]);
    }
}
