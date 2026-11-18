<?php namespace App\Services\Notifications;

class NotificationProviderReadiness
{
    public function isReady(): bool
    {
        if (!config('services.notifications.provider_enabled', false)) {
            return false;
        }

        // Never allow the real provider in automated tests.
        if (app()->environment('testing')) {
            return false;
        }

        $token = config('services.whatsapp.access_token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $apiVersion = config('services.whatsapp.api_version');

        return is_string($token) && trim($token) !== '' && is_string($phoneNumberId) && trim($phoneNumberId) !== '' && is_string($apiVersion) && preg_match('/^v\d+\.\d+$/', $apiVersion) === 1;
    }
}
