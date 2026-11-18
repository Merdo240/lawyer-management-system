<?php namespace App\Services\Notifications;

use App\Interfaces\NotificationProviderInterface;

class FakeNotificationProvider implements NotificationProviderInterface
{
    public function send(string $recipient, string $message): NotificationSendResult
    {
        return new NotificationSendResult(status: 'accepted', providerMessageId: 'fake-' . uniqid());
    }
}
