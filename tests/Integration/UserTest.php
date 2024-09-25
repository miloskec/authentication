<?php

namespace Tests\Integration;

use App\Notifications\UserCreatedKafkaNotification;
use App\Notifications\WelcomeEmailNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_registration_notification_success(): void
    {
        $user = $this->register();
        // Welcome email is sent
        Notification::assertSentTo(
            $user,
            WelcomeEmailNotification::class
        );
        // Kafka notification is sent
        Notification::assertSentTo(
            $user,
            UserCreatedKafkaNotification::class
        );
    }
}
