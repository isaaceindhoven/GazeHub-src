<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Providers;

use DI\Container;
use ISAAC\GazeHub\Repositories\ConfigRepositoryFilesystem;
use ISAAC\GazeHub\Repositories\IConfigRepository;

class ConfigRepositoryProvider implements IProvider
{
    public function register(Container &$container): void
    {
        $container->set(
            IConfigRepository::class,
            new ConfigRepositoryFilesystem(__DIR__ . '/../../config/config.php')
        );
    }
}
