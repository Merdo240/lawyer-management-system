<?php namespace App\Services\Notifications;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppTemplateSender
{
    public function send(string $recipient, string $templateName, array $parameters): NotificationSendResult
    {
        if (!config('services.notifications.provider_enabled', false)) {
            return new NotificationSendResult(status: 'failed', failureCode: 'provider_disabled', errorMessage: 'WhatsApp sending is disabled.');
        }

        $token = config('services.whatsapp.access_token');
        $phoneId = config('services.whatsapp.phone_number_id');
        $version = config('services.whatsapp.api_version');
        $language = config('services.whatsapp.template_language', 'ar');

        if (!is_string($token) || trim($token) === '' || !is_string($phoneId) || trim($phoneId) === '' || !is_string($version) || !preg_match('/^v\d+\.\d+$/', $version)) {
            return new NotificationSendResult(status: 'failed', failureCode: 'provider_not_configured', errorMessage: 'WhatsApp API configuration is incomplete.');
        }

        $bodyParameters = array_map(
            fn($value) => [
                'type' => 'text',
                'text' => (string) $value,
            ],
            array_values($parameters),
        );

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(15)
                ->post("https://graph.facebook.com/{$version}/{$phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $recipient,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => $language,
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => $bodyParameters,
                            ],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                $httpStatus = $response->status();

                Log::warning('WhatsApp template request failed.', [
                    'http_status' => $httpStatus,
                    'error_code' => $response->json('error.code'),
                ]);

                return new NotificationSendResult(status: $httpStatus >= 500 ? 'unknown' : 'failed', failureCode: $httpStatus >= 500 ? 'meta_server_error' : 'meta_api_rejected', errorMessage: 'WhatsApp template request failed.');
            }

            $messageId = $response->json('messages.0.id');

            if (!is_string($messageId) || $messageId === '') {
                return new NotificationSendResult(status: 'unknown', failureCode: 'missing_message_id', errorMessage: 'Meta did not return a message ID.');
            }

            return new NotificationSendResult(status: 'accepted', providerMessageId: $messageId);
        } catch (\Throwable $exception) {
            Log::error('WhatsApp template request outcome unknown.', [
                'exception_type' => get_class($exception),
            ]);

            return new NotificationSendResult(status: 'unknown', failureCode: 'request_outcome_unknown', errorMessage: 'Could not confirm template delivery request.');
        }
    }
}
