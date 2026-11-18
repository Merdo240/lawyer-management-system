<?php

namespace App\Console\Commands;

use App\Services\Notifications\StuckNotificationService;
use Illuminate\Console\Command;

class DetectStuckNotifications extends Command
{
    protected $signature = 'app:detect-stuck-notifications';

    protected $description = 'Detect notifications stuck in processing state';

    public function handle(StuckNotificationService $service): int
    {
        $count = $service->detect();

        $this->info("Detected {$count} stuck notification(s).");

        return self::SUCCESS;
    }
}