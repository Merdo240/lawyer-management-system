<?php

namespace Tests\Feature;

use App\Interfaces\NotificationProviderInterface;
use App\Services\Notifications\FakeNotificationProvider;
use Tests\TestCase;

class NotificationProviderTest extends TestCase
{
    public function test_fake_provider_is_registered_in_testing_environment(): void
    {
        $provider = app(NotificationProviderInterface::class);

        $this->assertInstanceOf(
            FakeNotificationProvider::class,
            $provider
        );
    }
}