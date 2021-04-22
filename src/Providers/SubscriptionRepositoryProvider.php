<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Providers;

use DI\Container;
use ISAAC\GazeHub\Repositories\SubscriptionRepository;
use ISAAC\GazeHub\Repositories\SubscriptionRepositoryInMemory;

class SubscriptionRepositoryProvider implements Provider
{
    public function register(Container &$container): void
    {
        $container->set(
            SubscriptionRepository::class,
            $container->get(SubscriptionRepositoryInMemory::class)
        );
    }
}
