<?php namespace App\Interfaces;

use App\Services\Notifications\NotificationSendResult;

interface NotificationProviderInterface
{
    public function send(string $recipient, string $message): NotificationSendResult;
}
