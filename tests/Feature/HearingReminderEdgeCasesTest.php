<?php namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HearingReminderEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    private function createCase(string $status = 'active'): int
    {
        return DB::table('legal_cases')->insertGetId([
            'title' => 'قضية مطالبة مالية',
            'status' => $status,
            'is_public' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createClient(string $name, string $number): int
    {
        return DB::table('clients')->insertGetId([
            'name' => $name,
            'whatsapp_number' => $number,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSession(int $caseId): int
    {
        return DB::table('case_sessions')->insertGetId([
            'case_id' => $caseId,
            'scheduled_at' => now()->addDay()->toDateString(),
            'status' => 'scheduled',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_multiple_clients_receive_separate_reminders(): void
    {
        config()->set('services.lawyer.whatsapp_number', '967777999999');

        config()->set('services.notifications.provider_enabled', false);

        $caseId = $this->createCase();

        $firstClient = $this->createClient('محمد أحمد', '967777111111');

        $secondClient = $this->createClient('أحمد علي', '967777222222');

        DB::table('case_client')->insert([
            [
                'case_id' => $caseId,
                'client_id' => $firstClient,
            ],
            [
                'case_id' => $caseId,
                'client_id' => $secondClient,
            ],
        ]);

        $this->createSession($caseId);

        $this->artisan('hearings:generate-reminders')->assertSuccessful();

        $this->assertDatabaseCount('notification_logs', 3);

        $this->assertDatabaseHas('notification_logs', [
            'type' => 'client_reminder',
            'recipient_number' => '967777111111',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('notification_logs', [
            'type' => 'client_reminder',
            'recipient_number' => '967777222222',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('notification_logs', [
            'type' => 'lawyer_reminder',
            'recipient_number' => '967777999999',
            'status' => 'pending',
        ]);

        $this->artisan('hearings:generate-reminders')->assertSuccessful();

        $this->assertDatabaseCount('notification_logs', 3);
    }

    public function test_inactive_case_does_not_generate_reminders(): void
    {
        config()->set('services.lawyer.whatsapp_number', '967777999999');

        $caseId = $this->createCase('closed');

        $clientId = $this->createClient('محمد أحمد', '967777111111');

        DB::table('case_client')->insert([
            'case_id' => $caseId,
            'client_id' => $clientId,
        ]);

        $this->createSession($caseId);

        $this->artisan('hearings:generate-reminders')->assertSuccessful();

        $this->assertDatabaseCount('notification_logs', 0);
    }
}
