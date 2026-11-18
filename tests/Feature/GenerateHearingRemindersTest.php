<?php namespace Tests\Feature;

use App\Models\NotificationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GenerateHearingRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_reminders_without_duplicates(): void
    {
        config()->set('services.lawyer.whatsapp_number', '967777999999');

        config()->set('services.notifications.provider_enabled', false);

        $date = now()->addDay()->toDateString();

        $caseId = DB::table('legal_cases')->insertGetId([
            'title' => 'قضية تجريبية',
            'status' => 'active',
            'is_public' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $clientId = DB::table('clients')->insertGetId([
            'name' => 'محمد أحمد',
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

        $this->artisan('hearings:generate-reminders')->assertSuccessful();

        $this->assertDatabaseCount('notification_logs', 2);

        $clientNotification = NotificationLog::query()->where('type', 'client_reminder')->firstOrFail();

        $this->assertSame($sessionId, $clientNotification->session_id);
        $this->assertSame('pending', $clientNotification->status);
        $this->assertSame('967777123456', $clientNotification->recipient_number);

        $clientPayload = json_decode($clientNotification->message, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('client', $clientPayload['template']);
        $this->assertSame(['محمد أحمد', $date], $clientPayload['parameters']);

        $lawyerNotification = NotificationLog::query()->where('type', 'lawyer_reminder')->firstOrFail();

        $this->assertSame('pending', $lawyerNotification->status);
        $this->assertSame('967777999999', $lawyerNotification->recipient_number);

        $lawyerPayload = json_decode($lawyerNotification->message, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('lawyer', $lawyerPayload['template']);
        $this->assertSame(['قضية تجريبية', 'محمد أحمد', $date], $lawyerPayload['parameters']);

        // Running the command again must not create duplicates.
        $this->artisan('hearings:generate-reminders')->assertSuccessful();

        $this->assertDatabaseCount('notification_logs', 2);
    }

    public function test_it_ignores_cancelled_sessions(): void
    {
        $caseId = DB::table('legal_cases')->insertGetId([
            'title' => 'قضية ملغاة',
            'status' => 'active',
            'is_public' => false,
        ]);

        DB::table('case_sessions')->insert([
            'case_id' => $caseId,
            'scheduled_at' => now()->addDay()->toDateString(),
            'status' => 'cancelled',
        ]);

        $this->artisan('hearings:generate-reminders')->assertSuccessful();

        $this->assertDatabaseCount('notification_logs', 0);
    }
}