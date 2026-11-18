<?php namespace Tests\Feature;

use App\Models\NotificationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppEarlyWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_early_webhook_status_is_preserved(): void
    {
        config()->set('services.whatsapp.app_secret', 'test-secret');

        $notification = NotificationLog::factory()->create([
            'session_id' => null,
            'status' => 'processing',
            'provider_message_id' => null,
            'processing_started_at' => now(),
        ]);

        $payload = json_encode(
            [
                'entry' => [
                    [
                        'changes' => [
                            [
                                'value' => [
                                    'statuses' => [
                                        [
                                            'id' => 'wamid.early123',
                                            'status' => 'delivered',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            JSON_THROW_ON_ERROR,
        );

        $signature = 'sha256=' . hash_hmac('sha256', $payload, 'test-secret');

        $this->call(
            'POST',
            '/api/webhooks/whatsapp',
            [],
            [],
            [],
            [
                'HTTP_X_HUB_SIGNATURE_256' => $signature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload,
        )->assertOk();

        // An early delivery event must be retained for later matching.
        $this->assertDatabaseHas('whatsapp_status_events', [
            'provider_message_id' => 'wamid.early123',
            'status' => 'delivered',
        ]);
    }
}

