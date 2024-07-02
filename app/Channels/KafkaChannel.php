<?php

namespace App\Channels;

use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class KafkaChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toKafka($notifiable);         

        $message = new Message(
            headers: ['header-key' => 'header-value'],
            body: ['user'=> json_encode($message)],
            key:  (string) $message['id']
        );
        $producer = Kafka::publish(config('brokers'))->onTopic(config('kafka.topics.user_created.topic'))->withMessage($message);
        $producer->send();
    }
}
