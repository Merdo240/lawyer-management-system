<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\NotificationProviderInterface;
use App\Services\Notifications\FakeNotificationProvider;
use App\Services\Notifications\DisabledNotificationProvider;
use App\Services\Notifications\WhatsAppNotificationProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register(): void
    {
        $this->app->bind(NotificationProviderInterface::class, function () {
            if ($this->app->environment('testing')) {
                return new FakeNotificationProvider();
            }

            if (config('services.notifications.provider_enabled', false)) {
                return new WhatsAppNotificationProvider();
            }

            return new DisabledNotificationProvider();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
