<?php

declare(strict_types=1);

use ISAAC\GazeHub\Providers\ClientRepositoryProvider;
use ISAAC\GazeHub\Providers\ConfigRepositoryProvider;
use ISAAC\GazeHub\Providers\LoggerProvider;
use ISAAC\GazeHub\Providers\SubscriptionRepositoryProvider;
use ISAAC\GazeHub\Providers\TokenDecoderProvider;

return [
    ConfigRepositoryProvider::class,
    LoggerProvider::class,
    ClientRepositoryProvider::class,
    SubscriptionRepositoryProvider::class,
    TokenDecoderProvider::class,
];
