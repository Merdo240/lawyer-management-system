<?php namespace App\Console\Commands;

use App\Models\NotificationLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateHearingReminders extends Command
{
    protected $signature = 'hearings:generate-reminders
                            {--date= : Hearing date (YYYY-MM-DD)}';

    protected $description = 'Generate reminders for upcoming court hearings';

    public function handle(): int
    {
        $date = $this->option('date') ?: now()->addDay()->toDateString();

        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
            $this->error('Invalid date. Use YYYY-MM-DD.');
            return self::FAILURE;
        }

        $sessions = DB::table('case_sessions as s')->join('legal_cases as c', 'c.id', '=', 's.case_id')->whereDate('s.scheduled_at', $date)->where('s.status', 'scheduled')->where('c.status', 'active')->select('s.id', 's.case_id', 's.scheduled_at', 'c.title')->orderBy('s.id')->get();

        $clientCount = 0;
        $lawyerCases = [];

        foreach ($sessions as $session) {
            $clients = DB::table('case_client as cc')->join('clients as cl', 'cl.id', '=', 'cc.client_id')->where('cc.case_id', $session->case_id)->select('cl.id', 'cl.name', 'cl.whatsapp_number')->get();

            $names = $clients->pluck('name')->filter()->implode('، ');

            $lawyerCases[] = [
                'title' => $session->title,
                'clients' => $names ?: 'غير محدد',
                'date' => $date,
            ];

            foreach ($clients as $client) {
                $number = trim((string) $client->whatsapp_number);

                if ($number === '') {
                    continue;
                }

                $key = "client:{$session->id}:{$client->id}:{$date}";

                $notification = NotificationLog::query()->firstOrCreate(
                    ['idempotency_key' => $key],
                    [
                        'session_id' => $session->id,
                        'type' => 'client_reminder',
                        'recipient_type' => 'client',
                        'recipient_number' => $number,
                        'scheduled_at' => now()->toDateString(),
                        'status' => 'pending',
                        'attempts' => 0,
                        'message' => json_encode(
                            [
                                'template' => 'client',
                                'parameters' => [$client->name, $date],
                            ],
                            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
                        ),
                    ],
                );

                if ($notification->wasRecentlyCreated) {
                    $clientCount++;
                }
            }
        }

        $lawyerCount = 0;

        $lawyerNumber = trim((string) config('services.lawyer.whatsapp_number', ''));

        if ($lawyerNumber !== '' && count($lawyerCases) > 0) {
            // The approved lawyer template accepts exactly three
            // parameters, so generate one reminder per session.
            foreach ($sessions as $index => $session) {
                $case = $lawyerCases[$index];

                $notification = NotificationLog::query()->firstOrCreate(
                    [
                        'idempotency_key' => "lawyer:{$session->id}:{$date}",
                    ],
                    [
                        'session_id' => $session->id,
                        'type' => 'lawyer_reminder',
                        'recipient_type' => 'lawyer',
                        'recipient_number' => $lawyerNumber,
                        'scheduled_at' => now()->toDateString(),
                        'status' => 'pending',
                        'attempts' => 0,
                        'message' => json_encode(
                            [
                                'template' => 'lawyer',
                                'parameters' => [$case['title'], $case['clients'], $case['date']],
                            ],
                            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
                        ),
                    ],
                );

                if ($notification->wasRecentlyCreated) {
                    $lawyerCount++;
                }
            }
        }

        $this->info("Hearing date: {$date}");
        $this->info("Sessions found: {$sessions->count()}");
        $this->info("Client reminders created: {$clientCount}");
        $this->info("Lawyer reminders created: {$lawyerCount}");

        return self::SUCCESS;
    }
}
