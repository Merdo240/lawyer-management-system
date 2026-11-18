<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:process-notifications {--date= : Date to check in Y-m-d format}')]
#[Description('Find sessions that need notification reminders')]
class ProcessNotifications extends Command
{
    public function handle(): int
    {
        $tomorrow = $this->option('date') ? \Illuminate\Support\Carbon::parse($this->option('date')) : today()->addDay();

        $sessions = Session::query()->with('legalCase.clients')->where('status', 'scheduled')->whereDate('scheduled_at', $tomorrow)->get();

        $this->info('Sessions scheduled for ' . $tomorrow->toDateString() . ': ' . $sessions->count());

        foreach ($sessions as $session) {
            $this->line('Case: ' . $session->legalCase->title . ' | Date: ' . $session->scheduled_at->format('Y-m-d') . ' | Clients: ' . $session->legalCase->clients->count());

            foreach ($session->legalCase->clients as $client) {
                $idempotencyKey = implode(':', ['client_reminder', 'session', $session->id, 'client', $client->id, 'date', $session->scheduled_at->format('Y-m-d')]);

                $alreadyLogged = \App\Models\NotificationLog::query()->where('idempotency_key', $idempotencyKey)->exists();

                if ($alreadyLogged) {
                    $this->line('Client ID: ' . $client->id . ' | Reminder already logged: Yes');

                    continue;
                }

                \App\Models\NotificationLog::create([
                    'session_id' => $session->id,
                    'type' => 'client_reminder',
                    'idempotency_key' => $idempotencyKey,
                    'recipient_type' => 'client',
                    'recipient_number' => $client->whatsapp_number,
                    'scheduled_at' => $session->scheduled_at->copy()->subDay()->toDateString(),
                    'status' => 'pending',
                ]);

                $this->info('Client ID: ' . $client->id . ' | Reminder log created: Yes');
            }
        }

        return self::SUCCESS;
    }
}
