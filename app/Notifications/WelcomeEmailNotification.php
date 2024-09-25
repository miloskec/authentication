<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WelcomeEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;  // Number of attempts

    public $backoff = 9000;  // Delay in seconds (150 minutes)

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Our Application!')
            ->greeting('Hello '.$notifiable->full_name.',')
            ->line('Thank you for registering at our site.')
            ->line('We are glad to have you in our community.')
            ->action('Visit Our Website', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function failed($notifiable, $exception)
    {
        // Handle failure (e.g., log the error, send an alert, etc.)
        Log::channel('authentication')->error('Failed to send email notification.', ['error' => $exception->getMessage()]);
    }
}
