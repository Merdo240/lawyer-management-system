<?php namespace Tests\Feature;

use App\Models\NotificationLog;
use App\Services\Notifications\HearingReminderValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HearingReminderValidatorTest extends TestCase
{
    use RefreshDatabase;

    private function createScenario(): array
    {
        $date = today()->addDay()->toDateString();

        $caseId = DB::table('legal_cases')->insertGetId([
            'title' => 'قضية تجريبية',
            'status' => 'active',
            'is_public' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $clientId = DB::table('clients')->insertGetId([
            'name' => 'أحمد محمد',
            'whatsapp_number' => '967777123456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('case_client')->insert([
            'case_id' => $caseId,
            'client_id' => $clientId,
        ]);

        $sessionId = DB::table('case_sessions')->insertGetId([
            'case_id' => $caseId,
            'scheduled_at' => $date,
            'status' => 'scheduled',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $notification = NotificationLog::factory()->create([
            'session_id' => $sessionId,
            'type' => 'client_reminder',
            'idempotency_key' => "client:{$sessionId}:{$clientId}:{$date}",
            'recipient_type' => 'client',
            'recipient_number' => '967777123456',
            'scheduled_at' => today(),
            'status' => 'processing',
            'attempts' => 1,
            'processing_started_at' => now(),
            'message' => json_encode(
                [
                    'template' => 'client',
                    'parameters' => ['أحمد محمد', $date],
                ],
                JSON_UNESCAPED_UNICODE,
            ),
        ]);

        return compact('caseId', 'clientId', 'sessionId', 'notification', 'date');
    }

    public function test_cancelled_session_invalidates_reminder(): void
    {
        $data = $this->createScenario();

        DB::table('case_sessions')
            ->where('id', $data['sessionId'])
            ->update(['status' => 'cancelled']);

        $this->assertFalse(app(HearingReminderValidator::class)->validate($data['notification']->fresh()));

        $this->assertDatabaseHas('notification_logs', [
            'id' => $data['notification']->id,
            'status' => 'failed',
            'failure_code' => 'hearing_cancelled',
        ]);
    }

    public function test_changed_date_invalidates_old_reminder(): void
    {
        $data = $this->createScenario();

        DB::table('case_sessions')
            ->where('id', $data['sessionId'])
            ->update([
                'scheduled_at' => today()->addDays(5)->toDateString(),
            ]);

        $this->assertFalse(app(HearingReminderValidator::class)->validate($data['notification']->fresh()));

        $this->assertDatabaseHas('notification_logs', [
            'id' => $data['notification']->id,
            'failure_code' => 'hearing_date_changed',
        ]);
    }

    public function test_removed_client_invalidates_reminder(): void
    {
        $data = $this->createScenario();

        DB::table('case_client')->where('case_id', $data['caseId'])->where('client_id', $data['clientId'])->delete();

        $this->assertFalse(app(HearingReminderValidator::class)->validate($data['notification']->fresh()));

        $this->assertDatabaseHas('notification_logs', [
            'id' => $data['notification']->id,
            'failure_code' => 'client_removed',
        ]);
    }

    public function test_changed_client_details_are_refreshed(): void
    {
        $data = $this->createScenario();

        DB::table('clients')
            ->where('id', $data['clientId'])
            ->update([
                'name' => 'أحمد الجديد',
                'whatsapp_number' => '967777999999',
            ]);

        $this->assertTrue(app(HearingReminderValidator::class)->validate($data['notification']->fresh()));

        $updated = $data['notification']->fresh();
        $payload = json_decode($updated->message, true);

        $this->assertSame('967777999999', $updated->recipient_number);

        $this->assertSame('أحمد الجديد', $payload['parameters'][0]);
    }
}
