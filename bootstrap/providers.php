<?php

return [
    App\Providers\AppServiceProvider::class,
    Junges\Kafka\Providers\LaravelKafkaServiceProvider::class,
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    App\Providers\ActivityServiceProvider::class,
];
