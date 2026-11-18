<?php namespace App\Services\Notifications;

final readonly class NotificationSendResult
{
    public function __construct(public string $status, public ?string $providerMessageId = null, public ?string $failureCode = null, public ?string $errorMessage = null)
    {
        if (!in_array($status, ['accepted', 'failed', 'unknown'], true)) {
            throw new \InvalidArgumentException('Invalid notification send status.');
        }

        if ($status === 'accepted' && empty($providerMessageId)) {
            throw new \InvalidArgumentException('Accepted notifications require a provider message ID.');
        }
    }
}
