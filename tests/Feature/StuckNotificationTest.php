<?php

namespace Tests\Feature;

use App\Models\NotificationLog;
use App\Services\Notifications\StuckNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StuckNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_stuck_notification_is_marked_as_unknown(): void
    {
        // إنشاء إشعار عالق منذ 20 دقيقة
        $notification = NotificationLog::create([
            'type' => 'test',
            'idempotency_key' => 'test_stuck_001',
            'recipient_type' => 'test',
            'recipient_number' => '000000000',
            'scheduled_at' => today(),
            'message' => 'Testing stuck notification',
            'status' => 'processing',
            'attempts' => 1,
            'processing_started_at' => now()->subMinutes(20),
        ]);

        // تشغيل خدمة اكتشاف الإشعارات العالقة
        $count = app(StuckNotificationService::class)->detect();

        // إعادة قراءة الإشعار من قاعدة البيانات
        $notification->refresh();

        // التأكد من النتائج
        $this->assertEquals(1, $count);
        $this->assertEquals('unknown', $notification->status);
        $this->assertEquals(1, $notification->attempts);
        $this->assertEquals('processing_timeout', $notification->failure_code);
        $this->assertNull($notification->processing_started_at);
    }
    public function test_recently_processing_notification_is_not_marked_as_unknown(): void
    {
        $notification = NotificationLog::create([
            'type' => 'test',
            'idempotency_key' => 'test_recent_001',
            'recipient_type' => 'test',
            'recipient_number' => '000000000',
            'scheduled_at' => today(),
            'message' => 'Testing recent notification',
            'status' => 'processing',
            'attempts' => 1,
            'processing_started_at' => now()->subMinutes(5),
        ]);

        $count = app(StuckNotificationService::class)->detect();

        $notification->refresh();

        $this->assertEquals(0, $count);
        $this->assertEquals('processing', $notification->status);
        $this->assertEquals(1, $notification->attempts);
        $this->assertNull($notification->failure_code);
    }

    public function test_stuck_timeout_uses_configured_minutes(): void
    {
        config([
            'services.notifications.stuck_timeout_minutes' => 30,
        ]);

        $notification = NotificationLog::create([
            'type' => 'test',
            'idempotency_key' => 'test_stuck_config_001',
            'recipient_type' => 'test',
            'recipient_number' => '000000000',
            'scheduled_at' => today(),
            'message' => 'Testing configured timeout',
            'status' => 'processing',
            'attempts' => 1,
            'processing_started_at' => now()->subMinutes(20),
        ]);

        $count = app(StuckNotificationService::class)->detect();

        $notification->refresh();

        $this->assertEquals(0, $count);
        $this->assertEquals('processing', $notification->status);
    }
}
