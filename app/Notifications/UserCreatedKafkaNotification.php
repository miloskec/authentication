<?php

namespace App\Notifications;

use App\Channels\KafkaChannel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class UserCreatedKafkaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable)
    {
        return [KafkaChannel::class];
    }

    public function toKafka(object $notifiable)
    {
        return [
            'username' => $notifiable->username,
            'email' => $notifiable->email,
            'full_name' => $notifiable->full_name,
            'id' => $notifiable->id,
            'is_admin' => $notifiable->is_admin,
        ];
    }
}
