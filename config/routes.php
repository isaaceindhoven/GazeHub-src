<?php

declare(strict_types=1);

use ISAAC\GazeHub\Controllers\EventController;
use ISAAC\GazeHub\Controllers\SSEController;
use ISAAC\GazeHub\Controllers\SubscriptionController;

return [
    'GET' => [
        '/sse' => [SSEController::class, 'handle'],
        '/ping' => [SubscriptionController::class, 'ping'],
    ],
    'POST' => [
        '/event' => [EventController::class, 'handle'],
        '/subscription' => [SubscriptionController::class, 'create'],
    ],
    'DELETE' => [
        '/subscription' => [SubscriptionController::class, 'destroy'],
    ],
];
