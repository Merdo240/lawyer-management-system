<?php namespace Tests\Feature;

use App\Models\NotificationLog;
use App\Services\Notifications\NotificationProviderReadiness;
use App\Services\Notifications\NotificationSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_does_not_send_when_readiness_check_fails(): void
    {
        config()->set('services.notifications.provider_enabled', true);

        config()->set('services.whatsapp.access_token', 'fake-test-token');
        config()->set('services.whatsapp.phone_number_id', '123456789');
        config()->set('services.whatsapp.api_version', 'v25.0');

        $notification = NotificationLog::factory()->create([
            'status' => 'processing',
            'processing_started_at' => now(),
            'attempts' => 1,
        ]);

        $sender = app(NotificationSender::class);
        $result = $sender->send($notification);

        $notification->refresh();

        $this->assertFalse($result);

        $this->assertSame('processing', $notification->status);

        $this->assertNull($notification->provider_message_id);
    }

    public function test_it_does_not_send_pending_notification(): void
    {
        $notification = NotificationLog::factory()->create([
            'status' => 'pending',
            'processing_started_at' => null,
        ]);

        $sender = app(NotificationSender::class);
        $result = $sender->send($notification);

        $this->assertFalse($result);
        $this->assertSame('pending', $notification->fresh()->status);
        $this->assertNull($notification->fresh()->provider_message_id);
    }
}
