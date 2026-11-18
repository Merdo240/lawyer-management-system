<?php namespace Tests\Feature;

use App\Models\NotificationLog;
use App\Services\Notifications\NotificationClaimer;
use App\Services\Notifications\NotificationProviderReadiness;
use App\Services\Notifications\NotificationSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class NotificationTemplateIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.notifications.provider_enabled', true);
        config()->set('services.notifications.max_attempts', 3);
        config()->set('services.whatsapp.access_token', 'fake-test-token');
        config()->set('services.whatsapp.phone_number_id', '123456');
        config()->set('services.whatsapp.api_version', 'v25.0');
        config()->set('services.whatsapp.template_language', 'ar');
        config()->set('services.whatsapp.client_template', 'client_hearing_reminder');

        // Simulate readiness; never contact the real provider.
        $readiness = Mockery::mock(NotificationProviderReadiness::class);

        $readiness->shouldReceive('isReady')->andReturn(true);

        $this->app->instance(NotificationProviderReadiness::class, $readiness);

        Http::preventStrayRequests();
    }

    private function createNotification(): NotificationLog
    {
        return NotificationLog::factory()->create([
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
                    'parameters' => ['محمد أحمد', '15/11/2026'],
                ],
                JSON_UNESCAPED_UNICODE,
            ),
        ]);
    }

    public function test_accepted_template_is_saved_correctly(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(
                [
                    'messages' => [['id' => 'wamid.accepted123']],
                ],
                200,
            ),
        ]);

        $notification = $this->createNotification();

        $this->assertTrue(app(NotificationClaimer::class)->claim($notification->id));

        $sent = app(NotificationSender::class)->send($notification->fresh());

        $this->assertTrue($sent);

        $this->assertDatabaseHas('notification_logs', [
            'id' => $notification->id,
            'status' => 'accepted',
            'provider_message_id' => 'wamid.accepted123',
            'attempts' => 1,
        ]);

        Http::assertSentCount(1);

        Http::assertSent(function ($request) {
            return $request['type'] === 'template' && $request['template']['name'] === 'client_hearing_reminder';
        });

        // Accepted messages must not be claimed again.
        $this->assertFalse(app(NotificationClaimer::class)->claim($notification->id));

        Http::assertSentCount(1);
    }

    public function test_rejected_template_is_marked_failed(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(
                [
                    'error' => [
                        'code' => 132001,
                        'message' => 'Template not found',
                    ],
                ],
                400,
            ),
        ]);

        $notification = $this->createNotification();

        $this->assertTrue(app(NotificationClaimer::class)->claim($notification->id));

        $sent = app(NotificationSender::class)->send($notification->fresh());

        $this->assertFalse($sent);

        $this->assertDatabaseHas('notification_logs', [
            'id' => $notification->id,
            'status' => 'failed',
            'failure_code' => 'meta_api_rejected',
        ]);

        $this->assertFalse(app(NotificationClaimer::class)->claim($notification->id));
    }

    public function test_server_error_is_marked_unknown(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response([], 500),
        ]);

        $notification = $this->createNotification();

        $this->assertTrue(app(NotificationClaimer::class)->claim($notification->id));

        $sent = app(NotificationSender::class)->send($notification->fresh());

        $this->assertFalse($sent);

        $this->assertDatabaseHas('notification_logs', [
            'id' => $notification->id,
            'status' => 'unknown',
            'failure_code' => 'meta_server_error',
        ]);

        // Unknown results must not be automatically retried.
        $this->assertFalse(app(NotificationClaimer::class)->claim($notification->id));

        Http::assertSentCount(1);
    }

    public function test_invalid_template_payload_is_rejected(): void
    {
        Http::fake();

        $notification = $this->createNotification();

        $notification->update([
            'message' => 'invalid-json',
        ]);

        $this->assertTrue(app(NotificationClaimer::class)->claim($notification->id));

        $sent = app(NotificationSender::class)->send($notification->fresh());

        $this->assertFalse($sent);

        $this->assertDatabaseHas('notification_logs', [
            'id' => $notification->id,
            'status' => 'failed',
            'failure_code' => 'invalid_payload',
        ]);

        Http::assertNothingSent();
    }
}
