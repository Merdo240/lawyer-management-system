<?php namespace App\Services\Notifications;

use App\Interfaces\NotificationProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationProvider implements NotificationProviderInterface
{
    public function send(string $recipient, string $message): NotificationSendResult
    {
        $token = config('services.whatsapp.access_token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $version = config('services.whatsapp.api_version');

        if (!config('services.notifications.provider_enabled', false)) {
            return new NotificationSendResult(status: 'failed', failureCode: 'provider_disabled', errorMessage: 'WhatsApp sending is disabled.');
        }

        if (!is_string($token) || trim($token) === '' || !is_string($phoneNumberId) || trim($phoneNumberId) === '' || !is_string($version) || !preg_match('/^v\d+\.\d+$/', $version)) {
            return new NotificationSendResult(status: 'failed', failureCode: 'provider_not_configured', errorMessage: 'WhatsApp API configuration is incomplete.');
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(15)
                ->post("https://graph.facebook.com/{$version}/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $recipient,
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            if ($response->failed()) {
                $httpStatus = $response->status();
                $errorCode = $response->json('error.code');

                Log::warning('WhatsApp API request failed.', [
                    'http_status' => $httpStatus,
                    'error_code' => $errorCode,
                ]);

                // A server-side failure may have an uncertain outcome.
                if ($httpStatus >= 500) {
                    return new NotificationSendResult(status: 'unknown', failureCode: 'meta_server_error', errorMessage: 'Meta returned a server error. Delivery status is unknown.');
                }

                return new NotificationSendResult(status: 'failed', failureCode: 'meta_api_rejected', errorMessage: 'Meta rejected the WhatsApp message request.');
            }

            $messageId = $response->json('messages.0.id');

            if (!is_string($messageId) || $messageId === '') {
                return new NotificationSendResult(status: 'unknown', failureCode: 'missing_message_id', errorMessage: 'Meta response did not contain a message ID.');
            }

            return new NotificationSendResult(status: 'accepted', providerMessageId: $messageId);
        } catch (\Throwable $exception) {
            Log::error('WhatsApp API request outcome is unknown.', [
                'exception_type' => get_class($exception),
            ]);

            return new NotificationSendResult(status: 'unknown', failureCode: 'request_outcome_unknown', errorMessage: 'Could not confirm whether Meta accepted the request.');
        }
    }
}
