<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Providers;

use DI\Container;
use ISAAC\GazeHub\Repositories\ISubscriptionRepository;
use ISAAC\GazeHub\Repositories\SubscriptionRepositoryInMemory;

class SubscriptionRepositoryProvider implements IProvider
{
    public function register(Container &$container): void
    {
        $container->set(
            ISubscriptionRepository::class,
            $container->get(SubscriptionRepositoryInMemory::class)
        );
    }
}
