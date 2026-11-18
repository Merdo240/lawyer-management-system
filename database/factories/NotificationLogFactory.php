<?php namespace Database\Factories;

use App\Models\NotificationLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationLog>
 */
class NotificationLogFactory extends Factory
{
    protected $model = NotificationLog::class;

    public function definition(): array
    {
        return [
            'session_id' => null,
            'type' => 'client_reminder',
            'idempotency_key' => fake()->uuid(),
            'recipient_type' => 'client',
            'recipient_number' => '967777123456',
            'scheduled_at' => now()->toDateString(),
            'sent_at' => null,
            'status' => 'pending',
            'provider_message_id' => null,
            'error_message' => null,
            'processing_started_at' => null,
            'message' => 'تذكير تجريبي بموعد الجلسة.',
            'attempts' => 0,
            'failure_code' => null,
        ];
    }
}
