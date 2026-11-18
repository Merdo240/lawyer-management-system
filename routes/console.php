<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('hearings:generate-reminders')->dailyAt('07:55')->withoutOverlapping();

Schedule::command('app:process-pending-notifications')->everyMinute()->withoutOverlapping();

Schedule::command('app:detect-stuck-notifications')->everyFiveMinutes()->withoutOverlapping();
