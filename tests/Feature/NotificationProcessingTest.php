<?php

namespace Tests\Feature;

use App\Models\NotificationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_notification_is_not_processed_when_provider_is_disabled(): void
    {
        $notification = NotificationLog::create([
            'type' => 'test',
            'idempotency_key' => 'test_provider_disabled',
            'recipient_type' => 'test',
            'recipient_number' => '000000000',
            'scheduled_at' => today(),
            'message' => 'Test notification',
            'status' => 'pending',
            'attempts' => 0,
        ]);

        $this->artisan('app:process-pending-notifications')
            ->expectsOutput(
                'Notification provider is not ready. Processing skipped.'
            )
            ->assertExitCode(0);

        $notification->refresh();

        $this->assertEquals('pending', $notification->status);
        $this->assertEquals(0, $notification->attempts);
    }
}