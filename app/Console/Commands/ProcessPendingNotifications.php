<?php namespace App\Console\Commands;

use App\Models\NotificationLog;
use App\Services\Notifications\HearingReminderValidator;
use App\Services\Notifications\NotificationClaimer;
use App\Services\Notifications\NotificationProviderReadiness;
use App\Services\Notifications\NotificationSender;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class ProcessPendingNotifications extends Command
{
    protected $signature = 'app:process-pending-notifications
                            {--date= : Processing date in Y-m-d format}';

    protected $description = 'Process pending notification logs';

    public function handle(NotificationClaimer $claimer, NotificationSender $sender, NotificationProviderReadiness $readiness, HearingReminderValidator $validator): int
    {
        if (!$readiness->isReady()) {
            $this->warn('Notification provider is not ready. Processing skipped.');

            return self::SUCCESS;
        }

        $dateOption = $this->option('date');

        if ($dateOption !== null) {
            $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateOption);

            if (!$parsed || $parsed->format('Y-m-d') !== $dateOption) {
                $this->error('Invalid date. Expected YYYY-MM-DD.');

                return self::FAILURE;
            }
        }

        $processingDate = $dateOption ? CarbonImmutable::parse($dateOption)->toDateString() : today()->toDateString();

        $maxAttempts = max(1, (int) config('services.notifications.max_attempts', 3));

        $exhaustedCount = NotificationLog::query()
            ->where('status', 'pending')
            ->whereIn('type', ['client_reminder', 'lawyer_reminder'])
            ->where('attempts', '>=', $maxAttempts)
            ->update(['status' => 'exhausted']);

        $this->info('Exhausted notifications updated: ' . $exhaustedCount);

        $processed = 0;
        $invalidated = 0;

        NotificationLog::query()
            ->where('status', 'pending')
            ->where('attempts', '<', $maxAttempts)
            ->whereIn('type', ['client_reminder', 'lawyer_reminder'])
            ->whereNotNull('message')
            ->whereDate('scheduled_at', '<=', $processingDate)
            ->orderBy('id')
            ->chunkById(100, function ($notifications) use ($claimer, $sender, $validator, &$processed, &$invalidated) {
                foreach ($notifications as $notification) {
                    if (!$claimer->claim($notification->id)) {
                        continue;
                    }

                    $claimed = $notification->fresh();

                    if (!$validator->validate($claimed)) {
                        $invalidated++;
                        $this->warn('Notification ID: ' . $notification->id . ' | Invalidated');

                        continue;
                    }

                    $sender->send($claimed->fresh());

                    $updated = $notification->fresh();

                    $this->line('Notification ID: ' . $notification->id . ' | Status: ' . $updated->status);

                    $processed++;
                }
            });

        $this->info('Processed notifications: ' . $processed);
        $this->info('Invalidated notifications: ' . $invalidated);

        return self::SUCCESS;
    }
}
