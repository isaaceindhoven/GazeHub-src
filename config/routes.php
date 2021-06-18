<?php

declare(strict_types=1);

use ISAAC\GazeHub\Controllers\AuthController;
use ISAAC\GazeHub\Controllers\DebugController;
use ISAAC\GazeHub\Controllers\EventController;
use ISAAC\GazeHub\Controllers\SSEController;
use ISAAC\GazeHub\Controllers\SubscriptionController;

return [
    'GET' => [
        '/sse' => [SSEController::class, 'handle'],
        '/debug' => [DebugController::class, 'handle'],
        '/debug/clients' => [DebugController::class, 'clients'],
    ],
    'POST' => [
        '/event' => [EventController::class, 'handle'],
        '/subscription' => [SubscriptionController::class, 'create'],
        '/auth' => [AuthController::class, 'authenticate'],
    ],
    'DELETE' => [
        '/subscription' => [SubscriptionController::class, 'destroy'],
        '/auth' => [AuthController::class, 'unauthenticate'],
    ],
];
