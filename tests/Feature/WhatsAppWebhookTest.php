<?php namespace Tests\Feature;

use App\Models\NotificationLog;
use App\Services\Notifications\NotificationClaimer;
use App\Services\Notifications\NotificationProviderReadiness;
use App\Services\Notifications\NotificationSender;
use App\Services\Notifications\WhatsAppStatusReconciler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function sendWebhook(string $messageId, string $status): void
    {
        config()->set('services.whatsapp.app_secret', 'test-secret');

        $payload = json_encode(
            [
                'entry' => [
                    [
                        'changes' => [
                            [
                                'value' => [
                                    'statuses' => [
                                        [
                                            'id' => $messageId,
                                            'status' => $status,
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
    }

    public function test_early_webhook_is_saved_and_reconciled(): void
    {
        $this->sendWebhook('wamid.early123', 'delivered');

        $this->assertDatabaseHas('whatsapp_status_events', [
            'provider_message_id' => 'wamid.early123',
            'status' => 'delivered',
        ]);

        $notification = NotificationLog::factory()->create([
            'session_id' => null,
            'status' => 'accepted',
            'provider_message_id' => 'wamid.early123',
        ]);

        app(WhatsAppStatusReconciler::class)->reconcile('wamid.early123');

        $this->assertDatabaseHas('notification_logs', [
            'id' => $notification->id,
            'status' => 'delivered',
        ]);

        // A duplicate or older event cannot downgrade delivery.
        $this->sendWebhook('wamid.early123', 'sent');
        $this->sendWebhook('wamid.early123', 'delivered');

        $this->assertDatabaseHas('notification_logs', [
            'id' => $notification->id,
            'status' => 'delivered',
        ]);

        $this->assertSame(2, \Illuminate\Support\Facades\DB::table('whatsapp_status_events')->where('provider_message_id', 'wamid.early123')->count());
    }

    public function test_sender_reconciles_early_webhook_automatically(): void
    {
        config()->set('services.notifications.provider_enabled', true);
        config()->set('services.notifications.max_attempts', 3);
        config()->set('services.whatsapp.access_token', 'fake-token');
        config()->set('services.whatsapp.phone_number_id', '123456');
        config()->set('services.whatsapp.api_version', 'v25.0');
        config()->set('services.whatsapp.client_template', 'client_hearing_reminder');

        $readiness = Mockery::mock(NotificationProviderReadiness::class);

        $readiness->shouldReceive('isReady')->andReturn(true);

        $this->app->instance(NotificationProviderReadiness::class, $readiness);

        Http::preventStrayRequests();

        Http::fake([
            'graph.facebook.com/*' => Http::response(
                [
                    'messages' => [['id' => 'wamid.automatic123']],
                ],
                200,
            ),
        ]);

        $notification = NotificationLog::factory()->create([
            'session_id' => null,
            'type' => 'client_reminder',
            'recipient_type' => 'client',
            'recipient_number' => '967777123456',
            'status' => 'pending',
            'attempts' => 0,
            'scheduled_at' => today(),
            'message' => json_encode(
                [
                    'template' => 'client',
                    'parameters' => ['محمد أحمد', '2026-11-15'],
                ],
                JSON_UNESCAPED_UNICODE,
            ),
        ]);

        $this->sendWebhook('wamid.automatic123', 'read');

        $this->assertTrue(app(NotificationClaimer::class)->claim($notification->id));

        $this->assertTrue(app(NotificationSender::class)->send($notification->fresh()));

        $this->assertDatabaseHas('notification_logs', [
            'id' => $notification->id,
            'provider_message_id' => 'wamid.automatic123',
            'status' => 'read',
        ]);

        Http::assertSentCount(1);
    }
}
