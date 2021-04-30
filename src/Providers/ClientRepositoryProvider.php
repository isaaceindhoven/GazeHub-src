<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Providers;

use DI\Container;
use ISAAC\GazeHub\Repositories\ClientRepository;
use ISAAC\GazeHub\Repositories\ClientRepositoryInMemory;

class ClientRepositoryProvider implements Provider
{
    public function register(Container &$container): void
    {
        $container->set(
            ClientRepository::class,
            $container->get(ClientRepositoryInMemory::class)
        );
    }
}
