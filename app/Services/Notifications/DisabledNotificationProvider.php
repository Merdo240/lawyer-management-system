<?php namespace App\Services\Notifications;

use App\Interfaces\NotificationProviderInterface;

class DisabledNotificationProvider implements NotificationProviderInterface
{
    public function send(string $recipient, string $message): NotificationSendResult
    {
        return new NotificationSendResult(status: 'failed', failureCode: 'provider_not_configured', errorMessage: 'Notification provider is disabled or not configured.');
    }
}
