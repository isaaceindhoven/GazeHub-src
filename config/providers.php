<?php

/**
 *   Do not remove or alter the notices in this preamble.
 *   This software code regards ISAAC Standard Software.
 *   Copyright © 2021 ISAAC and/or its affiliates.
 *   www.isaac.nl All rights reserved. License grant and user rights and obligations
 *   according to applicable license agreement. Please contact sales@isaac.nl for
 *   questions regarding license and user rights.
 */

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
