<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\NotificationLog;

#[Signature('app:generate-lawyer-daily-summary {--date= : Session date in Y-m-d format}')]
#[Description('Generate tomorrow sessions summary for the lawyer')]
class GenerateLawyerDailySummary extends Command
{
    public function handle(): int
    {
        $tomorrow = $this->option('date') ? \Illuminate\Support\Carbon::parse($this->option('date')) : today()->addDay();

        $sessions = Session::query()->with('legalCase.clients')->where('status', 'scheduled')->whereDate('scheduled_at', $tomorrow->toDateString())->get();

        $this->info('Sessions scheduled for ' . $tomorrow->toDateString() . ': ' . $sessions->count());

        $message = 'ملخص جلسات يوم ' . $tomorrow->format('Y-m-d') . "\n\n";

        foreach ($sessions as $index => $session) {
            $clientNames = $session->legalCase->clients->pluck('name')->implode('، ');

            $message .= $index + 1 . '- القضية: ' . $session->legalCase->title . "\nالعملاء: " . $clientNames . "\nالتاريخ: " . $session->scheduled_at->format('Y-m-d') . "\nالتفاصيل: " . ($session->details ?? 'لا توجد تفاصيل') . "\n\n";
        }

        $this->line($message);
        $idempotencyKey = 'lawyer_daily_summary:' . $tomorrow->format('Y-m-d');
        $notification = NotificationLog::query()->firstOrCreate(
            [
                'idempotency_key' => $idempotencyKey,
            ],
            [
                'session_id' => null,
                'type' => 'lawyer_daily_summary',
                'recipient_type' => 'lawyer',
                'recipient_number' => config('services.lawyer.whatsapp_number'),
                'message' => $message,
                'scheduled_at' => $tomorrow->copy()->subDay()->toDateString(),
                'status' => 'pending',
            ],
        );

        $this->info($notification->wasRecentlyCreated ? 'Lawyer summary notification created.' : 'Lawyer summary notification already exists.');

        $this->line('Idempotency key: ' . $idempotencyKey);
        return self::SUCCESS;
    }
}
