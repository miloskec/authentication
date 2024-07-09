<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class KafkaChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toKafka($notifiable);

        $message = new Message(
            headers: ['header-key' => 'header-value'],
            body: ['user' => json_encode($message)],
            key: (string) $message['id']
        );
        $producer = Kafka::publish(config('brokers'))->onTopic(config('kafka.topics.user_created.topic'))->withMessage($message);
        $producer->send();
    }
}
